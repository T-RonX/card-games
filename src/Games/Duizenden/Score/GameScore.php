<?php

namespace App\Games\Duizenden\Score;

use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;

class GameScore
{
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
	 * @param array $round_scores
	 */
	public function setRoundScore(array $round_scores): void
	{
		$this->round_scores = $round_scores;
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
			$score += $player_score->getScore();
		}

		return $score;
	}

	public function getLastRound(): ?RoundScore
	{
		return $this->round_scores ? end($this->round_scores) : null;
	}
}