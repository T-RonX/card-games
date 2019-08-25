<?php

namespace App\Common\Meld;

use App\CardPool\CardPool;
use App\Deck\Card\CardInterface;

class Meld
{
	/**
	 * @var MeldType
	 */
	private $meld_type;

	/**
	 * @var CardPool
	 */
	private $cards;

	/**
	 * @param CardPool $cards
	 * @param MeldType|null $meld_type
	 */
	public function __construct(CardPool $cards = null, MeldType $meld_type = null)
	{
		$this->cards = $cards ?? new CardPool();
		$this->meld_type = $meld_type;
	}

	/**
	 * @param MeldType $type
	 */
	public function setMeldType(MeldType $type): void
	{
		$this->meld_type = $type;
	}

	/**
	 * @return MeldType|null
	 */
	public function getType(): ?MeldType
	{
		return $this->meld_type;
	}

	/**
	 * @return CardPool
	 */
	public function getCards(): CardPool
	{
		return $this->cards;
	}

	/**
	 * @param CardPool $cards
	 */
	public function setCards(CardPool $cards): void
	{
		$this->cards = $cards;
	}

	/**
	 * @param CardInterface $card
	 */
	public function addCard(CardInterface $card): void
	{
		$this->cards->addCard($card);
	}
}