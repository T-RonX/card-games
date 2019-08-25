<?php

namespace  App\Games\Duizenden\Actions\DiscardCard;

use App\CardPool\Exception\CardNotFoundException;
use App\Deck\Card\CardInterface;
use App\Games\Duizenden\State;

class DiscardCard
{
	/**
	 * @param State $state
	 * @param CardInterface $card
	 *
	 * @throws CardNotFoundException
	 */
	function discard(State $state, CardInterface $card): void
	{
		$card = $state->getPlayers()->getCurrentPlayer()->getHand()->drawCard($card);
		$state->getDiscardedPool()->addCard($card);
	}
}