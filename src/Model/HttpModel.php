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

use Inane\Stdlib\Array\OptionsInterface;
use Inane\Stdlib\Options;
use Inane\View\Renderer\PhpRenderer;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
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
	 * get subview
	 *
	 * @param string $childName subview name
	 *
	 * @return mixed value
	 *
	 * @throws ContainerExceptionInterface
	 * @throws NotFoundExceptionInterface
	 */
	public function __get(string $childName): mixed {
		return (string)$this->children->get($childName);
	}

    /**
	 * @var string
	 */
	protected(set) string $renderer = PhpRenderer::class;

    /**
     * Adds a child HttpModel to the current model with the specified name.
     *
     * @param string $name The name to associate with the child model.
     * @param HttpModel $model The child HttpModel instance to add.
     *
     * @return self Returns the current instance for method chaining.
     */
    public function addChild(string $name, HttpModel $model): self {
        $this->children->offsetSet($name, $model);

        return $this;
    }
}
