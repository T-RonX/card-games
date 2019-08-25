<?php

namespace App\CardPool;

use App\CardPool\Exception\CardNotFoundException;
use App\CardPool\Exception\EmptyCardPoolException;
use App\Deck\Card\CardInterface;
use App\Shuffler\ShufflerInterface;
use Countable;

class CardPool implements CardPoolInterface, Countable
{
	/**
	 * @inheritDoc
	 */
	private $cards = [];

	/**
	 * @var int
	 */
	private $pointer;

	/**
	 * @param array|null $cards
	 * @param int $pointer
	 */
	public function __construct(array $cards = [], int $pointer = 0)
	{
		$this->cards = $cards;
		$this->pointer = $pointer;
	}

	/**
	 * @inheritDoc
	 */
	function clear(): void
	{
		$this->cards = [];
	}

	/**
	 * @inheritDoc
	 */
	function setCards(array $cards): void
	{
		$this->cards = $cards;
	}

	/**
	 * @inheritDoc
	 */
	function addCards(array $cards): void
	{
		$this->cards = array_merge($this->cards, $cards);
	}

	/**
	 * @inheritDoc
	 */
	function addCard(CardInterface $card): void
	{
		$this->cards[] = $card;
	}

	/**
	 * @inheritDoc
	 */
	function getCardCount(): int
	{
		return count($this->cards);
	}

	/**
	 * @inheritDoc
	 *
	 * @return CardInterface[]
	 */
	function getCards(): array
	{
		return $this->cards;
	}

	/**
	 * @inheritDoc
	 */
	public function shuffle(ShufflerInterface $shuffler): void
	{
		$this->cards = $shuffler->shuffle($this->cards);
	}

	/**
	 * @return CardInterface
	 *
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
	 * @inheritDoc
	 *
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

	/**
	 * @inheritDoc
	 */
	public function drawAllCards(): array
	{
		$cards = $this->cards;
		$this->clear();

		return $cards;
	}

	/**
	 * @@inheritDoc
	 *
	 * @throws CardNotFoundException
	 */
	public function drawCard(CardInterface $card): CardInterface
	{
		/** @var CardInterface $own_card */
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

	/**
	 * @return bool
	 */
	public function hasCards(): bool
	{
		return $this->count() > 0;
	}

	/**
	 * @inheritDoc
	*/
	public function current()
	{
		return $this->cards[$this->pointer];
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
		return array_key_exists($this->pointer, $this->cards);
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
		return count($this->cards);
	}
}