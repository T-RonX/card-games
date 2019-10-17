<?php

namespace App\Cards\Standard;

use App\Deck\Card\CardInterface;
use App\Deck\Card\Color\ColorInterface;
use App\Deck\Card\Rank\RankInterface;
use App\Deck\Card\Suit\SuitInterface;

class Card implements CardInterface
{
	/**
	 * @var string
	 */
	private $identifier;

	/**
	 * @var string
	 */
	private $identifier_value;

	/**
	 * @var SuitInterface
	 */
	private $suit;

	/**
	 * @var RankInterface
	 */
	private $rank;

	/**
	 * @var ColorInterface
	 */
	private $back_color;

	/**
	 * @param ColorInterface $back_color
	 * @param SuitInterface $suit
	 * @param RankInterface $rank
	 */
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

	/**
	 * @return SuitInterface
	 */
	public function getSuit(): SuitInterface
	{
		return $this->suit;
	}

	/**
	 * @return RankInterface
	 */
	public function getRank(): RankInterface
	{
		return $this->rank;
	}

	function getBackColor(): ColorInterface
	{
		return $this->back_color;
	}

	/**
	 * @inheritDoc
	 */
	public function equals(CardInterface $card): bool
	{
		return
			$card->getSuit() instanceof $this->suit &&
			$card->getRank() instanceof $this->rank;
	}
}