<?php

declare(strict_types=1);

namespace  App\Games\Duizenden\Actions\Deal;

use App\CardPool\Exception\EmptyCardPoolException;
use App\Games\Duizenden\Actions\StateChangeAction;
use App\Games\Duizenden\Dealer\DealerFinder;
use App\Games\Duizenden\Event\GameEvent;
use App\Games\Duizenden\Event\GameEventType;
use App\Games\Duizenden\Game;
use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;
use App\Games\Duizenden\Player\PlayerInterface;
use App\Games\Duizenden\State;
use App\Games\Duizenden\Workflow\MarkingType;
use App\Games\Duizenden\Workflow\TransitionType;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Workflow\StateMachine;

class Deal extends StateChangeAction
{
	private const CARDS_PER_PLAYER = 13;
	private DealerFinder $dealer_finder;
	private EventDispatcherInterface $event_dispatcher;

	public function __construct(
		StateMachine $state_machine,
		DealerFinder $dealer_finder,
		EventDispatcherInterface $event_dispatcher
	)
	{
		parent::__construct($state_machine);

		$this->dealer_finder = $dealer_finder;
		$this->event_dispatcher = $event_dispatcher;
	}

	/**
	 * @throws EmptyCardPoolException
	 * @throws NonUniqueResultException
	 * @throws PlayerNotFoundException
	 * @throws NoResultException
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

		if (!$this->isSandboxed())
		{
			$this->event_dispatcher->dispatch(new GameEvent($game), GameEventType::TURN_STARTED()->getValue());
		}

		return $dealer;
	}

	/**
	 * Get all cards in the game and rebuild te deck.
	 */
	private function rebuildUndrawnPool(Game $game): void
	{
		$state = $game->getState();

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