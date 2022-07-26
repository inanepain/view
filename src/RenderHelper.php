<?php

/**
 * Inane: View
 *
 * RenderHelper
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

namespace Inane\View;

use function array_key_exists;
use function array_merge_recursive;
use function array_shift;
use function count;
use function glob;
use function implode;
use function is_null;
use function str_starts_with;

/**
 * RenderHelper
 *
 * @version 1.0.0
 * @deprecated 1.0.0
 */
class RenderHelper {
    /**
     * Default file extension for templates
     */
    protected string $ext = '.phtml';

    /**
     * Directories used to find a match when requesting a template
     *
     * Using a stack and glob we simplify the name used when rendering.
     * Only enough of the name needed to match.
     */
    protected array $template_stack = [];

    /**
     * Get the default template file extension
     *
     * @return string
     */
    public function getExt(): string {
        return $this->ext;
    }

    /**
     * Set the default template file extension
     *
     * @param string $ext
     *
     * @return self
     */
    public function setExt(string $ext): self {
        if (!str_starts_with($ext, '.')) $ext = ".{$ext}";

        $this->ext = $ext;

        return $this;
    }

    /**
     * Get the value of template_stack
     *
     * @return array
     */
    public function getTemplateStack(): array {
        return $this->template_stack;
    }

    /**
     * Set the template_stack
     *
     * @param array $template_stack
     *
     * @return self
     */
    public function setTemplateStack(array $template_stack): self {
        $this->template_stack = $template_stack;

        return $this;
    }

    /**
     * add to template_stack
     *
     * @param array $template_stack
     *
     * @return self
     */
    public function addTemplateStack(array $template_stack): self {
        $this->template_stack = array_merge_recursive($this->template_stack, $template_stack);

        return $this;
    }

    /**
     * RenderHelper
     */
    public function __construct(?string $ext = null, ?array $template_stack = null) {
        if (!is_null($ext)) $this->setExt($ext);
        if (!is_null($template_stack)) $this->addTemplateStack($template_stack);
    }

    /**
     * Calls render
     *
     * @return string
     */
    public function __invoke(): string {
        return '';
    }

    /**
     * factory method to create a RenderHelper from an array
     *
     * @param array $array
     * @return self
     */
    public static function fromArray(array $array = []): self {
        $options = [
            'ext' => '.phtml',
            'template_stack' => [],
        ];

        foreach ($options as $key => $val) {
            if (array_key_exists($key, $array)) $options[$key] = $array[$key];
        }

        return new self(...$options);
    }

    public function partial(string $template, array $data = []): string {
        $paths = '{' . implode(',', $this->getTemplateStack()) . '}';

        $files = glob("{$paths}{/,/**/}{$template}{$this->getExt()}", GLOB_NOSORT | GLOB_BRACE);

        if (count($files) > 0) $template = array_shift($files);

        return RenderEngine::partial($template, $data);
    }

    /**
     * Render assigning $object to $this
     *
     * @param object $object
     * @param string $template
     * @param array $data
     * @return string
     */
    public function render(object $object, string $template, array $data = []): string {
        $paths = '{' . implode(',', $this->getTemplateStack()) . '}';
        dd($paths, 'tpl Stack');
        $files = glob("{$paths}{/,/**/}{$template}{$this->getExt()}", GLOB_NOSORT | GLOB_BRACE);

        dd($files, 'Found Templates');

        if (count($files) > 0) $template = array_shift($files);

        return RenderEngine::renderWithThis($object, $template, $data);
    }
}
