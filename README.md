# Readme: View

> $Id$ ($Date$)

View layer with models for most common of content types.

## Install

`composer require inanepain/view`

## Usage

Currently only two renderers are available:

 - PhpRenderer
 - StringRenderer

### Php Renderer

By far the more functional of the two, the PhpRenderer uses php files as templates. Any php code within the templates runs as expected.
The optional `$thisObject` parameter can be accessed inside the template as `$this`.

**example:**

```php
// TODO: PhpRenderer example
```

### String Renderer

This basic renderer completes string templates replacing the variable placeholder `{$name}` with the value from the `$data` array.
Everything else is treated as text, the template will not by run as code even if it is code.

**example:**

```php
$data = [
    'label' => ['label' => 'Inane Website'],
    'a' => ['title' => 'Inane', 'url' => 'https://inane.co.za'],
];

$sr = new StringRenderer([
    'label' => '<label>{$label}</label',
    'a' => '<a href="{$url}">{$title}</a>',
]);

echo $sr->render('label', $data['label'], true) . PHP_EOL;
echo $sr->render('a', $data['a'], true) . PHP_EOL;

echo $sr->render('label', ['label' => 'Google'], true) . PHP_EOL;
echo $sr->render('a', ['title' => 'SA', 'url' => 'https://www.google.co.za'], true) . PHP_EOL;
echo $sr->render('a', ['title' => 'International', 'url' => 'https://www.google.com'], true) . PHP_EOL;

echo $sr->render('<h1>{$heading}</h1>', ['heading' => 'Bob'], false) . PHP_EOL;
```
