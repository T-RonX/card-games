<?php

declare(strict_types=1);

namespace App\Games\Duizenden;

use App\DeckRebuilders\DeckRebuilderType;
use App\Games\Duizenden\Player\PlayerInterface;
use App\Games\Duizenden\Player\Players;
use App\Shufflers\ShufflerType;

class Configurator
{
	private Players $players;

	private bool $initial_shuffle = true;

	private ShufflerType $initial_shuffle_algorithm;

	private PlayerInterface $first_dealer;

	private bool $is_dealer_random = true;

	private int $target_score = 1000;

	private DeckRebuilderType $deck_rebuilder_algorithm;

	private int $first_meld_minimum_points = 30;

	private int $round_finish_extra_points = 0;

	public function __construct()
	{
		$this->players = new Players();
		$this->initial_shuffle_algorithm = ShufflerType::RANDOM();
		$this->deck_rebuilder_algorithm = DeckRebuilderType::DISTINCT();
	}

	public function addPlayer(PlayerInterface $player): self
	{
		$this->players->addPlayer($player);

		return $this;
	}

	public function setPlayers(array $players): self
	{
		$this->players->setPlayers($players);

		return $this;
	}

	public function getPlayers(): Players
	{
		return $this->players;
	}

	public function getDoInitialShuffle(): bool
	{
		return $this->initial_shuffle;
	}


	public function setDoInitialShuffle(bool $shuffle): self
	{
		$this->initial_shuffle = $shuffle;

		return $this;
	}

	public function getInitialShuffleAlgorithm(): ShufflerType
	{
		return $this->initial_shuffle_algorithm;
	}

	public function setInitialShuffleAlgorithm(?ShufflerType $initial_shuffle_algorithm): self
	{
		$this->initial_shuffle_algorithm = $initial_shuffle_algorithm;

		return $this;
	}

	public function setFirstDealer(PlayerInterface $player): self
	{
		$this->first_dealer = $player;
		$this->is_dealer_random = false;

		return $this;
	}

	public function hasFirstDealer(): bool
	{
		return null !== $this->first_dealer;
	}

	public function getFirstDealer(): PlayerInterface
	{
		return $this->first_dealer;
	}

	public function setIsDealerRandom(bool $random): self
	{
		$this->is_dealer_random = $random;

		return $this;
	}

	public function getIsDealerRandom(): bool
	{
		return $this->is_dealer_random;
	}

	public function setTargetScore(int $score): self
	{
		$this->target_score = $score;

		return $this;
	}

	public function getTargetScore(): int
	{
		return $this->target_score;
	}

	public function getDeckRebuilderAlgorithm(): DeckRebuilderType
	{
		return $this->deck_rebuilder_algorithm;
	}

	public function setDeckRebuilderAlgorithm(DeckRebuilderType $deck_rebuilder_algorithm): void
	{
		$this->deck_rebuilder_algorithm = $deck_rebuilder_algorithm;
	}

	public function setFirstMeldMinimumPoints(int $points): self
	{
		$this->first_meld_minimum_points = $points;

		return $this;
	}

	public function getFirstMeldMinimumPoints(): int
	{
		return $this->first_meld_minimum_points;
	}

	public function setRoundFinishExtraPoints(int $round_finish_extra_points): self
	{
		$this->round_finish_extra_points = $round_finish_extra_points;

		return $this;
	}

	public function getRoundFinishExtraPoints(): int
	{
		return $this->round_finish_extra_points;
	}
}