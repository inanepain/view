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

/**
 * Class TextModel
 *
 * Model with renders items to plain text.
 */
class TextModel extends AbstractModel {
	/**
	 * @var array http headers
	 */
	protected(set) array $headers = ['content-type' => 'plain/text'];

    /**
	 * @var bool
	 */
	protected(set) bool $useLayout = false;

    protected string $template = '{text}';
}
