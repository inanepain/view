<?php

/**
 * Inane: View
 *
 * View layer with models for the most common content types.
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

namespace Inane\View\Model;

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
}
