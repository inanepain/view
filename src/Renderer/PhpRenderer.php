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

use Inane\View\Exception\RuntimeException;
use ReflectionClass;

use function array_merge;
use function array_shift;
use function count;
use function extract;
use function file_exists;
use function glob;
use function implode;
use function is_array;
use function is_null;
use function is_readable;
use function ob_end_clean;
use function ob_end_flush;
use function ob_get_clean;
use function ob_get_level;
use function ob_start;

use const EXTR_SKIP;
use const GLOB_BRACE;
use const GLOB_NOSORT;

/**
 * PhpRenderer
 *
 * Renders php files.
 * Typically, with the .phtml extension.
 *
 * @version 0.2.0
 */
class PhpRenderer implements RendererInterface {
    use PartialRendererTrait;
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
     * Resolve template against template stack
     *
     * @param string $template name
     *
     * @return string template file path
     *
     * @throws \Inane\View\Exception\RuntimeException Unable to resolve template
     */
    protected function resolve(string $template): string {
        $paths = '{' . implode(',', $this->getTemplateStack()) . '}';
        $extensions = '{' . implode(',', $this->getTemplateExtensions()) . '}';

        $files = glob("$paths{/,/**/}$template$extensions", GLOB_NOSORT | GLOB_BRACE);

        if (count($files) > 0) return array_shift($files);

        throw new RuntimeException("Error: Unable to resolve template: `$template`");
    }

    /**
     * Renders a template, binding the renderer unless an object is supplied.
     *
     * @param string $template Template name.
     * @param array<string, mixed> $data Arbitrary template variables.
     * @param object|null $thisObject Optional template context.
     *
     * @return string Rendered template.
     *
     * @throws RuntimeException If the template cannot be resolved or read.
     * @throws \Throwable If template execution fails.
     */
    public function render(string $template, array $data = [], ?object $thisObject = null): string {
        $file = $this->resolve($template);

        return static::renderTemplate($file, $data, $thisObject ?? $this);
    }

    /**
     * Executes a template file with isolated, exception-safe output buffering.
     *
     * @param string $file Template file path.
     * @param array<string, mixed> $data Arbitrary template variables.
     * @param object|null $thisObject Optional template context.
     *
     * @return string Rendered template.
     *
     * @throws RuntimeException If the file cannot be read or binding fails.
     * @throws \Throwable If template execution fails.
     */
    public static function renderTemplate(string $file, array $data = [], ?object $thisObject = null): string {
        if (!file_exists($file)) throw new RuntimeException('Invalid => ' . $file . ': not found');
        if (!is_readable($file)) throw new RuntimeException('Invalid => ' . $file . ': not readable');

        $parseTemplate = function (string $templateFile, array $variables): string {
            $bufferLevel = ob_get_level();
            ob_start();
            try {
                extract($variables, EXTR_SKIP);
                include $templateFile;
                while (ob_get_level() > $bufferLevel + 1) ob_end_flush();

                return (string)ob_get_clean();
            } finally {
                while (ob_get_level() > $bufferLevel) ob_end_clean();
            }
        };

        if (!is_null($thisObject)) {
            $scope = new ReflectionClass($thisObject)->isInternal() ? null : $thisObject;
            $parseTemplate = $parseTemplate->bindTo($thisObject, $scope);
        }
        if ($parseTemplate === null) throw new RuntimeException('Unable to bind the template object.');

        return $parseTemplate($file, $data);
    }
}
