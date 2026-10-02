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

use Inane\Http\HttpStatus;
use Inane\Stdlib\Array\OptionsInterface;

/**
 * Interface Model
 */
interface ModelInterface {
	/**
	 * Represents whether the layout is used or not.
	 */
	protected(set) bool $useLayout {
        get;
        set;
    }

    /**
	 * Retrieves the options associated with the model.
	 *
	 * @return OptionsInterface The Options object implementing OptionsInterface.
	 */
	public function getOptions(): OptionsInterface;
    
    /**
	 * Sets an option for the model.
	 *
	 * @param string               $name  The name of the option to set.
	 * @param HttpStatus|bool|int|string|array $value Model option value.
	 * 
	 * @return self                Returns the current instance for method chaining.
	 *
	 * @throws \TypeError If the option has an incompatible value.
	 * @throws \ValueError If the HTTP status is invalid.
	 */
	public function setOption(string $name, HttpStatus|bool|int|string|array $value): self;
}
