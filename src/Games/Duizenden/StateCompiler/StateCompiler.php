<?php

declare(strict_types=1);

namespace App\Games\Duizenden\StateCompiler;

use App\CardPool\CardPoolInterface;
use App\CardPool\Exception\EmptyCardPoolException;
use App\Common\Meld\Meld;
use App\Common\Meld\Melds;
use App\Games\Duizenden\Actions\ActionType;
use App\Games\Duizenden\Initializer\DiscardedCardPool;
use App\Games\Duizenden\Player\PlayerInterface;
use App\Games\Duizenden\Score\Exception\UnmappedCardException;
use App\Games\Duizenden\Score\ScoreCalculator;

class StateCompiler implements StateCompilerInterface
{
	private ActionFactory $action_factory;

	private ScoreCalculator $score_calculator;

	public function __construct(
		ActionFactory $action_factory,
		ScoreCalculator $score_calculator
	)
	{
		$this->action_factory = $action_factory;
		$this->score_calculator = $score_calculator;
	}

	/**
	 * @return string[]
	 *
	 * @throws EmptyCardPoolException
	 * @throws InvalidActionException
	 * @throws UnmappedCardException
	 */
	public function compile(StateData $state_data): array
	{
		return [
			'config' => $this->createConfigData($state_data),
			'current_player' => $this->createPlayerIdData($state_data->getCurrentPlayer()),
			'allowed_actions' => $this->createActionsData($state_data->getAllowedActions()),
			'undrawn_pool' => $this->createCardPoolData($state_data->getUndrawnPool(), false),
			'discarded_pool' => $this->createDiscardedCardPoolData($state_data->getDiscardedPool()),
			'players' => $this->createPlayersData($state_data),
			'score' => $this->createGameScoreData($state_data)
		];
	}

	private function createConfigData(StateData $state_data): array
	{
		return [
			'target_score' => $state_data->getTargetScore(),
			'first_meld_minimum_points' => $state_data->getFirstMeldMinimumPoints(),
			'round_finish_extra_points' => $state_data->getRoundFinishExtraPoints(),
		];
	}

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
	 * @throws EmptyCardPoolException
	 */
	private function createDiscardedCardPoolData(DiscardedCardPool $pool): array
	{
		return [
			'count' => $pool->getCardCount(),
			'top_card' => $pool->hasCards() ? strtolower($pool->getTopCard()->getIdentifier()) : null,
			'is_first_card' => $pool->isFirstCard()
		];
	}

	private function createPlayersData(StateData $state_data): array
	{
		$data = [];

		foreach ($state_data->getPlayers() as $player)
		{
			$data[] = $this->createPlayerData($player, $state_data);
		}

		return $data;
	}

	private function createPlayerData(PlayerInterface $player, StateData $state_data): array
	{
		return [
			'id' => $player->getId(),
			'name' => $player->getName(),
			'hand' => $this->createCardPoolData($player->getHand(), $state_data->hasPlayerFullCardPool($player->getId())),
			'melds' => $this->createMeldsData($player->getMelds()),
		];
	}

	/**
	 * @throws UnmappedCardException
	 */
	private function createGameScoreData(StateData $state_data): array
	{
		return [
			'past_rounds' => $this->createRoundScoreData($state_data),
			'current_round' => $this->createCurrentRoundData($state_data),
		];
	}

	/**
	 * @throws UnmappedCardException
	 */
	private function createRoundScoreData(StateData $state_data): array
	{
		$game_score = $this->score_calculator->calculateGameScore($state_data->getGameId(), $state_data->getRoundFinishExtraPoints());

		$rounds = [];
		$n = 0;

		foreach ($game_score->getRoundScores() as $round_score)
		{
			foreach ($round_score->getPlayerScores() as $player_score)
			{
				$rounds[$n][$player_score->getPlayerId()] = [
					'finished_round' => $player_score->isRoundFinisher(),
					'round_finish_extra_points' => $player_score->isRoundFinisher() ? $player_score->getRoundFinishExtraPoints() : 0,
					'score' => $player_score->getMeldPoints(),
					'hand' => $player_score->getHandPoints(),
				];
			}

			++$n;
		}

		return $rounds;
	}

	/**
	 * @throws UnmappedCardException
	 */
	private function createCurrentRoundData(StateData $state_data): array
	{
		$current = [];

		foreach ($state_data->getPlayers() as $player)
		{
			$current[$player->getId()] = [
				'meld' => $this->score_calculator->calculatePlayerMeldsScore($player),
			];
		}

		return $current;
	}

	private function createMeldsData(Melds $melds): array
	{
		$data = [];

		foreach ($melds as $meld)
		{
			$data[] = $this->createMeldData($meld);
		}

		return $data;
	}

	private function createMeldData(Meld $meld): array
	{
		return [
			'type' => $meld->getType()->getValue(),
			'cards' => $this->createCardPoolData($meld->getCards(), true),
		];
	}

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

	public function createExtrasData(?array $extra): array
	{
		return $extra ?: [];
	}
}