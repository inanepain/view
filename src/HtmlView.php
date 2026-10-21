<?php

declare(strict_types=1);

namespace Inane\View;

use Inane\Stdlib\Array\OptionsInterface;
use Inane\Stdlib\Options;
use Inane\View\Exception\RuntimeException;
use Inane\View\Model\HtmlModel;
use Inane\View\Renderer\PhpRenderer;

use function array_key_exists;
use function array_merge;
use function htmlspecialchars;
use function in_array;

use const ENT_QUOTES;
use const ENT_SUBSTITUTE;

/** Renders HTML models and their nested template trees. */
class HtmlView extends View {
    protected string $contentType = 'text/html; charset=UTF-8';

    protected bool $rendering = false;
    private OptionsInterface $children {
        get => isset($this->children) ? $this->children : ($this->children = new Options());
        set => $this->children = $value;
    }
    private array $renderedChildren = [];

    public function __construct(
        protected readonly PhpRenderer $renderer,
        protected readonly HtmlModel $model,
        array $data = [],
    ) {
        parent::__construct($data);
    }

    /**
     * Renders the model and its children.
     *
     * @return string Rendered HTML.
     *
     * @throws RuntimeException If the model tree is circular.
     * @throws \Throwable If template rendering fails.
     */
    public function render(): string {
        return $this->renderWithData($this->data);
    }

    protected function renderWithData(array $data): string {
        if ($this->rendering) throw new RuntimeException('Circular view tree.');
        $this->rendering = true;
        try {
            return $this->renderModel($this->model, $data, []);
        } finally {
            $this->rendering = false;
        }
    }

    public function setData(string $key, mixed $value): static {
        $this->data[$key] = $value;
        return $this;
    }

    public function addChild(string $name, HtmlView|View $view): static {
        $this->children->offsetSet($name, $view);
        return $this;
    }

    public function __get(string $name): string {
        if (!array_key_exists($name, $this->renderedChildren)) throw new RuntimeException("Rendered child unavailable: `$name`");
        return $this->renderedChildren[$name];
    }

    protected function escape(mixed $value): string {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public function renderPartial(string $template, array $data = []): string {
        return $this->renderer->render($template, $data);
    }

    /**
     * @param array<string, mixed> $overrides
     * @param array<int, HtmlModel> $ancestors
     */
    protected function renderModel(HtmlModel $model, array $overrides, array $ancestors): string {
        if (in_array($model, $ancestors, true)) throw new RuntimeException('Circular view model tree.');
        $ancestors[] = $model;
        $variables = array_merge($model->variables, $overrides);

        return $this->renderOne($model, $variables, $ancestors);
    }

    /** @param array<string, mixed> $variables @param array<int, HtmlModel> $ancestors */
    private function renderOne(HtmlModel $model, array $variables, array $ancestors): string {
        $children = [];
        foreach ($this->children as $name => $child) {
            if (!$child instanceof HtmlView) throw new RuntimeException('HTML subviews must be HTML views.');
            $children[$name] = $child->renderWithData($variables);
        }

        $context = clone $this;
        $context->renderedChildren = $children;
        return $this->renderer->render($model->template, array_merge($variables, $children), $context);
    }
}
