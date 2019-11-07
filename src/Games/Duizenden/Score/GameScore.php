<?php

namespace App\Games\Duizenden\Score;

use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;

class GameScore
{
	/**
	 * @var int
	 */
	private $round_finish_extra_points;

	public function __construct(int $round_finish_extra_points)
	{
		$this->round_finish_extra_points = $round_finish_extra_points;
	}

	/**
	 * @var RoundScore[]
	 */
	private $round_scores = [];

	/**
	 * @param RoundScore $round_score
	 */
	public function addRoundScore(RoundScore $round_score): void
	{
		$this->round_scores[] = $round_score;
	}

	/**
	 * @param RoundScore[] $round_scores
	 */
	public function setRoundScores(array $round_scores): void
	{
		$this->round_scores = $round_scores;
	}

	/**
	 * @return RoundScore[]
	 */
	public function getRoundScores(): array
	{
		return $this->round_scores;
	}

	/**
	 * @param string $player_id
	 *
	 * @return int
	 *
	 * @throws PlayerNotFoundException
	 */
	public function getTotalPlayerScore(string $player_id): int
	{
		$score = 0;

		foreach ($this->round_scores as $round_score)
		{
			$player_score = $round_score->getByPlayerId($player_id);
			$score += $player_score->getScore() + ($player_score->isRoundFinisher() ? $this->round_finish_extra_points : 0);
		}

		return $score;
	}
}