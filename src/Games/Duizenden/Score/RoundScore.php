<?php

namespace App\Games\Duizenden\Score;

use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;

class RoundScore
{
	/**
	 * @var PlayerScore[]
	 */
	private $player_scores = [];

	/**
	 * @param PlayerScore $player_score
	 */
	public function addPlayerScore(PlayerScore $player_score): void
	{
		$this->player_scores[$player_score->getPlayerId()] = $player_score;
	}

	/**
	 * @return PlayerScore[]
	 */
	public function getPlayerScores(): array
	{
		return $this->player_scores;
	}

	/**
	 * @param string $id
	 *
	 * @return PlayerScore
	 *
	 * @throws PlayerNotFoundException
	 */
	public function getByPlayerId(string $id): PlayerScore
	{
		if (!array_key_exists($id, $this->player_scores))
		{
			throw new PlayerNotFoundException("Player with id '%s' was not present in the round score.");
		}

		return $this->player_scores[$id];
	}
}