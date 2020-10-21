<?php

declare(strict_types=1);

namespace App\Games\Duizenden\Score;

class GameScoreCloner
{
	public function cloneGameScore(GameScore $score): GameScore
	{
		$round_finish_extra_points = $score->getRoundFinishExtraPoints();
		$round_scores = $this->cloneRoundScores($score->getRoundScores());

		$c = new GameScore($round_finish_extra_points);
		$c->setRoundScores($round_scores);

		return $c;
	}

	/**
	 * @param RoundScore[] $round_scores
	 * @return RoundScore[]
	 */
	private function cloneRoundScores(array $round_scores): array
	{
		$c = [];

		foreach ($round_scores as $round_score)
		{
			$c[] = $this->cloneRoundScore($round_score);
		}

		return $c;
	}

	private function cloneRoundScore(RoundScore $round_score): RoundScore
	{
		$player_scores = $this->clonePlayerScores($round_score->getPlayerScores());

		$c = new RoundScore();
		$c->setPlayerScores($player_scores);

		return $c;
	}

	/**
	 * @param PlayerScore[] $player_scores
	 * @return PlayerScore[]
	 */
	private function clonePlayerScores(array $player_scores): array
	{
		$c = [];

		foreach ($player_scores as $player_score)
		{
			$c[] = $this->clonePlayerScore($player_score);
		}

		return $c;
	}

	private function clonePlayerScore(PlayerScore $player_score): PlayerScore
	{
		return new PlayerScore(
			$player_score->getPlayerId(),
			$player_score->getMeldPoints(),
			$player_score->getHandPoints(),
			$player_score->isRoundFinisher(),
			$player_score->getRoundFinishExtraPoints(),
		);
	}
}