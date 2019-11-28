<?php

declare(strict_types=1);

namespace App\CardPool;

use App\CardPool\Exception\CardNotFoundException;
use App\CardPool\Exception\EmptyCardPoolException;
use App\Deck\Card\CardInterface;
use App\Shuffler\ShufflerInterface;
use Countable;

class CardPool implements CardPoolInterface, Countable
{
	/**
	 * @var CardInterface[]
	 */
	private array $cards = [];

	private int $pointer;

	public function __construct(array $cards = [], int $pointer = 0)
	{
		$this->cards = $cards;
		$this->pointer = $pointer;
	}

	function clear(): void
	{
		$this->cards = [];
	}

	function setCards(array $cards): void
	{
		$this->cards = $cards;
	}

	function addCards(array $cards): void
	{
		$this->cards = array_merge($this->cards, $cards);
	}

	function addCard(CardInterface $card, int $target = null): void
	{
		$index = null === $target ? $this->getCardCount() : $target;
		array_splice($this->cards, $index, 0, [$card]);
	}

	function getCardCount(): int
	{
		return count($this->cards);
	}

	function getCards(): array
	{
		return $this->cards;
	}

	public function shuffle(ShufflerInterface $shuffler): void
	{
		$this->cards = $shuffler->shuffle($this->cards);
	}

	/**
	 * @throws EmptyCardPoolException
	 */
	public function drawTopCard(): CardInterface
	{
		if (!$this->hasCards())
		{
			throw new EmptyCardPoolException("Can not draw top card, pool is empty.");
		}

		return array_pop($this->cards);
	}

	/**
	 * @throws EmptyCardPoolException
	 */
	public function getTopCard(): CardInterface
	{
		if (!$this->hasCards())
		{
			throw new EmptyCardPoolException("Can not get top card, pool is empty.");
		}

		end($this->cards);

		return current($this->cards);
	}

	public function drawAllCards(): array
	{
		$cards = $this->cards;
		$this->clear();

		return $cards;
	}

	/**
	 * @throws CardNotFoundException
	 */
	public function drawCard(CardInterface $card): CardInterface
	{
		foreach ($this->cards as $index => $own_card)
		{
			if ($own_card->equals($card))
			{
				$card = $this->cards[$index];
				unset($this->cards[$index]);
				$this->cards = array_values($this->cards);

				return $card;
			}
		}

		throw new CardNotFoundException(
			sprintf("Can not draw card since there is no card with rank '%s' from suit '%s' in the pool.",
				$card->getRank()->getName(),
				$card->getSuit()->getSymbol()
			)
		);
	}

	public function hasCards(): bool
	{
		return $this->count() > 0;
	}

	/**
	 * @return string[]
	 */
	public function getIdentifiers(): array
	{
		$return = [];
		$cards = $this->cards;

		foreach ($cards as $card)
		{
			$return[] = $card->getIdentifier();
		}

		return $return;
	}

	public function current(): CardInterface
	{
		return $this->cards[$this->pointer];
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
		return array_key_exists($this->pointer, $this->cards);
	}

	public function rewind(): void
	{
		$this->pointer = 0;
	}

	public function count(): int
	{
		return count($this->cards);
	}
}