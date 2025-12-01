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

namespace Inane\View;

use Inane\Config\ConfigAwareAttribute;
use Inane\Config\ConfigAwareTrait;
use Inane\Stdlib\Array\OptionsInterface;
use Inane\Stdlib\Options;
use Inane\View\Model\AbstractModel;
use Inane\View\Model\HttpModel;
use Inane\View\Renderer\PhpRenderer;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use function array_merge;

/**
 * Class View
 *
 * Model with renders items to HTML.
 */
#[ConfigAwareAttribute]
class ViewManager {
	use ConfigAwareTrait;
}
