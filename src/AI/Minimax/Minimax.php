<?php

declare(strict_types=1);

namespace App\AI\Minimax;

use App\AI\Minimax\Action\ActionSequence;
use App\AI\Minimax\Context\ContextInterface;
use App\AI\Minimax\State\PossibleState;
use App\AI\Minimax\State\PossibleStateFactory;
use RuntimeException;

class Minimax
{
	private PossibleStateFactory $state_factory;

	public function __construct(PossibleStateFactory $state_factory)
	{
		$this->state_factory = $state_factory;
	}

	public function findBestActionSequence(ContextInterface $context): ActionSequence
	{
		$state_tree = $this->state_factory->create($context);

		$this->minimax($state_tree, 3, -PHP_INT_MAX, PHP_INT_MAX, true);

		return $this->getFirstViableActionSequence($state_tree);
	}

	private function getFirstViableActionSequence(PossibleState $state): ActionSequence
	{
		foreach ($state->getActionsSequences() as $actions_sequence)
		{
			if ($actions_sequence->getResultingState()->getScore() === $state->getScore())
			{
				return $actions_sequence;
			}
		}

		throw new RuntimeException('No viable action found.');
	}

	private function isMaxDepthOrFinalAction(int $depth, PossibleState $state): bool
	{
		return $depth === 0 || $state->isFinalAction();
	}

	private function minimax(PossibleState $possible_state, int $depth, int $alpha, int $beta, bool $is_maximizing_player): int
	{
		if ($this->isMaxDepthOrFinalAction($depth, $possible_state))
		{
			return $possible_state->getScoreOverContext();
		}

		if ($is_maximizing_player)
		{
			$score = $this->maximize($possible_state, $depth, $alpha, $beta);
		}
		else
		{
			$score = $this->minimize($possible_state, $depth, $alpha, $beta);
		}

		$possible_state->setScore($score);

		return $score;
	}

	private function maximize(PossibleState $state, int $depth, int $alpha, int $beta): int
	{
		$max_score = -PHP_INT_MAX;

		foreach ($state->getActionsSequences() as $action_sequence)
		{
			$action_sequence->executeSequenceInSandbox();
			$score = $this->minimax($action_sequence->getResultingState(), $depth - 1, $alpha, $beta, false);
			$max_score = max($max_score, $score);
			$alpha = max($alpha, $score);

			if ($this->canPruneTree($alpha, $beta))
			{
				break;
			}
		}

		return $max_score;
	}

	private function minimize(PossibleState $state, int $depth, int $alpha, int $beta): int
	{
		$min_score = PHP_INT_MAX;

		foreach($state->getActionsSequences() as $action_sequence)
		{
			$action_sequence->executeSequenceInSandbox();
			$score = $this->minimax($action_sequence->getResultingState(), $depth - 1, $alpha, $beta, true);
			$min_score = min($min_score, $score);
			$beta = min($beta, $score);

			if ($this->canPruneTree($alpha, $beta))
			{
				break;
			}
		}

		return $min_score;
	}

	private function canPruneTree(int $alpha, int $beta): bool
	{
		return $beta <= $alpha;
	}
}