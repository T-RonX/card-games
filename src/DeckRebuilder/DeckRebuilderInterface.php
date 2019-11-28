<?php

declare(strict_types=1);

namespace App\DeckRebuilder;

use App\CardPool\CardPool;

interface DeckRebuilderInterface
{
	/**
	 * @param CardPool[]|array $hand_pools
	 * @param CardPool[]|array $meld_pools
	 *
	 * @return CardPool
	 */
	public function rebuild(CardPool $deck_remainder, CardPool $discarded_pool, array $hand_pools, array $meld_pools): CardPool;

	public function getName(): string;
}