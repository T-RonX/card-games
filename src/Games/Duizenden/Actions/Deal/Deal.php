<?php

namespace  App\Games\Duizenden\Actions\Deal;

use App\CardPool\Exception\EmptyCardPoolException;
use App\Games\Duizenden\Actions\StateChangeAction;
use App\Games\Duizenden\Dealer\DealerFinder;
use App\Games\Duizenden\Game;
use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;
use App\Games\Duizenden\Player\PlayerInterface;
use App\Games\Duizenden\State;
use App\Games\Duizenden\Workflow\MarkingType;
use App\Games\Duizenden\Workflow\TransitionType;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Workflow\StateMachine;

class Deal extends StateChangeAction
{
	/**
	 * @var int
	 */
	private const CARDS_PER_PLAYER = 13;

	/**
	 * @var DealerFinder
	 */
	private $dealer_finder;

	public function __construct(
		StateMachine $state_machine,
		DealerFinder $dealer_finder
	)
	{
		parent::__construct($state_machine);

		$this->dealer_finder = $dealer_finder;
	}

	/**
	 * @param Game $game
	 *
	 * @return PlayerInterface
	 *
	 * @throws EmptyCardPoolException
	 * @throws NonUniqueResultException
	 * @throws PlayerNotFoundException
	 */
	public function deal(Game $game): PlayerInterface
	{
		$dealer = null;
		$state = $game->getState();

		$is_round_end = $this->isState(MarkingType::ROUND_END());
		$is_configured = $this->isState(MarkingType::CONFIGURED());

		if ($is_round_end)
		{
			$dealer = $this->dealer_finder->findNextDealer($game);
			$state->getPlayers()->setCurrentPlayer($dealer);
			$this->rebuildUndrawnPool($game);
		}
		elseif ($is_configured)
		{
			$dealer = $state->getPlayers()->getCurrentPlayer();
		}

		$this->giveCards($state);
		$state->getPlayers()->nextPayer();

		if (!$dealer)
		{
			$dealer = $state->getPlayers()->getCurrentPlayer();
		}

		$this->state_machine->apply($game, TransitionType::DEAL()->getValue(), [
			'up_round' => $is_round_end
		]);

		return $dealer;
	}

	/**
	 * Get all cards in the game and rebuild te deck.
	 *
	 * @param Game $game
	 */
	private function rebuildUndrawnPool(Game $game)
	{
		$state  = $game->getState();

		$hand_pools = [];
		$meld_pools = [];

		foreach ($state->getPlayers()->getFreshLoopIterator() as $player)
		{
			$hand_pools[] = $player->getHand();

			foreach ($player->getMelds() as $meld)
			{
				$meld_pools[] = $meld->getCards();
			}
		}

		$undrawn_pool = $game->getDeckRebuilder()->rebuild(
			$state->getUndrawnPool(),
			$state->getDiscardedPool(),
			$hand_pools,
			$meld_pools
		);

		$shuffler = $state->getPlayers()->getCurrentPlayer()->getShuffler();
		$undrawn_pool->setCards($shuffler->shuffle($undrawn_pool->getCards()));
		$state->getPlayers()->resetCards();
		$state->setUndrawnPool($undrawn_pool);
	}

	/**
	 * @param State $state
	 *
	 * @throws EmptyCardPoolException
	 */
	private function giveCards(State $state): void
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