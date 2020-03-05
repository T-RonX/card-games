<?php

declare(strict_types=1);

namespace  App\Games\Duizenden\Actions\DrawCard;

use App\CardPool\Exception\EmptyCardPoolException;
use App\Common\Meld\Melds;
use App\Deck\Card\CardInterface;
use App\Games\Duizenden\Actions\QueenOfSpadesTrait;
use App\Games\Duizenden\Actions\StateChangeAction;
use App\Games\Duizenden\Exception\OutOfCardsException;
use App\Games\Duizenden\Game;
use App\Games\Duizenden\Meld\TypeHelper;
use App\Games\Duizenden\State;
use App\Games\Duizenden\Workflow\TransitionType;

class FromUndrawnPool extends StateChangeAction
{
    use QueenOfSpadesTrait;

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
		$player = $state->getPlayers()->getCurrentPlayer();
		$hand = $player->getHand();

		if (
		    $hand->getCardCount() === 1 &&
            $this->isCardQueenOfSpades($card) &&
            $this->isCardQueenOfSpades($hand->getTopCard()) &&
            !$this->canExtendMeldWithCard($player->getMelds(), $card)
        )
        {
            // @TODO: Handle this exception. Perhaps allow this case and allow discard of queen of spades.
            throw new \Exception('Game can not be finished. Two Queen of Spades in hand and can not be used to extend a meld. Unable to throw a card away.');
        }

		$hand->addCard($card, $target);

		return $card;
	}

	private function recreate(State $state): void
	{
		$cards = $state->getDiscardedPool()->drawAllCards();
		$state->getUndrawnPool()->setCards($cards);

		$shuffler = $state->getPlayers()->getCurrentPlayer()->getShuffler();
		$state->getUndrawnPool()->shuffle($shuffler);
	}

	private function canExtendMeldWithCard(Melds $melds, CardInterface $card): bool
    {
        foreach ($melds as $meld)
        {
            $cards = [...$meld->getCards()->getCards(), $card];

            if (null !== TypeHelper::detectMeldType($cards))
            {
                return true;
            }
        }

        return false;
    }
}