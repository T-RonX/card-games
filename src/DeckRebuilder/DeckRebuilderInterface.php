<?php

namespace App\DeckRebuilder;

use App\CardPool\CardPool;

interface DeckRebuilderInterface
{
	/**
	 * @param CardPool $deck_remainder
	 * @param CardPool $discarded_pool
	 * @param CardPool[] $hand_pools
	 * @param CardPool[] $meld_pools
	 *
	 * @return CardPool
	 */
	public function rebuild(CardPool $deck_remainder, CardPool $discarded_pool, array $hand_pools, array $meld_pools): CardPool;

	/**
	 * @return string
	 */
	public function getName(): string;
}