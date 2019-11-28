<?php

declare(strict_types=1);

namespace App\Games\Duizenden\Initializer;

use App\CardPool\CardPool;
use App\Deck\Card\CardInterface;

class DiscardedCardPool extends CardPool
{
	private bool $is_first_card = false;

	public function isFirstCard(bool $is_first_card = null): bool
	{
		if (null !== $is_first_card)
		{
			$this->is_first_card = $is_first_card;
		}

		return $this->is_first_card;
	}

	public function addCard(CardInterface $card, int $target = null): void
	{
		if (count($this) >= 1)
		{
			$this->isFirstCard(false);
		}

		parent::addCard($card, $target);
	}

	public function clear(): void
	{
		$this->is_first_card = false;

		parent::clear();
	}
}