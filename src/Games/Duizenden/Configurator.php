<?php

namespace App\Games\Duizenden;

use App\DeckRebuilders\DeckRebuilderType;
use App\Games\Duizenden\Player\PlayerInterface;
use App\Games\Duizenden\Player\Players;
use App\Shufflers\ShufflerType;

class Configurator
{
	/**
	 * @var Players
	 */
	private $players;

	/**
	 * @var bool
	 */
	private $initial_shuffle = true;

	/**
	 * @var ShufflerType
	 */
	private $initial_shuffle_algorithm;

	/**
	 * @var PlayerInterface
	 */
	private $first_dealer;

	/**
	 * @var bool
	 */
	private $is_dealer_random = true;

	/**
	 * @var int
	 */
	private $target_score = 1000;

	/**
	 * @var DeckRebuilderType
	 */
	private $deck_rebuilder_algorithm;

	/**
	 * @var int
	 */
	private $first_meld_minimum_points = 30;

	/**
	 * @var int
	 */
	private $round_finish_extra_points = 0;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->players = new Players();
		$this->initial_shuffle_algorithm = ShufflerType::RANDOM();
		$this->deck_rebuilder_algorithm = DeckRebuilderType::DISTINCT();
	}

	/**
	 * @param PlayerInterface $player
	 *
	 * @return self
	 */
	public function addPlayer(PlayerInterface $player): self
	{
		$this->players->addPlayer($player);

		return $this;
	}

	/**
	 * @param PlayerInterface[] $players
	 *
	 * @return self
	 */
	public function setPlayers(array $players): self
	{
		$this->players->setPlayers($players);

		return $this;
	}

	/**
	 * @return Players
	 */
	public function getPlayers(): Players
	{
		return $this->players;
	}

	/**
	 * @return bool
	 */
	public function getDoInitialShuffle(): bool
	{
		return $this->initial_shuffle;
	}
	/**
	 * @param bool $shuffle
	 *
	 * @return self
	 */
	public function setDoInitialShuffle(bool $shuffle): self
	{
		$this->initial_shuffle = $shuffle;

		return $this;
	}

	/**
	 * @return ShufflerType
	 */
	public function getInitialShuffleAlgorithm(): ShufflerType
	{
		return $this->initial_shuffle_algorithm;
	}

	/**
	 * @param ShufflerType $initial_shuffle_algorithm
	 *
	 * @return self
	 */
	public function setInitialShuffleAlgorithm(?ShufflerType $initial_shuffle_algorithm): self
	{
		$this->initial_shuffle_algorithm = $initial_shuffle_algorithm;

		return $this;
	}

	/**
	 * @param PlayerInterface $player
	 *
	 * @return self
	 */
	public function setFirstDealer(PlayerInterface $player): self
	{
		$this->first_dealer = $player;
		$this->is_dealer_random = false;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function hasFirstDealer(): bool
	{
		return null !== $this->first_dealer;
	}

	/**
	 * @return PlayerInterface
	 */
	public function getFirstDealer(): PlayerInterface
	{
		return $this->first_dealer;
	}

	/**
	 * @param bool $random
	 *
	 * @return self
	 */
	public function setIsDealerRandom(bool $random): self
	{
		$this->is_dealer_random = $random;

		return $this;
	}

	/**
	 * @return bool
	 */
	public function getIsDealerRandom(): bool
	{
		return $this->is_dealer_random;
	}

	/**
	 * @param int $score
	 *
	 * @return self
	 */
	public function setTargetScore(int $score): self
	{
		$this->target_score = $score;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getTargetScore(): int
	{
		return $this->target_score;
	}

	/**
	 * @return DeckRebuilderType
	 */
	public function getDeckRebuilderAlgorithm(): DeckRebuilderType
	{
		return $this->deck_rebuilder_algorithm;
	}

	/**
	 * @param DeckRebuilderType $deck_rebuilder_algorithm
	 */
	public function setDeckRebuilderAlgorithm(DeckRebuilderType $deck_rebuilder_algorithm): void
	{
		$this->deck_rebuilder_algorithm = $deck_rebuilder_algorithm;
	}

	/**
	 * @param int $points
	 *
	 * @return Configurator
	 */
	public function setFirstMeldMinimumPoints(int $points): self
	{
		$this->first_meld_minimum_points = $points;

		return $this;
	}

	/**
	 * @return int
	 */
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