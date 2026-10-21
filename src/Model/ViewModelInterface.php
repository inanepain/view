<?php

declare(strict_types=1);

namespace Inane\View\Model;

/** Model exposing the data to be rendered by a view. */
interface ViewModelInterface extends ModelInterface {
	public bool $useLayout { get; set; }
	public array $variables { get; }
}
