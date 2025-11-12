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

namespace Inane\View\Model;

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
	 * @param bool|string|array    $value The value to assign to the option. Can be a boolean, string, or array.
	 * 
	 * @return self                Returns the current instance for method chaining.
	 */
	public function setOption(string $name, bool|string|array $value): self;
}
