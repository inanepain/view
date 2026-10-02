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

namespace Inane\View\Renderer;

use Inane\Stdlib\Options;
use Inane\View\Exception\RuntimeException;

use function is_scalar;
use function preg_replace;
use function strtr;

use const false;

/**
 * StringRenderer
 *
 * Renders strings.
 *
 * @version 0.2.0
 */
class StringRenderer implements RendererInterface {
    use PartialRendererTrait;
    /**
     * Templates stored by name
     *
     * @var \Inane\Stdlib\Options
     */
    protected Options $templateStack;

    /**
     * StringRenderer Constructor
     *
     * @param array $templateStack templates to initialise renderer with
     *
     * @return void
     */
    public function __construct(array $templateStack = []) {
        $this->setTemplateStack($templateStack);
    }

    /**
     * Escapes $ and \
     *
     * @param string $string
     *
     * @return string
     */
    protected static function pregEscapeBack(string $string): string {
        $string = preg_replace('#(?<!\\\\)(\\$|\\\\)#', '\\\\$1', $string);

        return $string;
    }

    /**
     * GET: Template Stack
     *
     * @return array
     */
    public function getTemplateStack(): array {
        return $this->templateStack->toArray();
    }

    /**
     * SET: Template Stack
     *
     * @param array $templateStack
     *
     * @return \Inane\View\Renderer\StringRenderer
     */
    public function setTemplateStack(array $templateStack): self {
        $this->templateStack = new Options($templateStack);

        return $this;
    }

    /**
     * Add to Template Stack
     *
     * @param string|array $templateStack
     *
     * @return \Inane\View\Renderer\StringRenderer
     */
    public function addTemplate(string $name, string $template): self {
        return $this->addTemplates([$name => $template]);
    }

    /**
     * Add templates to Template Stack
     *
     * @param array $templates array with templates by name
     *
     * @return \Inane\View\Renderer\StringRenderer
     */
    public function addTemplates(array $templates): self {
        $this->templateStack->merge($templates);

        return $this;
    }

    /**
     * Renders a literal string or a named template from the stack.
     *
     * @param string $template Template string or name.
     * @param array<string, mixed> $data Arbitrary scalar or stringable template variables.
     * @param bool $useStack Whether to resolve the template name.
     *
     * @return string Rendered template.
     *
     * @throws RuntimeException If the template or a variable is invalid.
     */
    public function render(string $template, array $data = [], bool $useStack = false): string {
        if ($useStack) {
            if (!$this->templateStack->has($template)) throw new RuntimeException("Error: Template not found: `$template`");
            $template = $this->templateStack->get($template);
            if (!is_string($template)) throw new RuntimeException('String templates must be strings.');
        }

        return static::renderTemplate($template, $data);
    }

    /**
     * Renders a named string partial.
     *
     * @param string $template Template name on the stack.
     * @param array<string, mixed> $data Arbitrary scalar or stringable template variables.
     *
     * @return string Rendered partial.
     *
     * @throws RuntimeException If the template or a variable is invalid.
     */
    public function renderPartial(string $template, array $data = []): string {
        return $this->render($template, $data, true);
    }

    /**
     * Replaces placeholders literally, without reprocessing inserted output.
     *
     * @param string $template Template string.
     * @param array<string, mixed> $data Arbitrary scalar or stringable template variables.
     *
     * @return string Rendered template.
     *
     * @throws RuntimeException If the template or a variable is invalid.
     */
    public static function renderTemplate(string $template, array $data = []): string {
        if ($template === '') throw new RuntimeException("Error: Template invalid: `$template`");

        $replacements = [];
        foreach ($data as $field => $value) {
            if ($value !== null && !is_scalar($value) && !$value instanceof \Stringable)
                throw new RuntimeException('String template variables must be scalar, null or stringable.');
            $replacements['{$' . $field . '}'] = (string)$value;
        }

        return strtr($template, $replacements);
    }
}
