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

namespace Inane\View;

use Inane\View\Model\TextModel;

use function implode;
use function is_array;
use function str_repeat;

use const PHP_EOL;

/**
 * Plain text view that renders arrays and nested views into text.
 */
class TextView extends View {
    /** @var string HTTP content type produced by this view */
    protected string $contentType = 'text/plain';

    public function __construct(array|TextModel $data = []) {
        parent::__construct($data instanceof TextModel ? $data->variables : $data);
    }

    /**
     * @inheritDoc
     */
    public function render(): string {
        return $this->processData($this->data);
    }

    /**
     * Recursively convert data into a plain text representation.
     *
     * @param mixed $data
     */
    private function processData($data, int $indent = 0): string {
        if ($data instanceof View) {
            return $data->render();
        }

        if (is_array($data)) {
            $lines = [];
            foreach ($data as $key => $value) {
                $prefix = str_repeat('  ', $indent);
                if (is_array($value)) {
                    $lines[] = "{$prefix}{$key}:";
                    $lines[] = $this->processData($value, $indent + 1);
                } else {
                    $lines[] = "{$prefix}{$key}: {$value}";
                }
            }
            return implode(PHP_EOL, $lines);
        }

        return (string) $data;
    }
}
