<?php

namespace App\Games\Duizenden\Score;

use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;

class RoundScore
{
	/**
	 * @var PlayerScore[]
	 */
	private $player_score = [];

	/**
	 * @param PlayerScore $player_score
	 */
	public function addPlayerScore(PlayerScore $player_score): void
	{
		$this->player_score[$player_score->getPlayerId()] = $player_score;
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
		if (!array_key_exists($id, $this->player_score))
		{
			throw new PlayerNotFoundException("Player with id '%s' was not present in the round score.");
		}

		return $this->player_score[$id];
	}
}