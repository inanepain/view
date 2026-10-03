<?php

/**
 * PartialRendererTrait
 *
 * Inane Library
 *
 * $Id$
 * $Date$
 *
 * PHP version 8.5
 *
 * @author   Philip Michael Raab <philip@cathedral.co.za>
 * @package  inanepain\view
 * @category view
 *
 * @license  UNLICENSE
 * @license  https://unlicense.org/UNLICENSE UNLICENSE
 *
 * _version_ $version
 */

declare(strict_types=1);

namespace Inane\View\Renderer;

use Inane\View\Exception\RuntimeException;

/**
 * Renders partial templates and collections of variable sets.
 */
trait PartialRendererTrait {
    /**
     * Renders a named partial with its own variables.
     *
     * @param string $template Template name.
     * @param array<string, mixed> $data Arbitrary template variables.
     *
     * @return string Rendered partial.
     *
     * @throws RuntimeException If the template cannot be rendered.
     */
    public function renderPartial(string $template, array $data = []): string {
        return $this->render($template, $data);
    }

    /**
     * Concatenates partials, with item variables overriding shared variables.
     *
     * @param string $template Template name.
     * @param iterable<array<string, mixed>> $items Variable sets containing arbitrary template values.
     * @param array<string, mixed> $data Shared template variables.
     *
     * @return string Rendered collection, or an empty string for no items.
     *
     * @throws RuntimeException If an item is not an array or rendering fails.
     */
    public function renderEach(string $template, iterable $items, array $data = []): string {
        $output = '';
        foreach ($items as $item) {
            if (!is_array($item)) throw new RuntimeException('View items must be arrays of template variables.');
            $output .= $this->renderPartial($template, array_merge($data, $item));
        }

        return $output;
    }
}
