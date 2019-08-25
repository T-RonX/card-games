<?php

namespace  App\Games\Duizenden\Actions\DrawCard;

use App\CardPool\Exception\EmptyCardPoolException;
use App\Deck\Card\CardInterface;
use App\Games\Duizenden\State;

class FromUndrawnPool
{
	/**
	 * @param State $state
	 *
	 * @return CardInterface
	 *
	 * @throws EmptyCardPoolException
	 */
	function draw(State $state): CardInterface
	{
		$card = $state->getUndrawnPool()->drawTopCard();
		$state->getPlayers()->getCurrentPlayer()->getHand()->addCard($card);

		return $card;
	}
}