<?php

namespace App\Games\Duizenden\StateCompiler;

use App\CardPool\CardPoolInterface;
use App\CardPool\Exception\EmptyCardPoolException;
use App\Common\Meld\Meld;
use App\Common\Meld\Melds;
use App\Games\Duizenden\Initializer\DiscardedCardPool;
use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;
use App\Games\Duizenden\Player\PlayerInterface;
use App\Games\Duizenden\Score\Exception\UnmappedCardException;
use App\Games\Duizenden\Score\ScoreCalculator;

class StateCompiler implements StateCompilerInterface
{
	/**
	 * @var ActionFactory
	 */
	private $action_factory;

	/**
	 * @var ScoreCalculator
	 */
	private $score_calculator;

	public function __construct(
		ActionFactory $action_factory,
		ScoreCalculator $score_calculator
	)
	{
		$this->action_factory = $action_factory;
		$this->score_calculator = $score_calculator;
	}

	/**
	 * @param StateData $state_data
	 *
	 * @return array
	 *
	 * @throws EmptyCardPoolException
	 * @throws InvalidActionException
	 * @throws PlayerNotFoundException
	 * @throws UnmappedCardException
	 */
	public function compile(StateData $state_data): array
	{
		return [
			'current_player' => $this->createPlayerIdData($state_data->getCurrentPlayer()),
			'allowed_actions' => $this->createActionsData($state_data->getAllowedActions()),
			'undrawn_pool' => $this->createCardPoolData($state_data->getUndrawnPool(), false),
			'discarded_pool' => $this->createDiscardedCardPoolData($state_data->getDiscardedPool()),
			'players' => $this->createPlayersData($state_data),
		];
	}

	/**
	 * @param CardPoolInterface $pool
	 * @param bool $show_identifiers
	 *
	 * @return string[]
	 */
	private function createCardPoolData(CardPoolInterface $pool, bool $show_identifiers): array
	{
		$data = [
			'count' => $pool->getCardCount()
		];

		$data['cards'] = [];

		foreach ($pool->getCards() as $card)
		{
			$data['cards'][] = strtolower($show_identifiers ? $card->getIdentifier() : $card->getBackColor()->getNameShort());
		}

		return $data;
	}

	/**
	 * @param DiscardedCardPool $pool
	 *
	 * @return string[]
	 *
	 * @throws EmptyCardPoolException
	 */
	private function createDiscardedCardPoolData(DiscardedCardPool $pool): array
	{
		$data = [
			'count' => $pool->getCardCount(),
			'top_card' => $pool->hasCards() ? strtolower($pool->getTopCard()->getIdentifier()) : null,
			'is_first_card' => $pool->isFirstCard()
		];

		return $data;
	}

	/**
	 * @param StateData $state_data
	 *
	 * @return string[]
	 *
	 * @throws PlayerNotFoundException
	 * @throws UnmappedCardException
	 */
	private function createPlayersData(StateData $state_data): array
	{
		$data = [];

		foreach ($state_data->getPlayers() as $player)
		{
			$data[] = $this->createPlayerData($player, $state_data);
		}

		return $data;
	}

	/**
	 * @param PlayerInterface $player
	 * @param StateData $state_data
	 *
	 * @return string[]
	 *
	 * @throws PlayerNotFoundException
	 * @throws UnmappedCardException
	 */
	private function createPlayerData(PlayerInterface $player, StateData $state_data): array
	{
		return [
			'id' => $player->getId(),
			'name' => $player->getName(),
			'hand' => $this->createCardPoolData($player->getHand(), $state_data->hasPlayerFullCardPool($player->getId())),
			'melds' => $this->createMeldsData($player->getMelds()),
			'score' => $this->createScoreData($player, $state_data)
		];
	}

	/**
	 * @param PlayerInterface $player
	 * @param StateData $state_data
	 *
	 * @return int[]
	 *
	 * @throws UnmappedCardException
	 * @throws PlayerNotFoundException
	 */
	private function createScoreData(PlayerInterface $player, StateData $state_data): array
	{
		$game_score = $this->score_calculator->calculateGameScore($state_data->getGameId());
		$round_score = $game_score->getLastRound();
		$player_score = $round_score ? $game_score->getLastRound()->getByPlayerId($player->getId()) : null;

		return [
			'meld' => $player_score ? $player_score->getMeldPoints() : 0,
			'round' => $player_score ? $player_score->getScore() : 0,
			'total' => $game_score->getTotalPlayerScore($player->getId()),
		];
	}

	/**
	 * @param Melds $melds
	 *
	 * @return string[]
	 */
	private function createMeldsData(Melds $melds): array
	{
		$data = [];

		foreach ($melds as $meld)
		{
			$data[] = $this->createMeldData($meld);
		}

		return $data;
	}

	/**
	 * @param Meld $meld
	 *
	 * @return string[]
	 */
	private function createMeldData(Meld $meld): array
	{
		return [
			'type' => $meld->getType()->getValue(),
			'cards' => $this->createCardPoolData($meld->getCards(), true),
		];
	}

	/**
	 * @param PlayerInterface $player
	 *
	 * @return string[]
	 */
	public function createPlayerIdData(PlayerInterface $player): array
	{
		return [
			'id' => $player->getId()
		];
	}

	/**
	 * @param ActionType[] $actions
	 *
	 * @return string[]
	 *
	 * @throws InvalidActionException
	 */
	private function createActionsData(array $actions): array
	{
		$data = [];

		foreach ($actions as $action)
		{
			$data[] = $this->createActionData($action);
		}

		return $data;
	}

	/**
	 * @param ActionType $action
	 *
	 * @return string[]
	 *
	 * @throws InvalidActionException
	 */
	public function createActionData(ActionType $action): array
	{
		$action = $this->action_factory->create($action);

		return [
			'id' => $action->getType()->getValue(),
			'title' => $action->getTitle(),
			'description' => $action->getDescription()
		];
	}
}