<?php

declare(strict_types=1);

namespace App\Games\Duizenden\StateBuilder;

use App\Games\Duizenden\Game;
use App\Games\Duizenden\Actions\ActionType;
use App\Games\Duizenden\Workflow\TransitionType;
use Symfony\Component\Workflow\StateMachine;

class AllowedActions
{
	private StateMachine $state_machine;

	public function __construct(StateMachine $state_machine)
	{
		$this->state_machine = $state_machine;
	}

	/**
	 * @return ActionType[]
	 */
	public function getAllowedActions(Game $game): array
	{
		$actions = [];

		foreach ($this->state_machine->getEnabledTransitions($game) as $marking)
		{
			switch ($marking->getName())
			{
				case TransitionType::DEAL:
					$actions[] = ActionType::DEAL();
					break;

				case TransitionType::DISCARD_END_TURN:
					$actions[] = ActionType::DISCARD_END_TURN();
					break;

				case TransitionType::DISCARD_END_ROUND:
					$actions[] = ActionType::DISCARD_END_ROUND();
					break;

				case TransitionType::DISCARD_END_GAME:
					$actions[] = ActionType::DISCARD_END_GAME();
					break;

				case TransitionType::DRAW_FROM_UNDRAWN:
					$actions[] = ActionType::DRAW_FROM_UNDRAWN();
					break;

				case TransitionType::DRAW_FROM_DISCARDED:
					$actions[] = ActionType::DRAW_FROM_DISCARDED();
					break;

				case TransitionType::MELD:
					$actions[] = ActionType::MELD_CARDS();
					break;

				case TransitionType::EXTEND_MELD:
					$actions[] = ActionType::EXTEND_MELD();
					break;
			}
		}

		return array_unique($actions);
	}
}