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
 * @author   Philip Michael Raab<philip@cathedral.co.za>
 * @package  inanepain\view
 * @category view
 *
 * @license  UNLICENSE
 * @license  https://unlicense.org/UNLICENSE UNLICENSE
 *
 * _version_ $version
 */

declare(strict_types = 1);

namespace Inane\View;

use Inane\Stdlib\Exception\RuntimeException;

use function explode;
use function file_exists;
use function str_contains;

/**
 * Resolves logical template names to concrete file paths.
 */
class TemplateResolver {
    //#region Properties
    /**
     * Map of a template type => base path.
     *
     * @var array<string, string>
     */
    private static array $templatePaths = [
        'layouts'    => 'layouts',
        'partials'   => 'partials',
        'components' => 'components',
        'pages'      => 'pages',
        'errors'     => 'errors',
    ];
    /**
     * Resolved template cache.
     *
     * @var array<string, string>
     */
    private static array $cache = [];
    private static string $basePath = 'views';

    private static string $extension = '.phtml';
    //#endregion Properties

    /**
     * Resolves the given template to its full file path based on predefined template paths.
     *
     * @param string $template The template identifier, optionally including a type prefix (e.g., "type:name").
     *
     * @return string The resolved full path to the template file.
     *
     * @throws RuntimeException If the template file does not exist at the resolved path.
     */
    public static function resolve(string $template): string {
        if (isset(self::$cache[$template])) {
            return self::$cache[$template];
        }

        $pathComponents = [];
        if (!empty(static::$basePath)) {
            $pathComponents[] = static::$basePath;
        }

        // Check if the template has an explicit path type
        if (str_contains($template, ':')) {
            [
                $type,
                $name,
            ] = explode(':', $template, 2);
            $pathComponents[] = self::$templatePaths[$type] . '/' . $name . static::$extension;
        } else {
            // Default to pages
            $pathComponents[] = self::$templatePaths['pages'] . '/' . $template . static::$extension;
        }

        $path = implode('/', $pathComponents);

        if (!file_exists($path)) {
            throw new RuntimeException("Template not found: {$path}");
        }

        self::$cache[$template] = $path;

        return $path;
    }

    /**
     * Adds or overrides a template base path for a given type.
     */
    public static function addPath(string $type, string $path): void {
        self::$templatePaths[$type] = $path;
    }
}
