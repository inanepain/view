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

use Inane\View\Exception\RuntimeException;
use Inane\View\Model\HtmlModel;
use Inane\View\Model\JsonModel;
use Inane\View\Model\TextModel;
use Inane\View\Model\ViewModelInterface;
use Inane\View\Renderer\PhpRenderer;

/**
 * A factory class for creating instances of various view types.
 */
class ViewFactory {
    public static function createForModel(ViewModelInterface $model, ?PhpRenderer $renderer = null): View {
        return match (true) {
            $model instanceof JsonModel => new JsonView($model, $model->getOption('flags') ?? JSON_PRETTY_PRINT),
            $model instanceof TextModel => new TextView($model),
            $model instanceof HtmlModel => new HtmlView($renderer ?? throw new RuntimeException('HTML rendering requires a PHP renderer.'), $model),
            default => throw new RuntimeException('No view is available for this model type.'),
        };
    }

    public static function createForFormat(string $format, mixed ...$arguments): View {
        return match ($format) {
            'html' => new HtmlView(...$arguments),
            'json' => new JsonView(...$arguments),
            'text' => new TextView(...$arguments),
            default => throw new RuntimeException("Unknown format: {$format}"),
        };
    }

    /**
     * Creates a new instance of a view based on the specified format.
     *
     * @param string $format  The format of the view to create ('html', 'json', 'text').
     * @param mixed  ...$args Additional arguments required for the view creation.
     *
     * @return View The created view instance corresponding to the specified format.
     *
     * @throws InvalidArgumentException If the specified format is not recognised.
     */
    public static function create(string $format, ...$args): View {
        return self::createForFormat($format, ...$args);
    }
}
