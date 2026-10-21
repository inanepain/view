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

namespace Inane\View\Model;

use Inane\View\Exception\RuntimeException;
use JsonException;

use function json_encode;

use const JSON_THROW_ON_ERROR;

/**
 * Class JsonModel
 *
 * Model with renders items to json.
 */
class JsonModel extends AbstractModel {
    /**
	 * @var array http headers
	 */
	protected(set) array $headers = ['content-type' => 'application/json'];

    /**
     * Encodes model variables as JSON.
     *
     * @return string Encoded JSON.
     *
     * @throws RuntimeException If the model variables cannot be encoded.
     */
    public function render(): string {
        try {
            return json_encode($this->variables, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('JSON model variables could not be encoded.', previous: $exception);
        }
    }
}
