<?php

namespace App\Games\Duizenden\Score;

use App\CardPool\CardPool;
use App\Deck\Card\CardInterface;
use App\Games\Duizenden\Player\PlayerInterface;
use App\Games\Duizenden\Repository\GamePlayerRepository;
use App\Games\Duizenden\Repository\GameRepository;
use App\Games\Duizenden\Score\Exception\UnmappedCardException;

class ScoreCalculator
{
	/**
	 * @var GameRepository
	 */
	private $game_player_repository;

	/**
	 * @param GamePlayerRepository $game_player_repository
	 */
	public function __construct(GamePlayerRepository $game_player_repository)
	{
		$this->game_player_repository = $game_player_repository;
	}

	/**
	 * @param string $uuid
	 *
	 * @return GameScore
	 *
	 * @throws UnmappedCardException
	 */
	public function calculateGameScore(string $uuid): GameScore
	{
		$data = $this->game_player_repository->getGameScoreData($uuid);

		$game_score = new GameScore();
		$round_scores = [];

		foreach ($data as ['player_uuid' => $player_uuid, 'game_id' => $game_id, 'hand' => $hand, 'melds' => $melds])
		{
			$hand = json_decode($hand);
			$melds = json_decode($melds);

			if (!array_key_exists($game_id, $round_scores))
			{
				$round_scores[$game_id] = new RoundScore();
			}

			$meld_points = 0;

			foreach ($melds as $meld)
			{
				$meld_points += $this->calculateCardIdArray($meld);
			}

			$round_scores[$game_id]->addPlayerScore(new PlayerScore($player_uuid, $meld_points, $this->calculateCardIdArray($hand)));
		}

		$game_score->setRoundScores($round_scores);

		return $game_score;
	}

	/**
	 * @param PlayerInterface $player
	 *
	 * @return PlayerScore
	 *
	 * @throws UnmappedCardException
	 */
	public function calculatePlayerRoundScore(PlayerInterface $player): PlayerScore
	{
		$hand_points = $this->calculateCardPool($player->getHand());
		$meld_points = 0;

		foreach ($player->getMelds() as $meld)
		{
			$meld_points += $this->calculateCardPool($meld->getCards());
		}

		return new PlayerScore($player->getId(), $meld_points, $hand_points);
	}

	/**
	 * @param PlayerInterface $player
	 *
	 * @return int
	 *
	 * @throws UnmappedCardException
	 */
	public function calculatePlayerMeldsScore(PlayerInterface $player): int
	{
		$score = 0;

		foreach ($player->getMelds() as $meld)
		{
			$score += $this->calculateCardPool($meld->getCards());
		}

		return $score;
	}

	/**
	 * @param CardPool $card_pool
	 *
	 * @return int
	 *
	 * @throws UnmappedCardException
	 */
	private function calculateCardPool(CardPool $card_pool): int
	{
		$points = 0;

		foreach ($card_pool as $card)
		{
			$points += PointMapping::getPointsByCard($card);
		}

		return $points;
	}

	/**
	 * @param string[] $card_ids
	 *
	 * @return int
	 *
	 * @throws UnmappedCardException
	 */
	private function calculateCardIdArray(array $card_ids): int
	{
		$points = 0;

		foreach ($card_ids as $card_id)
		{
			$points += PointMapping::getPointsByCardId($card_id);
		}

		return $points;
	}

	/**
	 * @param CardInterface[] $cards
	 *
	 * @return int
	 *
	 * @throws UnmappedCardException
	 */
	public function calculateCardScore(array $cards): int
	{
		$points = 0;

		foreach ($cards as $card)
		{
			$points += PointMapping::getPointsByCard($card);

		}

		return $points;
	}
}