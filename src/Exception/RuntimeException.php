<?php

/**
 * Inane\View
 *
 * View
 *
 * PHP version 8.1
 *
 * @package Inane\View
 * @author Philip Michael Raab<peep@inane.co.za>
 *
 * @license UNLICENSE
 * @license https://github.com/inanepain/stdlib/raw/develop/UNLICENSE UNLICENSE
 *
 * @version $Id$
 * $Date$
 */

declare(strict_types=1);

namespace Inane\View\Exception;

use Inane\Stdlib\Exception\RuntimeException as InaneRuntimeException;

/**
 * RuntimeException
 *
 * @package Inane\View
 *
 * @version 0.3.0
 */
class RuntimeException extends InaneRuntimeException {
    protected $code = 700;
}
