<?php

namespace App\Shufflers\Overhand;

use App\Deck\Card\CardInterface;
use App\Shuffler\ShufflerInterface;

class OverhandShuffle implements ShufflerInterface
{
	/**
	 * Shuffle at least this many times.
	 *
	 * @var int
	 */
	private $shuffle_count_min = 3;

	/**
	 * Shuffle at most this many times.
	 *
	 * @var int
	 */
	private $shuffle_count_max = null;

	/**
	 * Grab at at this percentage of cards from the pool.
	 *
	 * @var float
	 */
	private $grab_cards_min = .1;

	/**
	 * Grab at most this percentage of cards from the pool.
	 *
	 * @var float
	 */
	private $grab_cards_max = .65;

	/**
	 * Maximum number of slices to add the cards grabbed from the back of the pool back into the front of the pool.
	 *
	 * @var int
	 */
	private $max_inserts = null;

	/**
	 * Generate shuffling algorithm.
	 */
	public function __construct()
	{
		$this->generateRandomVariation();
	}

	/**
	 * Generate some variation in the shuffle algorithm.
	 */
	private function generateRandomVariation(): void
	{
		$this->shuffle_count_min += rand(0, 5);
		$this->shuffle_count_max = $this->shuffle_count_min + 3;
		$this->grab_cards_min += (rand(0, 20) / 100);
		$this->grab_cards_max -= (rand(0, 30) / 100);
		$this->max_inserts = rand(3, 5);
	}

	/**
	 * @inheritDoc
	 */
	function shuffle(array $cards): array
	{
		$shuffle_count = rand($this->shuffle_count_min, $this->shuffle_count_max);

		for ($i = 0; $i < $shuffle_count; ++$i)
		{
			$grab = $this->grabCards($cards);

			array_splice($cards, 0, count($grab));
			$cards = $this->insertCards($cards, $grab);
		}

		return $cards;
	}

	/**
	 * Represents the initial pickup of cards from the back of the pool.
	 *
	 * @param CardInterface[] $cards
	 *
	 * @return CardInterface[]
	 */
	private function grabCards(array $cards): array
	{
		$num_cards = count($cards);
		$range_min = ceil($num_cards * $this->grab_cards_min);
		$range_max = ceil($num_cards * $this->grab_cards_max);

		$range = $this->distributeRangeProbability($range_min, $range_max);
		$grab = $this->getRandomCardCountFromProbabilityRange($range);

		return array_splice($cards, 0, $grab);
	}

	/***
	 * Gets a random number, based on the distribution of probability, representing the amount of cards to grab from the back of the pool.
	 *
	 * @param int[] $range
	 *
	 * @return int
	 */
	private function getRandomCardCountFromProbabilityRange(array $range): int
	{
		$random = rand(1, 100) / 100;

		foreach ($range as $cards => $probability)
		{
			if ($random > $probability)
			{
				return $cards;
			}
		}

		end($range);

		return key($range);
	}

	/**
	 * Generates a linear probability distribution between 1 and 0.
	 * Cards lower in the pool representing by $range_min have a higher probability,
	 * Cards higher in the pool representing by $range_max have a lower probability,
	 *
	 * @param int $range_min
	 * @param int $range_max
	 *
	 * @return int[]
	 */
	private function distributeRangeProbability(int $range_min, int $range_max): array
	{
		$range = [];
		$step = 100 / max($range_max - $range_min, 1);

		for ($i = 0; $i < max($range_max - $range_min, 1); ++$i)
		{
			$range[$range_min + $i] = round(($step * (($range_max - $range_min) - $i)) / 100, 2);
		}

		return $range;
	}

	/**
	 * Inserts the cards grabbed from the back of the pool to the from of the pool in several random steps.
	 *
	 * @param CardInterface[] $cards
	 * @param CardInterface[] $grab
	 *
	 * @return CardInterface[]
	 */
	private function insertCards(array $cards, array $grab): array
	{
		$inserts = $this->getInsertsSlices($grab);

		foreach ($inserts as $insert_index)
		{
			$insert_in_cards = array_splice($grab, $insert_index);
			$cards = array_merge($cards, $insert_in_cards);
		}

		return $cards;
	}

	/**
	 * Gets an array in index values from the input array representing the slices to insert in the pool.
	 *
	 * @param CardInterface[] $grab
	 *
	 * @return int[]
	 */
	private function getInsertsSlices(array $grab): array
	{
		$inserts = [0];
		$grab_size = count($grab);
		$min_insert_index = min(1, $grab_size - 1);

		for ($n = rand(1, $this->max_inserts - 1), $i = 0; $i < $n; ++$i)
		{
			$inserts[] = rand($min_insert_index, $grab_size - 1);
		}

		rsort($inserts);
		$inserts = array_unique($inserts);

		return $inserts;
	}

	/**
	 * @return int
	 */
	public function getShuffleCountMin(): int
	{
		return $this->shuffle_count_min;
	}

	/**
	 * @param int $shuffle_count_min
	 *
	 * @return self
	 */
	public function setShuffleCountMin(int $shuffle_count_min): self
	{
		$this->shuffle_count_min = $shuffle_count_min;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getShuffleCountMax(): int
	{
		return $this->shuffle_count_max;
	}

	/**
	 * @param int $shuffle_count_max
	 *
	 * @return self
	 */
	public function setShuffleCountMax(int $shuffle_count_max): self
	{
		$this->shuffle_count_max = $shuffle_count_max;

		return $this;
	}

	/**
	 * @return float
	 */
	public function getGrabCardsMin(): float
	{
		return $this->grab_cards_min;
	}

	/**
	 * @param float $grab_cards_min
	 *
	 * @return self
	 */
	public function setGrabCardsMin(float $grab_cards_min): self
	{
		$this->grab_cards_min = $grab_cards_min;

		return $this;
	}

	/**
	 * @return float
	 */
	public function getGrabCardsMax(): float
	{
		return $this->grab_cards_max;
	}

	/**
	 * @param float $grab_cards_max
	 *
	 * @return self
	 */
	public function setGrabCardsMax(float $grab_cards_max): self
	{
		$this->grab_cards_max = $grab_cards_max;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getMaxInserts(): int
	{
		return $this->max_inserts;
	}

	/**
	 * @param int $max_inserts
	 *
	 * @return self
	 */
	public function setMaxInserts(int $max_inserts): self
	{
		$this->max_inserts = $max_inserts;

		return $this;
	}
}