<?php

declare(strict_types=1);

namespace App\AI\Minimax\State;

use App\AI\Minimax\Action\ActionGeneratorInterface;
use App\AI\Minimax\Action\ActionSequence;
use App\AI\Minimax\Context\ContextInterface;
use Generator;
use RuntimeException;

class PossibleState
{
	private ?ActionGeneratorInterface $action_generator;
	private ?int $score = null;
	private bool $is_final_action;
	private ContextInterface $context;
	private ?array $actions = null;

	public function __construct(
		?ActionGeneratorInterface $action_generator,
		ContextInterface $context,
		bool $is_final_action
	)
	{
		$this->action_generator = $action_generator;
		$this->context = $context;
		$this->is_final_action = $is_final_action;
	}

	/**
	 * @return ActionSequence[]|Generator
	 */
	public function getActionsSequences(): Generator
	{
		if ($this->actions !== null)
		{
			foreach($this->actions as $action)
			{
				yield $action;
			}
		}

		foreach ($this->action_generator->getActionSequences($this->context) as $action_sequence)
		{
			$this->actions[] = $action_sequence;
			yield $action_sequence;
		}

		return [];
	}

	public function getScoreOverContext(): int
	{
		$score = 1; // $this->context

		if ($this->score !== null)
		{
			throw new RuntimeException("Score was already set.");
		}

		$this->score = $score;

		return $this->score;
	}

	public function isFinalAction(): bool
	{
		return $this->is_final_action;
	}

	public function setScore(int $score): void
	{
		$this->score = $score;
	}

	public function getScore(): ?int
	{
		return $this->score;
	}
}