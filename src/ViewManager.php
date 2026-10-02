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
use Inane\View\Exception\RuntimeException;
use Inane\View\Model\HttpModel;
use Inane\View\Renderer\PhpRenderer;

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
    public function __construct(private readonly ?PhpRenderer $renderer = null) {
    }

    /**
     * Renders a nested or iterative HTTP view model.
     *
     * @param HttpModel $model Root view model.
     *
     * @return string Rendered view tree.
     *
     * @throws RuntimeException If no renderer is configured or rendering fails.
     * @throws \Throwable If template execution fails.
     */
    public function render(HttpModel $model): string {
        if ($this->renderer === null) throw new RuntimeException('A PHP renderer is required to render view models.');

        return $model->render($this->renderer);
    }
}
