<?php
declare(strict_types=1);

namespace Inane\View\Tests;

use Inane\View\Exception\RuntimeException;
use Inane\View\Model\HttpModel;
use Inane\View\Model\IterativeModel;
use Inane\View\Renderer\PhpRenderer;
use Inane\View\Renderer\StringRenderer;
use Inane\View\ViewManager;
use PHPUnit\Framework\TestCase;

/**
 * Covers nested and iterative rendering.
 */
final class RendererTest extends TestCase {
    /**
     * Renders named string partials without interpreting replacement text.
     *
     * @throws \Throwable
     */
    public function testStringPartials(): void {
        $renderer = new StringRenderer(['item' => '[{$name}]']);
        $child = $renderer->renderPartial('item', ['name' => '$1\\path']);

        self::assertSame('[[$1\\path]]', $renderer->render('[{$child}]', ['child' => $child]));
        self::assertSame('[{$missing}]', $renderer->render('[{$missing}]'));
    }

    /**
     * Supports generators, shared variables and isolated item variables.
     *
     * @throws \Throwable
     */
    public function testStringIteration(): void {
        $renderer = new StringRenderer(['item' => '{$prefix}:{$name};']);
        $items = (static function (): \Generator {
            yield ['name' => 'one', 'prefix' => 'first'];
            yield ['name' => 'two'];
        })();

        self::assertSame('first:one;shared:two;', $renderer->renderEach('item', $items, ['prefix' => 'shared']));
        self::assertSame('', $renderer->renderEach('item', []));
    }

    /**
     * Rejects malformed iteration rows.
     *
     * @throws \Throwable
     */
    public function testInvalidIterationRow(): void {
        $renderer = new StringRenderer(['item' => '{$name}']);
        $this->expectException(RuntimeException::class);
        $renderer->renderEach('item', ['invalid']);
    }

    /**
     * Reports missing named templates consistently.
     *
     * @throws \Throwable
     */
    public function testMissingStringPartial(): void {
        $this->expectException(RuntimeException::class);
        new StringRenderer()->renderPartial('missing');
    }

    /**
     * Makes recursive partial helpers available inside PHP templates.
     *
     * @throws \Throwable
     */
    public function testPhpPartialsAndIteration(): void {
        $renderer = new PhpRenderer(__DIR__ . '/templates');
        self::assertSame('<[one][two]>', $renderer->render('partials', ['items' => [['name' => 'one'], ['name' => 'two']]]));
        self::assertSame('<>', $renderer->render('partials', ['items' => []]));
    }

    /**
     * Keeps explicit template object binding compatible.
     *
     * @throws \Throwable
     */
    public function testExplicitObjectBinding(): void {
        $renderer = new PhpRenderer(__DIR__ . '/templates');
        self::assertSame('[bound]', $renderer->render('binding', [], (object)['name' => 'bound']));
        $context = new class {
            private readonly string $name;

            /**
             * Initialises a private template context.
             */
            public function __construct() {
                $this->name = 'private';
            }
        };
        self::assertSame('[private]', $renderer->render('binding', [], $context));
        self::assertSame('[static]', PhpRenderer::renderTemplate(__DIR__ . '/templates/item.phtml', ['name' => 'static']));
    }

    /**
     * Does not leak output or buffers when a nested template fails.
     *
     * @throws \Throwable
     */
    public function testFailedNestedRenderCleansBuffers(): void {
        $renderer = new PhpRenderer(__DIR__ . '/templates');
        $level = ob_get_level();
        ob_start();
        try {
            $renderer->render('failure');
            self::fail('The failing template must throw.');
        } catch (RuntimeException $exception) {
            self::assertSame('Template failure', $exception->getMessage());
            self::assertSame($level + 1, ob_get_level());
            self::assertSame('', ob_get_contents());
        } finally {
            while (ob_get_level() > $level) ob_end_clean();
        }
        self::assertSame('[next]', $renderer->render('item', ['name' => 'next']));
    }

    /**
     * Renders grandchildren and iterative children without mutating models.
     *
     * @throws \Throwable
     */
    public function testNestedModels(): void {
        $renderer = new PhpRenderer(__DIR__ . '/templates');
        $rows = new IterativeModel(items: [['name' => 'one'], ['name' => 'two']], options: ['template' => 'item']);
        $inner = new HttpModel(options: ['template' => 'wrapper']);
        $inner->addChild('content', $rows);
        $outer = new HttpModel(options: ['template' => 'wrapper']);
        $outer->addChild('content', $inner);
        $manager = new ViewManager($renderer);

        self::assertSame('<<[one][two]>>', $manager->render($outer));
        self::assertSame('<<[one][two]>>', $manager->render($outer));
        self::assertSame([], $rows->variables);
    }

    /**
     * Makes child output available as a template variable too.
     *
     * @throws \Throwable
     */
    public function testChildVariables(): void {
        $renderer = new PhpRenderer(__DIR__ . '/templates');
        $parent = new HttpModel(variables: ['name' => 'parent', 'content' => 'shadowed'], options: ['template' => 'variable']);
        $parent->addChild('content', new HttpModel(variables: ['name' => 'child'], options: ['template' => 'item']));
        self::assertSame('parent:[child]', $parent->render($renderer));
    }

