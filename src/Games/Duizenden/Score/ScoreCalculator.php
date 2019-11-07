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
	 * @var GameRepository
	 */
	private $game_repository;

	/**
	 * @param GameRepository $game_repository
	 * @param GamePlayerRepository $game_player_repository
	 */
	public function __construct(
		GameRepository $game_repository,
		GamePlayerRepository $game_player_repository
	)
	{
		$this->game_repository = $game_repository;
		$this->game_player_repository = $game_player_repository;
	}

	/**
	 * @param string $game_id
	 * @param int $round_finish_extra_points
	 *
	 * @return GameScore
	 *
	 * @throws UnmappedCardException
	 */
	public function calculateGameScore(string $game_id, int $round_finish_extra_points): GameScore
	{
		$data = $this->game_player_repository->getGameScoreData($game_id);

		$finishers = $this->getRoundFinishersByScoreData($game_id, $data);

		$game_score = new GameScore($round_finish_extra_points);
		$round_scores = [];

		foreach ($data as ['round' => $round, 'player_uuid' => $player_uuid, 'hand' => $hand, 'melds' => $melds])
		{
			$hand = json_decode($hand);
			$melds = json_decode($melds);

			if (!array_key_exists($round, $round_scores))
			{
				$round_scores[$round] = new RoundScore();
			}

			$meld_points = 0;

			foreach ($melds as $meld)
			{
				$meld_points += $this->calculateCardIdArray($meld);
			}

			$is_round_finisher = $player_uuid === $finishers[$round]['player_id'];

			$player_score = new PlayerScore(
				$player_uuid,
				$meld_points,
				$this->calculateCardIdArray($hand),
				$is_round_finisher,
				$round_finish_extra_points,
			);

			$round_scores[$round]->addPlayerScore($player_score);
		}

		$game_score->setRoundScores($round_scores);

		return $game_score;
	}

	private function getRoundFinishersByScoreData(string $game_id, array $data): array
	{
		$sequences = [];

		foreach ($data as $item)
		{
			$sequences[] = $item['sequence'] - 1;
		}

		$finishers = $this->game_repository->getPlayersBySequences($game_id, $sequences);

		return $finishers;
	}

	/**
	 * @param PlayerInterface $player
	 * @param bool $is_round_finisher
	 * @param int $round_finish_extra_points
	 *
	 * @return PlayerScore
	 *
	 * @throws UnmappedCardException
	 */
	public function calculatePlayerRoundScore(PlayerInterface $player, bool $is_round_finisher, int $round_finish_extra_points): PlayerScore
	{
		$hand_points = $this->calculateCardPool($player->getHand());
		$meld_points = 0;

		foreach ($player->getMelds() as $meld)
		{
			$meld_points += $this->calculateCardPool($meld->getCards());
		}

		return new PlayerScore($player->getId(), $meld_points, $hand_points, $is_round_finisher, $round_finish_extra_points);
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