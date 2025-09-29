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
use Inane\Stdlib\Options;
use Inane\View\Renderer\PhpRenderer;

use function array_merge;

/**
 * Class HttpModel
 *
 * Model with renders items to HTML.
 */
class HttpModel extends AbstractModel {
    #region Option Properties
    /**
     * The template string used for rendering views.
     * 
     * @var string
     * @access protected (set)
     */
    protected(set) string $template = '';

    /**
	 * @var array List of option property names for the model.
	 */
	protected array $optionProperties {
        get => array_merge($this->optionProperties, ['template']);
    }

    /**
     * Represents the child options for the model.
     *
     * @var OptionsInterface
     */
    protected OptionsInterface $children {
        get => isset($this->children) ? $this->children : ($this->children = new Options());
        set => $this->children = $value;
    }
    #endregion Option Properties

    /**
	 * get variable
	 *
	 * @param mixed $variable key
	 * 
	 * @return mixed value
	 */
	public function __get(mixed $variable) {
		return $this->getVariable($variable) ?: (string)$this->children->get($variable);
	}

    /**
	 * @var string
	 */
	protected(set) string $renderer = PhpRenderer::class;

    /**
     * Adds a child HttpModel to the current model with the specified name.
     *
     * @param HttpModel $model The child HttpModel instance to add.
     * @param string $name The name to associate with the child model.
     * 
     * @return self Returns the current instance for method chaining.
     */
    public function addChild(HttpModel $model, string $name): self {
        $this->children->offsetSet($name, $model);

        return $this;
    }
}
