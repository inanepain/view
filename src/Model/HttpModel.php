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
 * @version $version
 */

declare(strict_types=1);

namespace Inane\View\Model;

/**
 * Class HttpModel
 *
 * Model with items used for rendering HTML.
 */
class HttpModel extends AbstractModel {
    public string $template;
}