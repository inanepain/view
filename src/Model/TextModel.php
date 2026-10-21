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
use Inane\View\Renderer\StringRenderer;

/**
 * Class TextModel
 *
 * Model with renders items to plain text.
 */
class TextModel extends AbstractModel {
	/**
	 * @var array http headers
	 */
	protected(set) array $headers = ['content-type' => 'text/plain; charset=UTF-8'];

    /**
	 * @var bool
	 */
	public bool $useLayout = false;

    protected string $template = '{$text}';

    protected(set) string $rendererClass = StringRenderer::class;

    /**
     * Renders the model as plain text.
     *
     * @return string Rendered text.
     *
     * @throws RuntimeException If a template variable cannot be rendered.
     */
    public function render(): string {
        return $this->rendererInstance->render($this->template, $this->variables);
    }
}
