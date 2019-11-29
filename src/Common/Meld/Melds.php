<?php

declare(strict_types=1);

namespace App\Common\Meld;

use App\Deck\Card\CardInterface;
use Countable;
use Exception;
use Iterator;

class Melds implements Iterator, Countable
{
	/**
	 * @var Meld[]
	 */
	private array $melds = [];

	private int $pointer = 0;

	public function addMeld(Meld $meld): void
	{
		$this->melds[] = $meld;
	}

	public function setMelds(array $melds): void
	{
		$this->melds = $melds;
	}

	/**
	 * @throws Exception
	 */
	public function get(int $index): Meld
	{
		if (!array_key_exists($index, $this->melds))
		{
			throw new Exception("no such meld");
		}

		return $this->melds[$index];
	}

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
			$cards = [...$cards, ...$meld->getCards()->drawAllCards()];
		}

		$this->clear();

		return $cards;
	}

	public function current(): Meld
	{
		return $this->melds[$this->pointer];
	}

	public function next(): void
	{
		++$this->pointer;
	}

	public function key(): int
	{
		return $this->pointer;
	}

	public function valid(): bool
	{
		return array_key_exists($this->pointer, $this->melds);
	}

	public function rewind(): void
	{
		$this->pointer = 0;
	}

	public function count(): int
	{
		return count($this->melds);
	}

	public function last(): Meld
	{
		return end($this->melds);
	}
}