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
 * Abstract Model Class
 * 
 * Models are the glue and coordinators for the various items that are used to compile a specific output type.
 * 
 * Generally these are the components used:
 * - Variables Container
 * - Which, if any, template to use
 * - And the renderer used to compile everything
 */
abstract class AbstractModel implements ModelInterface {
    /**
	 * @var int http response status
	 */
	protected(set) int $status = 200;

	/**
	 * @var array http headers
	 */
	protected(set) array $headers = [];

	/**
	 * @var bool
	 */
	protected(set) bool $useLayout = true;

	/**
	 * @var string
	 */
	protected(set) string $renderer;

    /**
     * The name of the template associated with this model.
     *
     * @var string
     */
    protected string $template = '';

    public function __construct(
        /**
         * @var array view variables
         */
        protected(set) array $variables = [],
        /**
         * @var array options model and renderer options
         */
        array|OptionsInterface $options = [],
    ) {
        $this->setOptions($options);
    }

    /**
	 * Set options to generic values
	 *
	 * Only options not yet set via other means will be set
	 *
	 * @param   array|OptionsInterface|  $options
	 *
	 * @return $this
	 */
	public function setOptions(array|OptionsInterface $options): self {
		foreach ($options as $key => $value) {
			if (property_exists($this, $key)) {
				if ($key === 'headers' && is_array($value)) {
					$this->headers = array_merge($this->headers, $value);
					continue;
				}
				$this->$key = $value;
			}
		}
		return $this;
	}
}
