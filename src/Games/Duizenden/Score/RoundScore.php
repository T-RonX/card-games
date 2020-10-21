<?php

declare(strict_types=1);

namespace App\Games\Duizenden\Score;

use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;

class RoundScore
{
	/**
	 * @var PlayerScore[]
	 */
	private array $player_scores = [];

	public function addPlayerScore(PlayerScore $player_score): void
	{
		$this->player_scores[$player_score->getPlayerId()] = $player_score;
	}

	/**
	 * @param PlayerScore[] $player_scores
	 */
	public function setPlayerScores(array $player_scores): void
	{
		$this->player_scores = $player_scores;
	}

	/**
	 * @return PlayerScore[]
	 */
	public function getPlayerScores(): array
	{
		return $this->player_scores;
	}

	/**
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