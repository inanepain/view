<?php

/**
 * HtmlBuilder
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

use InvalidArgumentException;
use Stringable;

use function array_filter;
use function count;
use function htmlspecialchars;
use function implode;
use function is_array;
use function is_int;
use function is_string;
use function preg_match_all;
use function str_ireplace;
use function trim;

use const ENT_QUOTES;
use const ENT_SUBSTITUTE;
use const PREG_SET_ORDER;

class HtmlBuilder implements Stringable {
    private array $options = [
        'doctype' => 'HTML',
        'lang'    => 'en',
    ];

    /**
     * @var array stored html structure commands
     */
    private array $elementsList = [];

    /**
     * HtmlBuilder Constructor
     *
     * @param string $stylesheetPosition location where to add style sheets
     */
    public function __construct(
        private readonly string $stylesheetPosition = 'footer',
    ) {}

    /**
     * Returns html string
     *
     * @return string the rendered html
     */
    public function __toString(): string {
        return $this->render();
    }

    /**
     * Kicks off a new build process
     *
     * @param string $stylesheetPosition
     *
     * @return static HtmlBuilder
     */
    public static function create(string $stylesheetPosition = 'footer'): static {
        return new static($stylesheetPosition);
    }

    /**
     * Parses the method to determine the tag and action.
     *
     * @param string $calledMethod
     *
     * @return (string|\Inane\View\Helper\Action)[]
     */
    protected function parseMethodName(string $calledMethod): array {
        preg_match_all('/(?<tag>[a-z]+[0-9]?)(?<action>Start|End)?/', $calledMethod, $matches, PREG_SET_ORDER);
        @[
            'tag'    => $tag,
            'action' => $action,
        ] = $matches[0] + ['action' => 'None'];
        $action = $action ? Action::tryFromName($action, true) : Action::None;

        return [
            $tag,
            $action,
        ];
    }

    /**
     * Generates a style attribute string from the given styles.
     *
     * @param array|string $style  An array of style properties and values,
     *                             or a string containing preformatted CSS declarations.
     *
     * @return string The rendered style attribute string.
     *
     * @throws InvalidArgumentException If the provided style argument is neither a string nor an array.
     */
    protected function styleAttribute(array|string $style): string {
        if (is_string($style)) {
            $css = $style;
        } else {
            $declarations = [];

            foreach($style as $property => $value) {
                // Numeric entries contain complete declarations.
                if (is_int($property)) $declarations[] = trim($value);
                else $declarations[] = "$property:" . trim($value);
            }

            $css = implode(';', array_filter($declarations, 'strlen'));
        }

        return htmlspecialchars(
            $css,
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8',
        );

        //        return 'style="' . htmlspecialchars(
        //                $css,
        //                ENT_QUOTES | ENT_SUBSTITUTE,
        //                'UTF-8',
        //            ) . '"';
    }

    /**
     * Render HTML string
     *
     * @return string html string
     */
    public function render(): string {
        $stylesList = [];
        //        $output = "<!DOCTYPE {$this->options['doctype']}><html lang=\"{$this->options['lang']}\">";
        $output = '';

        foreach($this->elementsList as $currElement) {
            $parameters = $currElement[1][0] ?? '';
            $paramsList = [];
            $params = '';
            $currStyle = '';
            $currSelector = '';
            $styleTag = 'style';

            if (is_array($parameters)) {
                foreach($parameters as $key => $val) {
                    if ($key === 'styleSelector')
                        $currSelector .= $val;
                    elseif ($key === 'style') $currStyle = $this->styleAttribute($val);
                    else $paramsList[] = "$key=\"$val\"";
                }
                $params = ' ' . implode(' ', $paramsList);
            }

            [
                $tag,
                $action,
            ] = $this->parseMethodName($currElement[0]);

            if (!empty($paramsList)) $styleTag = ' ' . $styleTag;
            if ($currSelector === '' && $currStyle !== '') $params .= "$styleTag=\"$currStyle\"";
            elseif ($currSelector !== '' && $currStyle !== '') $stylesList[$currSelector] = $currStyle;

            if ($action !== Action::None) {
                if ($action === Action::Start) {
                    $output .= '<' . $tag . $params . '>';
                    if ($tag === 'head') $haveHeader = true;
                } elseif ($action === Action::End) {
                    if ($tag === 'head' && $this->stylesheetPosition === 'head') $output .= '[__CssContentPlaceholder__]';
                    elseif ($tag === 'body' && $this->stylesheetPosition === 'footer') $output .= '[__CssContentPlaceholder__]';
                    $output .= '</' . $tag . '>';
                }
            } else {
                if ($tag === 'contents') $output .= $currElement[1][0];
                else $output .= '<' . $tag . $params . ' />';
            }
        }
        $cssCode = '<style>';
        if (count($stylesList) > 0) {
            foreach($stylesList as $sKey => $sVal) {
                if ($sKey === '' || $sVal === '') continue;
                $cssCode .= $sKey . '{ ' . $sVal . ' }';
            }
        }
        $cssCode .= '</style>';

        // replace placeholder with actual css code
        $output = str_ireplace('[__CssContentPlaceholder__]', $cssCode, $output);

        //        $output .= '</html>';
        $output .= '';

        return $output;
    }

    /**
     * Method Handler
     *
     * @param string $name      method name
     * @param array  $arguments method arguments
     *
     * @return mixed $this
     */
    public function __call(string $name, array $arguments): mixed {
        $this->elementsList[] = [
            $name,
            $arguments,
        ];

        return $this;
    }
}
