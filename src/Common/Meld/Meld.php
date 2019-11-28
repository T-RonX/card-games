<?php

declare(strict_types=1);

namespace App\Common\Meld;

use App\CardPool\CardPool;
use App\Deck\Card\CardInterface;

class Meld
{
	private MeldType $meld_type;

	private CardPool $cards;

	public function __construct(CardPool $cards = null, MeldType $meld_type = null)
	{
		$this->cards = $cards ?? new CardPool();
		$this->meld_type = $meld_type;
	}

	public function setMeldType(MeldType $type): void
	{
		$this->meld_type = $type;
	}

	public function getType(): ?MeldType
	{
		return $this->meld_type;
	}

	public function getCards(): CardPool
	{
		return $this->cards;
	}

	public function setCards(CardPool $cards): void
	{
		$this->cards = $cards;
	}

	public function addCard(CardInterface $card): void
	{
		$this->cards->addCard($card);
	}
}