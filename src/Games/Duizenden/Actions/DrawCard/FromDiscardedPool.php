<?php

namespace  App\Games\Duizenden\Actions\DrawCard;

use App\CardPool\Exception\EmptyCardPoolException;
use App\Games\Duizenden\State;

class FromDiscardedPool
{
	/**
	 * @inheritDoc
	 *
	 * @param State $state
	 *
	 * @throws EmptyCardPoolException
	 */
	function draw(State $state): void
	{
		$card = $state->getDiscardedPool()->drawTopCard();
		$state->getPlayers()->getCurrentPlayer()->getHand()->addCard($card);
	}
}