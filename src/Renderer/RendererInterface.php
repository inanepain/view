<?php

/**
 * Inane: View
 *
 * View layer with models for most common of content types.
 *
 * $Id$
 * $Date$
 *
 * PHP version 8.4
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

/**
 * Renderer Interface
 *
 * @version 0.2.0
 */
interface RendererInterface {
    /**
     * Render template
     *
     * @param string      $template   template
     * @param array       $data       variables made available in template
     *
     * @return string rendered template
     */
    public function render(string $template, array $data = []): string;

    /**
     * Core render function
     *
     * @since 0.2.0
     *
     * @param string    $template   the template for renderer, could be a file path or string,...
     * @param array     $data       data to populate template with
     *
     * @return string rendered string
     */
    public static function renderTemplate(string $template, array $data = []): string;
}
