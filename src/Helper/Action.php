<?php

/**
 * Action
 *
 * Inane Library
 *
 * $Id$
 * $Date$
 *
 * PHP version 8.5
 *
 * @author   Philip Michael Raab <philip@cathedral.co.za>
 * @package  inanepain\view
 * @category view
 *
 * @license  UNLICENSE
 * @license  https://unlicense.org/UNLICENSE UNLICENSE
 *
 * _version_ $version
 */

declare(strict_types = 1);

namespace Inane\View\Helper;

use Inane\Stdlib\Enum\CoreEnumInterface;
use Inane\Stdlib\Enum\CoreEnumTrait;

/**
 * Action
 *
 * @version 0.1.0
 */
enum Action: string implements CoreEnumInterface {
    case Start = 'start';
    case End   = 'end';
    case Close = 'close';
    case None  = '';

    use CoreEnumTrait;
}
