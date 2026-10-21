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

/**
 * Abstract class representing a generic view.
 *
 * Provides a base structure for managing view data and rendering content. All
 * concrete views must implement the `render` method to produce a string output
 * for the configured content type.
 */
abstract class View {
    /**
     * Arbitrary key/value data made available to the view when rendering.
     *
     * @var array<string, mixed>
     */
    protected array $data = [];

    /**
     * Template identifier or path used by template-driven views.
     * Not all view types make use of templates.
     */
    protected string $template;

    /**
     * HTTP content type this view produces (e.g. "text/html").
     */
    protected string $contentType;

    public function contentType(): string {
        return $this->contentType;
    }

    /**
     * @param array<string, mixed> $data Initial view data
     */
    public function __construct(array $data = []) {
        $this->data = $data;
    }

    /**
     * Render the view to its string representation.
     */
    abstract public function render(): string;

    /**
     * Sets a single data item on the view.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return $this
     */
    public function setData(string $key, $value): self {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Nests a child view which will be rendered when this view renders.
     *
     * @param string $key  Data key to assign the nested view to
     * @param View   $view Child view instance
     *
     * @return $this
     */
    public function nest(string $key, View $view): self {
        $this->data[$key] = $view;
        return $this;
    }
}
