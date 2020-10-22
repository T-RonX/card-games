<?php

declare(strict_types=1);

namespace App\AI\Minimax\State;

use App\AI\Minimax\Action\ActionGeneratorInterface;
use App\AI\Minimax\Context\ContextInterface;
use RuntimeException;

class PossibleStateFactory
{
	/**
	 * @var ActionGeneratorInterface[]
	 */
	private iterable $action_generators;

	public function __construct(iterable $action_generators)
	{
		$this->action_generators = $action_generators;
	}

	public function create(ContextInterface $context, bool $is_final_action = false): PossibleState
	{
		$action_generator = $this->getActionGenerator($context->getGame()->getName());

		return new PossibleState($action_generator, $context, $is_final_action);
	}

	private function getActionGenerator(string $game_name): ActionGeneratorInterface
	{
		foreach ($this->action_generators as $action_generator)
		{
			if ($action_generator->getGameName() === $game_name)
			{
				return $action_generator;
			}
		}

		throw new RuntimeException(sprintf("No action generator found for game '%s'.", $game_name));
	}
}