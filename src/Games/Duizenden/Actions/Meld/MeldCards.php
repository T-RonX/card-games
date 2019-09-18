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
use App\Games\Duizenden\Player\PlayerInterface;
use App\Games\Duizenden\State;
use App\Games\Duizenden\Workflow\TransitionType;

class MeldCards extends StateChangeAction
{
	/**
	 * @param Game $game
	 * @param CardInterface[] $cards
	 *
	 * @throws CardNotFoundException
	 * @throws HandException
	 * @throws InvalidMeldException
	 * @throws MeldException
	 */
	public function meld(Game $game, array $cards): void
	{
		$state = $game->getState();

		$this->drawFromHand($state->getPlayers()->getCurrentPlayer(), $cards);
		$this->meldCards($state, $cards);

		$this->state_machine->apply($game, TransitionType::MELD()->getValue());
	}

	/**
	 * @param PlayerInterface $player
	 * @param CardInterface[] $cards
	 *
	 * @throws CardNotFoundException
	 * @throws HandException
	 */
	private function drawFromHand(PlayerInterface $player, array $cards): void
	{
		foreach ($cards as $card)
		{
			$player->getHand()->drawCard($card);
		}

		if ($player->getHand()->getCardCount() < 1)
		{
			throw new HandException("Can not meld cards, at least one card must remain to discard.");
		}
	}

	/**
	 * @param State $state
	 * @param CardInterface[] $cards
	 *
	 * @throws InvalidMeldException
	 * @throws MeldException
	 */
	public function meldCards(State $state, array $cards): void
	{
		OrderHelper::orderCards($cards);
		$meld_type = TypeHelper::detectMeldType($cards);

		if (!$meld_type)
		{
			throw new InvalidMeldException(sprintf(
				"Unable to compose a valid meld from the given cards '%s'.",
				$this->getCardErrorString($cards)
			));
		}

		$meld = new Meld(new CardPool($cards), $meld_type);

		$state->getPlayers()->getCurrentPlayer()->getMelds()->addMeld($meld);
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