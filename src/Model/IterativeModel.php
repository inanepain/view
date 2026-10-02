<?php
declare(strict_types=1);

namespace Inane\View\Model;

use Inane\Stdlib\Array\OptionsInterface;
use Inane\View\Exception\RuntimeException;
use Inane\View\Renderer\PhpRenderer;

/**
 * Repeats an HTTP template for each variable set.
 */
class IterativeModel extends HttpModel {
    private readonly array $items;

    /**
     * Materialises iterable items so the model can be rendered repeatedly.
     *
     * @param iterable<array<string, mixed>> $items Rows containing arbitrary template variables.
     * @param array<string, mixed> $variables Shared template variables.
     * @param array<string, bool|string|array>|OptionsInterface $options Model options.
     *
     * @throws RuntimeException If an item is not a variable array.
     * @throws \TypeError If an option has an incompatible value.
     */
    public function __construct(iterable $items = [], array $variables = [], array|OptionsInterface $options = []) {
        $rows = [];
        foreach ($items as $item) {
            if (!is_array($item)) throw new RuntimeException('View items must be arrays of template variables.');
            $rows[] = $item;
        }
        $this->items = $rows;
        parent::__construct($variables, $options);
    }

    /**
     * Concatenates rows, with item variables overriding shared variables.
     *
     * @param PhpRenderer $renderer Renderer used throughout the tree.
     * @param array<string, mixed> $data Shared overrides containing arbitrary template variables.
     *
     * @return string Rendered rows, or an empty string for no items.
     *
     * @throws RuntimeException If a circular tree or invalid template is encountered.
     * @throws \Throwable If template execution fails.
     */
    public function render(PhpRenderer $renderer, array $data = []): string {
        $output = '';
        foreach ($this->items as $item)
            $output .= parent::render($renderer, array_merge($data, $item));

        return $output;
    }
}