<?php

namespace  App\Games\Duizenden\Actions\Meld;

use App\CardPool\CardPool;
use App\Common\Meld\Exception\InvalidMeldException;
use App\Common\Meld\Meld;
use App\Deck\Card\CardInterface;
use App\Games\Duizenden\Meld\Exception\MeldException;
use App\Games\Duizenden\Meld\OrderHelper;
use App\Games\Duizenden\Meld\TypeHelper;
use App\Games\Duizenden\State;

class ExtendMeld
{
	/**
	 * @param State $state
	 * @param int $meld_id
	 * @param CardInterface $card
	 *
	 * @throws InvalidMeldException
	 * @throws MeldException
	 */
	function extendMeld(State $state, int $meld_id, CardInterface $card): void
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
			$strings[] = $card->getSuit()->getName() . $card->getRank()->getName();
		}

		return implode(', ', $strings);
	}
}