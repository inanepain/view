<?php

/**
 * Inane: View
 *
 * TemplateRenderer
 *
 * PHP Version 8.1
 *
 * @author Philip Michael Raab <philip@inane.co.za>
 * @package Inane\View
 *
 * @license UNLICENSE
 * @license https://github.com/inanepain/view/raw/develop/UNLICENSE UNLICENSE
 */

declare(strict_types=1);

namespace Inane\View;

use Exception;

use function array_keys;
use function file_exists;
use function func_get_args;
use function is_array;
use function ob_get_clean;
use function ob_start;
use function preg_replace;

/**
 * TemplateRenderer
 *
 * Example:
 *   echo TemplateRenderer::withString('{$title}: <a href="{$url}" target="{$target}">{$title}</a>', ['title' => 'Inane', 'url' => 'https://inane.co.za', 'target'=>'_blank']);
 *
 * @deprecated 1.0.0
 * @package Utilities
 * @version 1.0.0
 */
class TemplateRenderer {
    /**
     * partial
     *
     * Renders a template with variables
     *
     * @internal param $template - a partial file that gets executed
     * @internal param $variables - passed to the templates at execution
     *
     * @return string rendered template
     *
     * @throws Exception
     */
    public static function partial(/*$template, $variables*/) {
        // ensure the file exists
        if (!file_exists(func_get_args()[0])) {
            throw new Exception('Template File: ' . func_get_args()[0] . '<br/>Not Found!');
        }

        // Make values in the associative array easier to access by extracting them
        if (is_array(func_get_args()[1]))
            foreach (func_get_args()[1] as $key => $value) ${$key} = $value;

        ob_start();
        include func_get_args()[0];
        return ob_get_clean();
    }

    /**
     * withString
     *
     * Renders a string template with variables
     *
     * Template:
     *   <a href="{$url}">{$title}</a>
     *
     * Variables:
     *   ['title' => 'Inane', 'url' => 'https://inane.co.za']
     *
     * Result:
     *   <a href="https://inane.co.za">Inane</a>
     *
     * @param string $template - a string template
     * @param array $variables - to be passed into template
     *
     * @return string the rendered template
     */
    public static function withString(string $template, array $variables = []): string {
        $fields = array_keys($variables);
        foreach ($fields as $field) {
            $replace = $variables[$field];
            $pattern = '/\{\$' . $field . '\}/';
            $template = preg_replace($pattern, static::preg_escape_back($replace), $template);
        }
        return $template;
    }

    /**
     * Escapes $ and \
     *
     * @param mixed $string
     *
     * @return string|string[]|null
     */
    protected static function preg_escape_back($string) {
        // Replace $ with \$ and \ with \\
        $string = preg_replace('#(?<!\\\\)(\\$|\\\\)#', '\\\\$1', $string);
        return $string;
    }
}
