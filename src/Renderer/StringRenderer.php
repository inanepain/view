<?php

/**
 * Inane: View
 *
 * RenderEngine
 *
 * PHP Version 8.1
 *
 * @author Philip Michael Raab <philip@inane.co.za>
 * @package Inane\View
 *
 * @license UNLICENSE
 * @license https://github.com/inanepain/view/raw/develop/UNLICENSE UNLICENSE
 *
 * @version $Id$
 * $Date$
 */

declare(strict_types=1);

namespace Inane\View\Renderer;

use Inane\Stdlib\Options;
use Inane\View\Exception\RuntimeException;

use function array_keys;
use function preg_replace;
use const false;

/**
 * StringRenderer
 *
 * Renders strings.
 *
 * @package Inane\View
 *
 * @version 0.1.0
 */
class StringRenderer implements RendererInterface {
    /**
     * Templates stored by name
     *
     * @var \Inane\Stdlib\Options
     */
    protected Options $templateStack;

    /**
     * StringRenderer Constructor
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
     * Render assigning $object to $this
     *
     * template:
     *   <a href="{$url}">{$title}</a>
     *
     * data:
     *   ['title' => 'Inane', 'url' => 'https://inane.co.za']
     *
     * output:
     *   <a href="https://inane.co.za">Inane</a>
     *
     * @param string    $template name of template file
     * @param array     $data     array of variables to be made available in template
     * @param bool      $useStack $template is the name of a template on the stack
     *
     * @return string   rendered template
     *
     * @throws \Inane\View\Exception\RuntimeException Template not found
     */
    public function render(string $template, array $data = [], bool $useStack = false): string {
        if ($useStack)
            $template = $this->templateStack->get($template);

        if (!$template) throw new RuntimeException("Error: Template invalid: `$template`");

        foreach (array_keys($data) as $field) {
            $replace = $data[$field];
            $pattern = '/\{\$' . $field . '\}/';
            $template = preg_replace($pattern, static::pregEscapeBack($replace), $template);
        }

        return $template;
    }
}
