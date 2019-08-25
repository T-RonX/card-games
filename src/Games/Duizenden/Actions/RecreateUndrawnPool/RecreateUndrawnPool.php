<?php

namespace App\Games\Duizenden\Actions\RecreateUndrawnPool;

use App\Games\Duizenden\State;

class RecreateUndrawnPool
{
	/**
	 * @param State $state
	 */
	function recreate(State $state): void
	{
		$cards = $state->getDiscardedPool()->drawAllCards();
		$state->getUndrawnPool()->setCards($cards);

		$shuffler = $state->getPlayers()->getCurrentPlayer()->getShuffler();
		$state->getUndrawnPool()->shuffle($shuffler);
	}
}