<?php

namespace  App\Games\Duizenden\Actions\Deal;

use App\CardPool\Exception\EmptyCardPoolException;
use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;
use App\Games\Duizenden\State;

class Deal
{
	/**
	 * @var int
	 */
	private const CARDS_PER_PLAYER = 13;

	/**
	 * @param State $state
	 *
	 * @throws PlayerNotFoundException
	 * @throws EmptyCardPoolException
	 */
	function deal(State $state): void
	{
		$iterator = $state->getPlayers()->getContinueLoopIterator(true);

		for ($i = 0; $i < self::CARDS_PER_PLAYER; ++$i)
		{
			foreach ($iterator as $player)
			{
				$card = $state->getUndrawnPool()->drawTopCard();
				$player->getHand()->addCard($card);
			}
		}

		$state->getDiscardedPool()->addCard($state->getUndrawnPool()->drawTopCard());
		$state->getDiscardedPool()->isFirstCard(true);
	}
}