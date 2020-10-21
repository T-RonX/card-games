<?php

declare(strict_types=1);

namespace App\Games\Duizenden\StateBuilder;

use App\Games\Duizenden\Game;
use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;
use App\Games\Duizenden\Score\Exception\UnmappedCardException;
use App\Games\Duizenden\Actions\ActionType;
use App\Games\Duizenden\StateCompiler\StateCompiler;
use App\Games\Duizenden\StateCompiler\StateData;
use App\Games\Duizenden\Workflow\TransitionType;
use Symfony\Component\Workflow\StateMachine;

class StateBuilder
{
	private StateCompiler $state_compiler;
	private StateMachine $state_machine;
	private AllowedActions $allowed_actions;

	public function __construct(
		StateCompiler $state_compiler,
		StateMachine $state_machine,
		AllowedActions $allowed_actions
	)
	{
		$this->state_compiler = $state_compiler;
		$this->state_machine = $state_machine;
		$this->allowed_actions = $allowed_actions;
	}

	/**
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
	 * @throws PlayerNotFoundException
	 * @throws UnmappedCardException
	 */
	public function fillStateData(StateData $state_data, Game $game): void
	{
		$state = $game->getState();

		$state_data->setGameId($game->getId())
			->setTargetScore($game->getState()->getTargetScore())
			->setFirstMeldMinimumPoints($game->getState()->getFirstMeldMinimumPoints())
			->setRoundFinishExtraPoints($game->getState()->getRoundFinishExtraPoints())
			->setCurrentPlayer($state->getPlayers()->getCurrentPlayer())
			->setAllowedActions($this->allowed_actions->getAllowedActions($game))
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
	 * @throws UnmappedCardException
	 * @throws PlayerNotFoundException
	 */
	private function addPlayerScores(StateData $state_data, Game $game): void
	{
		$score = $game->getScoreCalculator()->calculateGameScore($game->getId(), $game->getState()->getRoundFinishExtraPoints());

		foreach ($state_data->getPlayers() as $player)
		{
			$state_data->setPlayerScore($player, $score->getTotalPlayerScore($player->getId()));
		}
	}
}