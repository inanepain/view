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

namespace Inane\View\Exception;

use Inane\Stdlib\Exception\RuntimeException as InaneRuntimeException;

/**
 * RuntimeException
 *
 * @version 0.3.0
 */
class RuntimeException extends InaneRuntimeException {
    protected $code = 700;
}
