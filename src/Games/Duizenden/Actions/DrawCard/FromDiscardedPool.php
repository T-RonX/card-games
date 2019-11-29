<?php

declare(strict_types=1);

namespace  App\Games\Duizenden\Actions\DrawCard;

use App\CardPool\Exception\CardNotFoundException;
use App\CardPool\Exception\EmptyCardPoolException;
use App\Common\Meld\Exception\InvalidMeldException;
use App\Deck\Card\CardInterface;
use App\Games\Duizenden\Actions\Meld\MeldCards;
use App\Games\Duizenden\Actions\StateChangeAction;
use App\Games\Duizenden\Exception\DrawCardException;
use App\Games\Duizenden\Game;
use App\Games\Duizenden\Meld\Exception\MeldException;
use App\Games\Duizenden\State;
use App\Games\Duizenden\Workflow\TransitionType;
use Symfony\Component\Workflow\StateMachine;

class FromDiscardedPool extends StateChangeAction
{
	private MeldCards $meld_cards;

	public function __construct(
		StateMachine $state_machine,
		MeldCards $meld_cards
	)
	{
		parent::__construct($state_machine);

		$this->meld_cards = $meld_cards;
	}

	/**
	 * @throws DrawCardException
	 * @throws EmptyCardPoolException
	 */
	public function draw(Game $game, int $target = null): void
	{
		$state = $game->getState();

		if ($state->getDiscardedPool()->isFirstCard())
		{
			$this->drawCard($state, $target);
			$this->state_machine->apply($game, TransitionType::DRAW_FROM_DISCARDED()->getValue());
		}
		else
		{
			throw new DrawCardException("Can not draw card from discarded pool since it is not the first card anymore.");
		}
	}

	/**
	 * @param CardInterface[] $meld_with
	 *
	 * @throws CardNotFoundException
	 * @throws EmptyCardPoolException
	 * @throws InvalidMeldException
	 * @throws MeldException
	 */
	public function drawAndMeld(Game $game, array $meld_with): void
	{
		$state = $game->getState();

		$card = $state->getDiscardedPool()->drawTopCard();
		$cards = [...$meld_with, $card];

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
	 * @throws EmptyCardPoolException
	 */
	private function drawCard(State $state, int $target = null): void
	{
		$card = $state->getDiscardedPool()->drawTopCard();
		$state->getPlayers()->getCurrentPlayer()->getHand()->addCard($card, $target);
	}
}