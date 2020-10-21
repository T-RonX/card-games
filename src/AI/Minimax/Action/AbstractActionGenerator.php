<?php

declare(strict_types=1);

namespace App\AI\Minimax\Action;

use App\AI\Minimax\Context\ContextInterface;

abstract class AbstractActionGenerator implements ActionGeneratorInterface
{
	private ContextInterface $context;

	abstract protected function createActionSequences(ContextInterface $context): array;

	/**
	 * @return Action[]
	 */
	public function getActionSequences(): iterable
	{
		static $actions = null;

		if ($actions === null)
		{
			$actions = $this->createActionSequences($this->context);
		}

		foreach ($actions as $action)
		{
			yield $action;
		}

		return [];
	}

	public function setContext(ContextInterface $context): void
	{
		$this->context = $context;
	}
}