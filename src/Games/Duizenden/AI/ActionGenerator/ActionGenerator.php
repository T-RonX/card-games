<?php

declare(strict_types=1);

namespace App\Games\Duizenden\AI\ActionGenerator;

use App\AI\Minimax\Action\ActionGeneratorInterface;
use App\AI\Minimax\Context\ContextInterface;
use App\Games\Duizenden\AI\Context\Context;
use App\Games\Duizenden\Game;
use Generator;
use RuntimeException;

class ActionGenerator implements ActionGeneratorInterface
{
	/**
	 * @var SequenceCalculatorInterface[]
	 */
	private iterable $sequence_calculators;

	public function __construct(iterable $sequence_calculators)
	{
		$this->sequence_calculators = $sequence_calculators;
	}

	public function getActionSequences(ContextInterface $context): Generator
	{
		$context = $this->validateContext($context);
		$sequence_calculator = $this->getSequenceCalculator($context->getAllowedActions());

		return yield from $sequence_calculator->getActionSequences($context);
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