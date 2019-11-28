<?php

declare(strict_types=1);

namespace  App\Games\Duizenden\Actions\DrawCard;

use App\CardPool\Exception\EmptyCardPoolException;
use App\Deck\Card\CardInterface;
use App\Games\Duizenden\Actions\StateChangeAction;
use App\Games\Duizenden\Exception\OutOfCardsException;
use App\Games\Duizenden\Game;
use App\Games\Duizenden\State;
use App\Games\Duizenden\Workflow\TransitionType;

class FromUndrawnPool extends StateChangeAction
{
	/**
	 * @throws EmptyCardPoolException
	 * @throws OutOfCardsException
	 */
	public function draw(Game $game, int $target = null): CardInterface
	{
		$state = $game->getState();

		try
		{
			$card = $this->drawCard($state, $target);
		}
		catch (EmptyCardPoolException $e)
		{
			$this->recreate($state);

			if (!count($state->getUndrawnPool()))
			{
				throw new OutOfCardsException("There are not more cards left, game ended.", 0, $e);
			}

			$card = $this->drawCard($state);
		}

		$this->state_machine->apply($game, TransitionType::DRAW_FROM_UNDRAWN()->getValue());

		return $card;
	}

	/**
	 * @throws EmptyCardPoolException
	 */
	private function drawCard(State $state, int $target = null): CardInterface
	{
		$card = $state->getUndrawnPool()->drawTopCard();
		$state->getPlayers()->getCurrentPlayer()->getHand()->addCard($card, $target);

		return $card;
	}

	private function recreate(State $state): void
	{
		$cards = $state->getDiscardedPool()->drawAllCards();
		$state->getUndrawnPool()->setCards($cards);

		$shuffler = $state->getPlayers()->getCurrentPlayer()->getShuffler();
		$state->getUndrawnPool()->shuffle($shuffler);
	}
}