    /**
     * Applies all supplied model options.
     *
     * @throws \Throwable
     */
    public function testModelOptions(): void {
        $model = new HttpModel(options: ['useLayout' => false, 'template' => 'item']);
        self::assertFalse($model->useLayout);
        self::assertSame('item', $model->template);
    }

    /**
     * Handles empty models and merges iteration defaults per row.
     *
     * @throws \Throwable
     */
    public function testIterativeModelDefaults(): void {
        $renderer = new PhpRenderer(__DIR__ . '/templates');
        $model = new IterativeModel(items: [['name' => 'one'], []], variables: ['name' => 'default'], options: ['template' => 'item']);
        self::assertSame('[one][default]', $model->render($renderer));
        self::assertSame('[one][default]', $model->render($renderer));
        self::assertSame('', new IterativeModel(items: [], options: ['template' => 'item'])->render($renderer));
    }

    /**
     * Detects circular model trees rather than recursing indefinitely.
     *
     * @throws \Throwable
     */
    public function testCircularModels(): void {
        $renderer = new PhpRenderer(__DIR__ . '/templates');
        $parent = new HttpModel(options: ['template' => 'wrapper']);
        $child = new HttpModel(options: ['template' => 'wrapper']);
        $parent->addChild('content', $child);
        $child->addChild('content', $parent);
        $this->expectException(RuntimeException::class);
        $parent->render($renderer);
    }

    /**
     * Allows retrying the same model after rendering fails.
     *
     * @throws \Throwable
     */
    public function testModelRecoversAfterFailure(): void {
        $renderer = new PhpRenderer(__DIR__ . '/templates');
        $model = new HttpModel(options: ['template' => 'missing']);
        try {
            $model->render($renderer);
            self::fail('A missing template must throw.');
        } catch (RuntimeException) {
            $model->setOption('template', 'item');
        }
        self::assertSame('[recovered]', $model->render($renderer, ['name' => 'recovered']));
    }

    /**
     * Passes row variables to children, while preserving explicit child values.
     *
     * @throws \Throwable
     */
    public function testNestedChildrenWithinIteration(): void {
        $renderer = new PhpRenderer(__DIR__ . '/templates');
        $model = new IterativeModel(items: [['name' => 'one'], ['name' => 'two']], options: ['template' => 'wrapper']);
        $model->addChild('content', new HttpModel(options: ['template' => 'item']));
        self::assertSame('<[one]><[two]>', $model->render($renderer));

        $model->addChild('content', new HttpModel(variables: ['name' => 'fixed'], options: ['template' => 'item']));
        self::assertSame('<[fixed]><[fixed]>', $model->render($renderer));
    }

    /**
     * Allows generator-backed models to render more than once.
     *
     * @throws \Throwable
     */
    public function testGeneratorModel(): void {
        $items = (static function (): \Generator {
            yield ['name' => 'one'];
            yield ['name' => 'two'];
        })();
        $model = new IterativeModel(items: $items, options: ['template' => 'item']);
        $renderer = new PhpRenderer(__DIR__ . '/templates');
        self::assertSame('[one][two]', $model->render($renderer));
        self::assertSame('[one][two]', $model->render($renderer));
    }

    /**
     * Rejects malformed model rows before rendering.
     *
     * @throws \Throwable
     */
    public function testInvalidModelRow(): void {
        $this->expectException(RuntimeException::class);
        new IterativeModel(['invalid']);
    }

    /**
     * Checks renderer configuration explicitly.
     *
     * @throws \Throwable
     */
    public function testUnconfiguredManager(): void {
        $this->expectException(RuntimeException::class);
        new ViewManager()->render(new HttpModel());
    }

    /**
     * Leaves inserted placeholders literal and supports scalar values.
     *
     * @throws \Throwable
     */
    public function testLiteralStringSubstitution(): void {
        self::assertSame('{$name}:42:', StringRenderer::renderTemplate('{$child}:{$count}:{$empty}', ['child' => '{$name}', 'name' => 'parent', 'count' => 42, 'empty' => null]));
        self::assertSame('literal', StringRenderer::renderTemplate('{$a.b}', ['a.b' => 'literal']));
        self::assertSame('0', StringRenderer::renderTemplate('0'));
    }

    /**
     * Reports unsupported string variables clearly.
     *
     * @throws \Throwable
     */
    public function testInvalidStringVariable(): void {
        $this->expectException(RuntimeException::class);
        StringRenderer::renderTemplate('{$items}', ['items' => []]);
    }

    /**
     * Protects rendering internals against variable name collisions.
     *
     * @throws \Throwable
     */
    public function testReservedPhpVariables(): void {
        $renderer = new PhpRenderer(__DIR__ . '/templates');
        self::assertSame('[safe]', $renderer->render('item', ['name' => 'safe', 'templateFile' => __DIR__ . '/templates/throw.phtml', 'bufferLevel' => -1]));
    }

    /**
     * Cleans buffers for non-runtime exceptions too.
     *
     * @throws \Throwable
     */
    public function testTemplateErrorCleansBuffers(): void {
        $renderer = new PhpRenderer(__DIR__ . '/templates');
        $level = ob_get_level();
        try {
            $renderer->render('error');
            self::fail('The template must throw.');
        } catch (\Error $error) {
            self::assertSame('Template error', $error->getMessage());
            self::assertSame($level, ob_get_level());
        }
    }
}