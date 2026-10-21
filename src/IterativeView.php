<?php

/**
 * Inane: IterativeView
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

namespace Inane\View;

use Inane\View\Exception\RuntimeException;
use Inane\View\Model\HtmlModel;
use Inane\View\Renderer\PhpRenderer;

use function array_merge;
use function is_array;

/**
 * Repeats an HTTP template for each variable set.
 */
class IterativeView extends HtmlView {
    private readonly array $items;

    /**
     * Materialises iterable items so the model can be rendered repeatedly.
     *
     * @param iterable<array<string, mixed>> $items Rows containing arbitrary template variables.
     * @param PhpRenderer $renderer Configured template renderer.
     * @param HtmlModel $model HTML model rendered for each item.
     * @param iterable<array<string, mixed>> $items Variable sets.
     *
     * @throws RuntimeException If an item is not a variable array.
     * @throws \TypeError If an option has an incompatible value.
     */
    public function __construct(PhpRenderer $renderer, HtmlModel $model, iterable $items = []) {
        $rows = [];
        foreach ($items as $item) {
            if (!is_array($item)) throw new RuntimeException('View items must be arrayed of template variables.');
            $rows[] = $item;
        }
        $this->items = $rows;
        parent::__construct($renderer, $model);
    }

    /**
     * Concatenates rows, with item variables overriding shared variables.
     *
     * @param array<string, mixed> $data Shared overrides containing arbitrary template variables.
     *
     * @return string Rendered rows, or an empty string for no items.
     *
     * @throws RuntimeException If a circular tree or invalid template is encountered.
     * @throws \Throwable If template execution fails.
     */
    public function render(): string {
        return $this->renderWithData($this->data);
    }

    protected function renderWithData(array $data): string {
        if ($this->rendering) throw new RuntimeException('Circular view tree.');
        $this->rendering = true;
        $output = '';
        try {
            foreach ($this->items as $item) $output .= $this->renderModel($this->model, array_merge($data, $item), []);
            return $output;
        } finally {
            $this->rendering = false;
        }
    }

}
