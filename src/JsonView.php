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

use Inane\Stdlib\Json;
use Inane\View\Model\JsonModel;

use function array_map;
use function is_array;

/**
 * Renders data as JSON.
 *
 * Supports nested views by rendering them first and decoding their JSON
 * representation back into arrays to be composed into the parent structure.
 */
class JsonView extends View {
    /**
     * HTTP content type produced by this view.
     *
     * @var string
     */
    protected string $contentType = 'application/json';

    /**
     * JSON encoding options flags.
     */
    private int $options;

    public function __construct(array|JsonModel $data = [], int $options = JSON_PRETTY_PRINT) {
        parent::__construct($data instanceof JsonModel ? $data->variables : $data);
        $this->options = $options;
    }

    /**
     * @inheritDoc
     */
    public function render(): string {
        // Process nested views
        $processedData = $this->processData($this->data);
        return Json::encode($processedData, ['flags' => $this->options]);
    }

    /**
     * Recursively process data to ensure nested views are rendered and decoded.
     *
     * @param array<string, mixed>|string|View $data
     *
     * @return array<string, mixed>|string
     */
    private function processData(string|array|View $data): array|string {
        if (is_array($data)) {
            return array_map([$this, 'processData'], $data);
        }
        return $data instanceof View
            ? Json::decode($data->render())
            : $data;
    }
}
