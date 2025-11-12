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

use Inane\Http\HttpStatus;
use Inane\Stdlib\{
	Array\OptionsInterface,
	Options
};

use function array_key_exists;
use function array_merge;
use function in_array;
use function is_array;
use function property_exists;

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
	#region Option Properties
	/**
	 * The HTTP status associated with the model.
	 * Defaults to HttpStatus::Ok.
	 *
	 * @var HttpStatus
	 */
	protected(set) HttpStatus $httpStatus = HttpStatus::Ok;

	/**
	 * @var int http response status
	 */
	protected(set) int $status {
		get => $this->httpStatus->code();
		set => $this->httpStatus = HttpStatus::from($value);
	}

	/**
	 * Status message describing the current state or result.
	 *
	 * @var string
	 */
	public string $statusMessage {
		get => $this->httpStatus->description();
	}

	/**
	 * @var array<string, string|string[]> http headers
	 */
	protected(set) array $headers = [];

	/**
	 * Indicates whether the layout should be used when rendering views.
	 *
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
	#endregion Option Properties

	/**
	 * @var array List of option property names for the model.
	 */
	protected array $optionProperties = [
		'httpStatus',
		'status',
		'headers',
		'useLayout',
		'renderer',
	];

	/**
	 * Constructor for the AbstractModel class.
	 *
	 * Initializes the model instance with required dependencies or properties.
	 *
	 * @param mixed ...$args Arguments required for model initialization.
	 */
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

	#region Variable Methods
	/**
	 * Retrieves the value of a variable by its name.
	 *
	 * @param string $name The name of the variable to retrieve.
	 * 
	 * @return null|bool|string|array The value of the variable, which can be null, boolean, string, or array.
	 */
	public function getVariable(string $name): null|bool|string|array {
		if (array_key_exists($name, $this->variables))
			return $this->variables[$name];

		return null;
	}
	#endregion Variable Methods

	#region Option Methods
	/**
	 * Retrieves the options associated with the model.
	 *
	 * @return OptionsInterface The options object implementing OptionsInterface.
	 */
	public function getOptions(): OptionsInterface {
		$options = new Options();
		foreach ($this->optionProperties as $name)
			$options->set($name, $this->getOption($name));

		return $options;
	}

	/**
	 * Retrieves the value of an option by its name.
	 *
	 * @param string $name The name of the option to retrieve.
	 * 
	 * @return null|bool|string|array The value of the option, which can be null, boolean, string, or array.
	 */
	public function getOption(string $name): null|bool|string|array {
		if (property_exists($this, $name) && in_array($name, $this->optionProperties))
			return $this->$name;

		return null;
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
		foreach ($options as $key => $value)
			return $this->setOption($key, $value);

		return $this;
	}

	/**
	 * Sets an option for the model.
	 *
	 * @param string               $name  The name of the option to set.
	 * @param bool|string|array    $value The value to assign to the option. Can be a boolean, string, or array.
	 * 
	 * @return self                Returns the current instance for method chaining.
	 */
	public function setOption(string $name, bool|string|array $value): self {
		if (property_exists($this, $name) && in_array($name, $this->optionProperties))
			if ($name === 'headers' && is_array($value)) $this->headers = array_merge($this->headers, $value);
			else $this->$name = $value;

		return $this;
	}
	#endregion Option Methods
}
