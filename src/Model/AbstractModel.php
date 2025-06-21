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
 * Abstract Model Class
 * 
 * Models are the glue and coordinators for the various items that are used to compile a specific output type.
 * 
 * Generally these are the components used:
 * - Variables Container
 * - Which, if any, template to use
 * - And the renderer used to compile everything
 */
abstract class AbstractModel implements ModelInterface {
    
}