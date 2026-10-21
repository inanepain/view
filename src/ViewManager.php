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

use Inane\Config\ConfigAware\ConfigAwareAttribute;
use Inane\Config\ConfigAware\ConfigAwareTrait;
use Inane\View\Model\ViewModelInterface;
use Inane\View\Renderer\PhpRenderer;
use RuntimeException;

/**
 * Class View
 *
 * Model with renders items to HTML.
 */
#[ConfigAwareAttribute]
class ViewManager {
	use ConfigAwareTrait;

    /**
     * Sets the renderer used by HTTP view models.
     *
     * @param PhpRenderer|null $renderer Configured template renderer.
     */
    public function __construct(public readonly ?PhpRenderer $renderer = null) {

    }

    /**
     * Creates a view for a model.
     *
     * @param ViewModelInterface $model Root view model.
     *
     * @return View Created view.
     *
     * @throws RuntimeException If view creation fails.
     * @throws \Throwable If template execution fails.
     */
    public function createView(ViewModelInterface $model): View {
        return ViewFactory::createForModel($model, $this->renderer);
    }

    /**
     * Creates and renders the view for a model.
     *
     * @param ViewModelInterface $model Root view model.
     *
     * @return string Rendered content.
     *
     * @throws RuntimeException If view creation or rendering fails.
     * @throws \Throwable If template execution fails.
     */
    public function render(ViewModelInterface $model): string {
        return $this->createView($model)->render();
    }
}
