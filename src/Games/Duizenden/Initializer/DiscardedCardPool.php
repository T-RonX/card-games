<?php

namespace App\Games\Duizenden\Initializer;

use App\CardPool\CardPool;
use App\Deck\Card\CardInterface;

class DiscardedCardPool extends CardPool
{
	/**
	 * @var bool
	 */
	private $is_first_card = false;

	/**
	 * @param bool|null $is_first_card
	 *
	 * @return bool
	 */
	public function isFirstCard(bool $is_first_card = null): bool
	{
		if (null !== $is_first_card)
		{
			$this->is_first_card = $is_first_card;
		}

		return $this->is_first_card;
	}

	/**
	 * @param CardInterface $card
	 */
	public function addCard(CardInterface $card, int $target = null): void
	{
		if (count($this) >= 1)
		{
			$this->isFirstCard(false);
		}

		parent::addCard($card, $target);
	}

	/**
	 * @inheritDoc
	 */
	public function clear(): void
	{
		$this->is_first_card = false;

		parent::clear();
	}
}