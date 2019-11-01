<?php

namespace App\Common\Meld;

use App\Deck\Card\CardInterface;
use Countable;
use Iterator;

class Melds implements Iterator, Countable
{
	/**
	 * @var Meld[]
	 */
	private $melds = [];

	/**
	 * @var int
	 */
	private $pointer = 0;

	/**
	 * @param Meld $meld
	 */
	public function addMeld(Meld $meld): void
	{
		$this->melds[] = $meld;
	}

	/**
	 * @param Meld[] $melds
	 */
	public function setMelds(array $melds): void
	{
		$this->melds = $melds;
	}

	/**
	 * @param int $index
	 *
	 * @return Meld
	 */
	public function get(int $index): Meld
	{
		if (!array_key_exists($index, $this->melds))
		{
			throw new \Exception("no such meld");
		}

		return $this->melds[$index];
	}

	/**
	 * Clears all melds.
	 */
	public function clear(): void
	{
		$this->melds = [];
	}

	/**
	 * @return CardInterface[]
	 */
	public function drawAllCardsAndClearMelds(): array
	{
		$cards = [];

		foreach ($this as $meld)
		{
			$cards = array_merge($cards, $meld->getCards()->drawAllCards());
		}

		$this->clear();

		return $cards;
	}

	/**
	 * @inheritDoc
	 */
	public function current()
	{
		return $this->melds[$this->pointer];
	}

	/**
	 * @inheritDoc
	 */
	public function next()
	{
		++$this->pointer;
	}

	/**
	 * @inheritDoc
	 */
	public function key()
	{
		return $this->pointer;
	}

	/**
	 * @inheritDoc
	 */
	public function valid()
	{
		return array_key_exists($this->pointer, $this->melds);
	}

	/**
	 * @inheritDoc
	 */
	public function rewind()
	{
		$this->pointer = 0;
	}

	/**
	 * @inheritDoc
	 */
	public function count()
	{
		return count($this->melds);
	}

	public function last(): Meld
	{
		return end($this->melds);
	}
}