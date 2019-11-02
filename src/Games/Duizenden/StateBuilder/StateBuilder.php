<?php

namespace App\Games\Duizenden\StateBuilder;

use App\Games\Duizenden\Game;
use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;
use App\Games\Duizenden\Score\Exception\UnmappedCardException;
use App\Games\Duizenden\StateCompiler\ActionType;
use App\Games\Duizenden\StateCompiler\StateCompiler;
use App\Games\Duizenden\StateCompiler\StateData;
use App\Games\Duizenden\Workflow\TransitionType;
use Symfony\Component\Workflow\StateMachine;

class StateBuilder
{
	/**
	 * @var StateCompiler
	 */
	private $state_compiler;

	/**
	 * @var StateMachine
	 */
	private $state_machine;

	public function __construct(
		StateCompiler $state_compiler,
		StateMachine $state_machine
	)
	{
		$this->state_compiler = $state_compiler;
		$this->state_machine = $state_machine;
	}

	/**
	 * @param Game $game
	 *
	 * @return array
	 *
	 * @throws PlayerNotFoundException
	 * @throws UnmappedCardException
	 */
	public function createCompiledStateData(Game $game): array
	{
		return $this->createStateData($game)->create();
	}

	/**
	 * @param Game $game
	 *
	 * @return StateData
	 *
	 * @throws PlayerNotFoundException
	 * @throws UnmappedCardException
	 */
	public function createStateData(Game $game): StateData
	{
		$state_data = $this->createStateDataBuilder();
		$this->fillStateData($state_data, $game);

		return $state_data;
	}

	/**
	 * @param StateData $state_data
	 * @param Game $game
	 *
	 * @throws PlayerNotFoundException
	 * @throws UnmappedCardException
	 */
	public function fillStateData(StateData $state_data, Game $game)
	{
		$state = $game->getState();

		$state_data->setGameId($game->getId())
			->setTargetScore($game->getState()->getTargetScore())
			->setFirstMeldMinimumPoints($game->getState()->getFirstMeldMinimumPoints())
			->setCurrentPlayer($state->getPlayers()->getCurrentPlayer())
			->setAllowedActions($this->createAllowedActions($game))
			->setUndrawnPool($state->getUndrawnPool())
			->setDiscardedPool($state->getDiscardedPool())
			->setPlayers($state->getPlayers()->getFreshLoopIterator());

		$this->addPlayerScores($state_data, $game);
	}

	private function createStateDataBuilder(): StateData
	{
		return new StateData($this->state_compiler);
	}

	/**
	 * @param StateData $state_data
	 * @param Game $game
	 *
	 * @throws UnmappedCardException
	 * @throws PlayerNotFoundException
	 */
	private function addPlayerScores(StateData $state_data, Game $game): void
	{
		$score = $game->getScoreCalculator()->calculateGameScore($game->getId());

		foreach ($state_data->getPlayers() as $player)
		{
			$state_data->setPlayerScore($player, $score->getTotalPlayerScore($player->getId()));
		}
	}

	/**
	 * @param Game $game
	 *
	 * @return ActionType[]
	 */
	private function createAllowedActions(Game $game): array
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