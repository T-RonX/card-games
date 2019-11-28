<?php

declare(strict_types=1);

namespace App\Cards\Standard;

use App\Deck\Card\CardInterface;
use App\Deck\Card\Color\ColorInterface;
use App\Deck\Card\Rank\RankInterface;
use App\Deck\Card\Suit\SuitInterface;

class Card implements CardInterface
{
	private ?string $identifier = null;

	private ?string $identifier_value = null;

	private SuitInterface $suit;

	private RankInterface $rank;

	private ColorInterface $back_color;

	public function __construct(ColorInterface $back_color, SuitInterface $suit, RankInterface $rank)
	{
		$this->suit = $suit;
		$this->rank = $rank;
		$this->back_color = $back_color;
	}

	public function getIdentifier(): string
	{
		if (null === $this->identifier)
		{
			$this->identifier = $this->createId(true);
		}

		return $this->identifier;
	}

	public function getIdentifierValue(): string
	{
		if (null === $this->identifier_value)
		{
			$this->identifier_value = $this->createId(false);
		}

		return $this->identifier_value;
	}

	private function createId(bool $with_back_color): string
	{
		return strtolower(($with_back_color ? $this->back_color->getNameShort()[0] : '').$this->suit->getName().$this->rank->getName());
	}

	public function getSuit(): SuitInterface
	{
		return $this->suit;
	}

	public function getRank(): RankInterface
	{
		return $this->rank;
	}

	function getBackColor(): ColorInterface
	{
		return $this->back_color;
	}

	public function equals(CardInterface $card): bool
	{
		return
			$card->getSuit() instanceof $this->suit &&
			$card->getRank() instanceof $this->rank;
	}
}