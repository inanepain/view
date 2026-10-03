<?php

/**
 * Inane: View
 *
 * View layer with models for the most common content types.
 *
 * $Id$
 * $Date$
 *
 * PHP version 8.5
 *
 * @author Philip Michael Raab<philip@cathedral.co.za>
 * @package inanepain\view
 * @category view
 *
 * @license UNLICENSE
 * @license https://unlicense.org/UNLICENSE UNLICENSE
 *
 * _version_ $version
 */

declare(strict_types=1);

namespace Inane\View\Model;

use Inane\Stdlib\Array\OptionsInterface;
use Inane\Stdlib\Options;
use Inane\View\Exception\RuntimeException;
use Inane\View\Renderer\PhpRenderer;
use Psr\Container\ContainerExceptionInterface;

use function array_key_exists;
use function array_merge;
use function htmlspecialchars;

use const ENT_QUOTES;
use const ENT_SUBSTITUTE;

/**
 * Class HttpModel
 *
 * Model with renders items to HTML.
 */
class HttpModel extends AbstractModel {
    private bool $rendering = false;
    private array $renderedChildren = [];
    #region Option Properties
    /**
     * The template string used for rendering views.
     *
     * @var string
     * @access protected (set)
     */
    protected(set) string $template = '';


    /**
     * Represents the child options for the model.
     *
     * @var OptionsInterface
     */
    protected OptionsInterface $children {
        get => isset($this->children) ? $this->children : ($this->children = new Options());
        set => $this->children = $value;
    }
    #endregion Option Properties

    /**
     * get subview
     *
     * @param string $childName subview name
     *
     * @return string Rendered child output.
     *
     * @throws RuntimeException If the child is unavailable outside rendering.
     * @noinspection MagicMethodsValidityInspection
     */
    public function __get(string $childName): string {
        if (!array_key_exists($childName, $this->renderedChildren))
            throw new RuntimeException("Rendered child unavailable: `$childName`");

        return $this->renderedChildren[$childName];
    }

    /**
     * The class responsible for rendering views.
     *
     * @var string
     * @access protected (set)
     */
    protected(set) string $renderer = PhpRenderer::class;

    /**
     * Adds a child HttpModel to the current model with the specified name.
     *
     * @param string $name The name to associate with the child model.
     * @param HttpModel $model The child HttpModel instance to add.
     *
     * @return self Returns the current instance for method chaining.
     *
     * @throws ContainerExceptionInterface If the child cannot be stored.
     */
    public function addChild(string $name, HttpModel $model): self {
        $this->children->offsetSet($name, $model);

        return $this;
    }

    /**
     * Renders children before the parent, without changing model variables.
     *
     * @param PhpRenderer $renderer Renderer used throughout the tree.
     * @param array<string, mixed> $data Overrides containing arbitrary template variables.
     *
     * @return string Rendered model.
     *
     * @throws RuntimeException If a circular tree or invalid template is encountered.
     * @throws \Throwable If template execution fails.
     */
    public function render(PhpRenderer $renderer, array $data = []): string {
        if ($this->rendering) throw new RuntimeException('Circular view model tree.');

        $this->rendering = true;
        try {
            $context = clone($this, [
                'renderedChildren' => [],
                'variables'        => array_merge($this->variables, $data)
            ]);
            foreach ($this->children as $name => $child)
                $context->renderedChildren[$name] = $child->render($renderer, array_merge($context->variables, $child->variables));

            $context->variables = array_merge($context->variables, $context->renderedChildren);

            return $renderer->render($this->template, $context->variables, $context);
        } finally {
            $this->rendering = false;
        }
    }

    #region Helpers

    /**
     * Escapes a string for safe output in HTML.
     *
     * @param mixed $string The input to be escaped. It will be cast to a string if not already one.
     *
     * @return string The escaped string, safe for inclusion in HTML.
     *
     * @throws \Exception If encoding is unsupported by htmlspecialchars.
     */
    protected function escape(mixed $string): string {
        return htmlspecialchars((string)$string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
    #endregion Helpers
}
