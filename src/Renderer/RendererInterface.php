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

/**
 * Renderer Interface
 *
 * @package Inane\View
 *
 * @version 0.1.0
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
}
