<?php

namespace  App\Games\Duizenden\Actions\Meld;

use App\CardPool\CardPool;
use App\CardPool\Exception\CardNotFoundException;
use App\Common\Meld\Exception\InvalidMeldException;
use App\Common\Meld\Meld;
use App\Deck\Card\CardInterface;
use App\Games\Duizenden\Actions\StateChangeAction;
use App\Games\Duizenden\Exception\HandException;
use App\Games\Duizenden\Game;
use App\Games\Duizenden\Meld\Exception\MeldException;
use App\Games\Duizenden\Meld\OrderHelper;
use App\Games\Duizenden\Meld\TypeHelper;
use App\Games\Duizenden\State;
use App\Games\Duizenden\Workflow\TransitionType;

class ExtendMeld extends StateChangeAction
{
	/**
	 * @param Game $game
	 * @param int $meld_id
	 * @param CardInterface $card
	 *
	 * @throws CardNotFoundException
	 * @throws HandException
	 * @throws InvalidMeldException
	 * @throws MeldException
	 */
	public function extend(Game $game, int $meld_id, CardInterface $card): void
	{
		$state = $game->getState();

		$state->getPlayers()->getCurrentPlayer()->getHand()->drawCard($card);

		if ($state->getPlayers()->getCurrentPlayer()->getHand()->getCardCount() < 1)
		{
			throw new HandException("Can not extend meld, at least one card must remain to discard.");
		}

		$this->extendMeld($state, $meld_id, $card);

		$this->state_machine->apply($game, TransitionType::EXTEND_MELD()->getValue());
	}

	/**
	 * @param State $state
	 * @param int $meld_id
	 * @param CardInterface $card
	 *
	 * @throws InvalidMeldException
	 * @throws MeldException
	 */
	private function extendMeld(State $state, int $meld_id, CardInterface $card): void
	{
		$meld = $state->getPlayers()->getCurrentPlayer()->getMelds()->get($meld_id);

		$cards = $meld->getCards()->getCards();
		$cards[] = $card;

		OrderHelper::orderCards($cards);
		$meld_type = TypeHelper::detectMeldType($cards);

		if (!$meld_type)
		{
			throw new InvalidMeldException(sprintf(
				"Unable to compose a valid meld from the given cards '%s'.",
				$this->getCardErrorString($cards)
			));
		}

		$meld->getCards()->setCards($cards);
	}

	/**
	 * @param CardInterface[] $cards
	 *
	 * @return string
	 */
	private function getCardErrorString(array $cards): string
	{
		$strings = [];

		foreach ($cards as $card)
		{
			$strings[] = $card->getIdentifier();
		}

		return implode(', ', $strings);
	}
}