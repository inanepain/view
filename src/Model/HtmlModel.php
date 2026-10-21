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


/**
 * Class HtmlModel
 *
 * Model with renders items to HTML.
 */
class HtmlModel extends AbstractModel {
    protected array $optionProperties = [
        'httpStatus',
        'status',
        'headers',
        'useLayout',
        'template',
    ];

    #region Option Properties
    /**
     * The template string used for rendering views.
     *
     * @var string
     * @access protected (set)
     */
    public protected(set) string $template = '';


    #endregion Option Properties

    /**
     * The class responsible for rendering views.
     *
     * @var string
     * @access protected (set)
     */
}
