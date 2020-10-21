<?php

declare(strict_types=1);

namespace App\Games\Duizenden\AI\ActionGenerator;

use App\AI\Minimax\Action\AbstractActionGenerator;
use App\AI\Minimax\Context\ContextInterface;
use App\Games\Duizenden\AI\Context\Context;
use App\Games\Duizenden\Game;
use RuntimeException;

class ActionGenerator extends AbstractActionGenerator
{
	/**
	 * @var SequenceCalculatorInterface[]
	 */
	private iterable $sequence_calculators;

	public function __construct(iterable $sequence_calculators)
	{
		$this->sequence_calculators = $sequence_calculators;
	}

	protected function createActionSequences(ContextInterface $context): array
	{
		$context = $this->validateContext($context);
		$sequence_calculator = $this->getSequenceCalculator($context->getAllowedActions());

		return $sequence_calculator->getActionSequences($context);
	}

	private function getSequenceCalculator(array $allowed_actions): SequenceCalculatorInterface
	{
		foreach ($this->sequence_calculators as $sequence_calculator)
		{
			if ($sequence_calculator->supports($allowed_actions))
			{
				return $sequence_calculator;
			}
		}

		throw new RuntimeException("No sequence calculator found for given actions.");
	}

	private function validateContext(ContextInterface $context): Context
	{
		if (!$context instanceof Context)
		{
			throw new RuntimeException();
		}

		return $context;
	}

	public function getGameName(): string
	{
		return Game::NAME;
	}
}