<?php

declare(strict_types=1);

namespace App\DeckRebuilders;

use App\CardPool\CardPool;
use App\DeckRebuilder\DeckRebuilderInterface;

class DistinctDeckRebuilder implements DeckRebuilderInterface
{
	/**
	 * @inheritDoc
	 */
	public function rebuild(CardPool $deck_remainder, CardPool $discarded_pool, array $hand_pools, array $meld_pools): CardPool
	{
		$cards = [];

		foreach($hand_pools as $hand_pool)
		{
			$cards = [...$cards, ...$hand_pool->drawAllCards()];
		}

		foreach($meld_pools as $meld_pool)
		{
			$cards = [...$cards, ...$meld_pool->drawAllCards()];
		}

		$cards = [...$cards, ...$discarded_pool->drawAllCards()];
		$cards = [...$cards, ...$deck_remainder->drawAllCards()];

		return (new CardPool($cards));
	}

	private function spreadJokers()
	{

	}

	private function moveQueenOfSpades()
	{

	}

	/**
	 * @inheritDoc
	 */
	public function getName(): string
	{
		return DeckRebuilderType::DISTINCT()->getValue();
	}
}