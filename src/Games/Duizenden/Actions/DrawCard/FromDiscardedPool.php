<?php

namespace  App\Games\Duizenden\Actions\DrawCard;

use App\CardPool\Exception\CardNotFoundException;
use App\CardPool\Exception\EmptyCardPoolException;
use App\Common\Meld\Exception\InvalidMeldException;
use App\Deck\Card\CardInterface;
use App\Games\Duizenden\Actions\StateChangeAction;
use App\Games\Duizenden\Actions\Meld\MeldCards;
use App\Games\Duizenden\Exception\DrawCardException;
use App\Games\Duizenden\Exception\HandException;
use App\Games\Duizenden\Game;
use App\Games\Duizenden\Meld\Exception\MeldException;
use App\Games\Duizenden\State;
use App\Games\Duizenden\Workflow\TransitionType;
use Symfony\Component\Workflow\StateMachine;

class FromDiscardedPool extends StateChangeAction
{
	/**
	 * @var MeldCards
	 */
	private $meld_cards;

	public function __construct(
		StateMachine $state_machine,
		MeldCards $meld_cards
	)
	{
		parent::__construct($state_machine);

		$this->meld_cards = $meld_cards;
	}

	/**
	 * @param Game $game
	 *
	 * @throws DrawCardException
	 * @throws EmptyCardPoolException
	 */
	public function draw(Game $game): void
	{
		$state = $game->getState();

		if (
			$state->getDiscardedPool()->isFirstCard() ||
			$state->getPlayers()->getCurrentPlayer()->hasMelds()
		)
		{
			$this->drawCard($state);
			$this->state_machine->apply($game, TransitionType::DRAW_FROM_DISCARDED()->getValue());
		}
		else
		{
			throw new DrawCardException("Can not draw card from discarded pool since it is not the first card anymore.");
		}
	}

	/**
	 * @param Game $game
	 * @param CardInterface[] $meld_with
	 *
	 * @throws CardNotFoundException
	 * @throws EmptyCardPoolException
	 * @throws InvalidMeldException
	 * @throws MeldException
	 * @throws HandException
	 */
	public function drawAndMeld(Game $game, array $meld_with): void
	{
		$state = $game->getState();

		$card = $state->getDiscardedPool()->drawTopCard();
		$cards = array_merge($meld_with, [$card]);

		foreach ($meld_with as $card)
		{
			$state->getPlayers()->getCurrentPlayer()->getHand()->drawCard($card);
		}

		$this->meld_cards->meldCards($game->getState(), $cards);

		$state->getPlayers()->getCurrentPlayer()->getHand()->addCards(
			$state->getDiscardedPool()->drawAllCards()
		);

		$this->state_machine->apply($game, TransitionType::DRAW_FROM_DISCARDED()->getValue());
	}

	/**
	 * @inheritDoc
	 *
	 * @param State $state
	 *
	 * @throws EmptyCardPoolException
	 */
	private function drawCard(State $state): void
	{
		$card = $state->getDiscardedPool()->drawTopCard();
		$state->getPlayers()->getCurrentPlayer()->getHand()->addCard($card);
	}
}