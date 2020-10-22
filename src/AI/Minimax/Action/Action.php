<?php

declare(strict_types=1);

namespace App\AI\Minimax\Action;

use App\AI\Minimax\Context\ContextInterface;
use Closure;

class Action
{
	private Closure $invoker;
	private bool $is_final_action;

	public function __construct(Closure $invoker, bool $is_final_action = false)
	{
		$this->invoker = $invoker;
		$this->is_final_action = $is_final_action;
	}

	public function execute(ContextInterface $context): ContextInterface
	{
		$closure = $this->invoker;

		return $closure($context);
	}

	public function isIsFinalAction(): bool
	{
		return $this->is_final_action;
	}
}
