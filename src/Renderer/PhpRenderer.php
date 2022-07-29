<?php

/**
 * Inane: View
 *
 * Renderers
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

use Inane\View\Exception\RuntimeException;

use function array_merge;
use function is_array;

/**
 * PhpRenderer
 *
 * Renders php files.
 * Typically with the .phtml extension.
 *
 * @package Inane\View
 *
 * @version 0.1.0
 */
class PhpRenderer implements RendererInterface {
    /**
     * Directories used to find a match when requesting a template
     *
     * Using a stack and glob we simplify the name used when rendering
     *  to only the file name.
     */
    protected array $templateStack;

    /**
     * Template file extensions
     *
     * With extensions predefined the template name is neater.
     */
    protected array $templateExtensions = ['.phtml'];

    /**
     * PhpRenderer Constructor
     *
     * @return void
     */
    public function __construct(string|array $templateStack) {
        $this->setTemplateStack($templateStack);
    }

    /**
     * GET: Template Stack
     *
     * @return array
     */
    public function getTemplateStack(): array {
        return $this->templateStack;
    }

    /**
     * SET: Template Stack
     *
     * @param string|array $templateStack
     *
     * @return \Inane\View\Renderer\PhpRenderer
     */
    public function setTemplateStack(string|array $templateStack): self {
        if (!is_array($templateStack)) $templateStack = [$templateStack];

        $this->templateStack = $templateStack;

        return $this;
    }

    /**
     * Add to Template Stack
     *
     * @param string|array $templateStack
     *
     * @return \Inane\View\Renderer\PhpRenderer
     */
    public function addTemplateStack(string|array $templateStack): self {
        if (!is_array($templateStack)) $templateStack = [$templateStack];

        $this->templateStack = array_merge($this->templateStack, $templateStack);

        return $this;
    }

    /**
     * GET: Template Extensions
     *
     * @return array
     */
    public function getTemplateExtensions(): array {
        return $this->templateExtensions;
    }

    /**
     * SET: Template Extensions
     *
     * @param array $templateExtensions
     *
     * @return \Inane\View\Renderer\PhpRenderer
     */
    public function setTemplateExtensions(array $templateExtensions): self {
        $this->templateExtensions = $templateExtensions;
        return $this;
    }

    /**
     * Render assigning $object to $this
     *
     * @param string    $template   name of template file
     * @param array     $data       array of variables to be made available in template
     * @param object    $object     optional $this object
     *
     * @return string   rendered template
     *
     * @throws \Inane\View\Exception\RuntimeException Template not found
     */
    public function render(string $template, array $data = [], ?object $thisObject = null): string {
        $paths = '{' . implode(',', $this->getTemplateStack()) . '}';
        $extensions = '{' . implode(',', $this->getTemplateExtensions()) . '}';

        $files = glob("$paths{/,/**/}$template$extensions", GLOB_NOSORT | GLOB_BRACE);

        if (count($files) > 0) $template = array_shift($files);
        else throw new RuntimeException("Error: Template not found: `$template`");

        $parseTemplate = function ($template, $data) {
            ob_start();
            extract($data);
            include $template;
            return ob_get_clean();
        };

        if (!\is_null($thisObject)) $parseTemplate = $parseTemplate->bindTo($thisObject, $thisObject);

        return $parseTemplate($template, $data);
    }
}
