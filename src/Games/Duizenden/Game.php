<?php

namespace App\Games\Duizenden;

use App\CardPool\Exception\CardNotFoundException;
use App\CardPool\Exception\EmptyCardPoolException;
use App\Cards\Standard\Rank\Queen;
use App\Cards\Standard\Suit\Spades;
use App\Common\Meld\Exception\InvalidMeldException;
use App\Deck\Card\CardInterface;
use App\DeckRebuilder\DeckRebuilderInterface;
use App\Games\Duizenden\Dealer\DealerFinder;
use App\Game\GameInterface;
use App\Games\Duizenden\Actions\Deal\Deal;
use App\Games\Duizenden\Actions\DiscardCard\DiscardCard;
use App\Games\Duizenden\Actions\DrawCard\FromDiscardedPool;
use App\Games\Duizenden\Actions\DrawCard\FromUndrawnPool;
use App\Games\Duizenden\Actions\Hand\ReorderCard;
use App\Games\Duizenden\Actions\Meld\ExtendMeld;
use App\Games\Duizenden\Actions\Meld\MeldCards;
use App\Games\Duizenden\Actions\Meld\RevertMeld;
use App\Games\Duizenden\Actions\RecreateUndrawnPool\RecreateUndrawnPool;
use App\Games\Duizenden\Exception\DiscardCardException;
use App\Games\Duizenden\Exception\DrawCardException;
use App\Games\Duizenden\Exception\HandException;
use App\Games\Duizenden\Exception\OutOfCardsException;
use App\Games\Duizenden\Initializer\Exception\InvalidDealerPlayerException;
use App\Games\Duizenden\Initializer\Initializer;
use App\Games\Duizenden\Meld\Exception\MeldException;
use App\Games\Duizenden\Player\Exception\EmptyPlayerSetException;
use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;
use App\Games\Duizenden\Player\PlayerInterface;
use App\Games\Duizenden\Score\Exception\UnmappedCardException;
use App\Games\Duizenden\Score\ScoreCalculator;
use App\Games\Duizenden\Workflow\MarkingType;
use App\Games\Duizenden\Workflow\TransitionType;
use App\Repository\PlayerRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Workflow\StateMachine;

class Game implements GameInterface
{
	/**
	 * @var string
	 */
	public const NAME = 'duizenden';

	/**
	 * @var State
	 */
	private $state;

	/**
	 * @var DeckRebuilderInterface
	 */
	private $deck_rebuilder;

	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var Initializer
	 */
	private $initializer;

	/**
	 * @var ScoreCalculator
	 */
	private $score_calculator;

	/**
	 * @var StateMachine
	 */
	private $state_machine;

	/**
	 * @var Deal
	 */
	private $action_deal;

	/**
	 * @var DiscardCard
	 */
	private $action_discard_card;

	/**
	 * @var FromDiscardedPool
	 */
	private $action_draw_from_discarded_pool;

	/**
	 * @var FromUndrawnPool
	 */
	private $action_draw_from_undrawn_pool;

	/**
	 * @var MeldCards
	 */
	private $action_meld_cards;

	/**
	 * @var RecreateUndrawnPool
	 */
	private $action_recreate_undrawn_pool;

	/**
	 * @var ExtendMeld
	 */
	private $action_extend_meld;

	/**
	 * @var ReorderCard
	 */
	private $action_reorder_card;

	/**
	 * @var RevertMeld
	 */
	private $action_revert_meld;
	/**
	 * @var DealerFinder
	 */
	private $dealer_finder;

	/**
	 * @param Initializer $initializer
	 * @param ScoreCalculator $score_calculator
	 * @param StateMachine $state_machine
	 * @param DealerFinder $dealer_finder
	 * @param Deal $deal
	 * @param FromDiscardedPool $draw_from_discarded_pool
	 * @param FromUndrawnPool $draw_from_undrawn_pool
	 * @param MeldCards $meld_cards
	 * @param ExtendMeld $extend_meld
	 * @param RecreateUndrawnPool $recreate_undrawn_pool
	 * @param DiscardCard $discard_card
	 * @param ReorderCard $reorder_card
	 * @param RevertMeld $revert_meld
	 */
	public function __construct(
		Initializer $initializer,
		ScoreCalculator $score_calculator,
		StateMachine $state_machine,
		DealerFinder $dealer_finder,
		Deal $deal,
		FromDiscardedPool $draw_from_discarded_pool,
		FromUndrawnPool $draw_from_undrawn_pool,
		MeldCards $meld_cards,
		ExtendMeld $extend_meld,
		RecreateUndrawnPool $recreate_undrawn_pool,
		DiscardCard $discard_card,
		ReorderCard $reorder_card,
		RevertMeld $revert_meld
	)
	{
		$this->initializer = $initializer;
		$this->score_calculator = $score_calculator;
		$this->state_machine = $state_machine;
		$this->dealer_finder = $dealer_finder;
		$this->action_deal = $deal;
		$this->action_discard_card = $discard_card;
		$this->action_draw_from_discarded_pool = $draw_from_discarded_pool;
		$this->action_draw_from_undrawn_pool = $draw_from_undrawn_pool;
		$this->action_meld_cards = $meld_cards;
		$this->action_recreate_undrawn_pool = $recreate_undrawn_pool;
		$this->action_extend_meld = $extend_meld;
		$this->action_reorder_card = $reorder_card;
		$this->action_revert_meld = $revert_meld;
	}

	/**
	 * @param Configurator $configurator
	 *
	 * @throws InvalidDealerPlayerException
	 * @throws EmptyPlayerSetException
	 */
	public function configure(Configurator $configurator): void
	{
		$this->state = $this->initializer->createInitialState($configurator);
		$this->deck_rebuilder = $this->initializer->getDeckRebuilderFromConfig($configurator);

		$this->state_machine->apply($this, TransitionType::CONFIGURE()->getValue());
	}

	/**
	 * @throws EmptyCardPoolException
	 * @throws PlayerNotFoundException
	 * @throws NonUniqueResultException
	 */
	public function deal(): PlayerInterface
	{
		$dealer = null;

		$is_round_end = $this->isState(MarkingType::ROUND_END());
		$is_configured = $this->isState(MarkingType::CONFIGURED());

		if ($is_round_end)
		{
			$dealer = $this->dealer_finder->findNextDealer($this);
			$this->getState()->getPlayers()->setCurrentPlayer($dealer);
			$this->rebuildUndrawnPool();
		}
		elseif ($is_configured)
		{
			$dealer = $this->getState()->getPlayers()->getCurrentPlayer();
		}

		$this->action_deal->deal($this->state);
		$this->nextPlayer();

		if (!$dealer)
		{
			$dealer = $this->getState()->getPlayers()->getCurrentPlayer();
		}

		$this->state_machine->apply($this, TransitionType::DEAL()->getValue(), [
			'up_round' => $is_round_end
		]);

		return $dealer;
	}

	/**
	 * @param PlayerInterface $player
	 * @param int $source
	 * @param int $target
	 *
	 * @throws NonUniqueResultException
	 * @throws ORMException
	 * @throws OptimisticLockException
	 * @throws PlayerNotFoundException
	 */
	public function reorderCard(PlayerInterface $player, int $source, int $target): void
	{
		$this->action_reorder_card->reorder($this->id, $player, $source, $target);
	}

	public function getGamePlayerById(string $id): PlayerInterface
	{
		return $this->state->getPlayers()->getPlayerById($id);
	}

	/**
	 * Get all cards in the game and rebuild te deck.
	 */
	private function rebuildUndrawnPool()
	{
		$hand_pools = [];
		$meld_pools = [];

		foreach ($this->state->getPlayers()->getFreshLoopIterator() as $player)
		{
			$hand_pools[] = $player->getHand();

			foreach ($player->getMelds() as $meld)
			{
				$meld_pools[] = $meld->getCards();
			}
		}

		$undrawn_pool = $this->deck_rebuilder->rebuild(
			$this->state->getUndrawnPool(),
			$this->state->getDiscardedPool(),
			$hand_pools,
			$meld_pools
		);

		$shuffler = $this->state->getPlayers()->getCurrentPlayer()->getShuffler();
		$undrawn_pool->setCards($shuffler->shuffle($undrawn_pool->getCards()));
		$this->state->getPlayers()->resetCards();
		$this->state->setUndrawnPool($undrawn_pool);
	}

	/**
	 * @param MarkingType $state
	 *
	 * @return bool
	 */
	private function isState(MarkingType $state): bool
	{
		return $this->state_machine->getMarking($this)->has($state->getValue());
	}

	/**
	 * @throws EmptyCardPoolException
	 * @throws OutOfCardsException
	 *
	 * @return CardInterface
	 */
	public function drawCardFromUndrawnPool(): CardInterface
	{
		try
		{
			$card = $this->action_draw_from_undrawn_pool->draw($this->state);
		}
		catch (EmptyCardPoolException $e)
		{
			$this->action_recreate_undrawn_pool->recreate($this->state);

			if (!count($this->state->getUndrawnPool()))
			{
				throw new OutOfCardsException("There are not more cards left, game ended.", 0, $e);
			}

			$card = $this->action_draw_from_undrawn_pool->draw($this->state);
		}

		$this->state_machine->apply($this, TransitionType::DRAW_FROM_UNDRAWN()->getValue());

		return $card;
	}

	/**
	 * @throws DrawCardException
	 * @throws EmptyCardPoolException
	 */
	public function drawCardFromDiscardedPool(): void
	{
		$has_melds = $this->state->getPlayers()->getCurrentPlayer()->hasMelds();

		if ($this->state->getDiscardedPool()->isFirstCard())
		{
			$this->action_draw_from_discarded_pool->draw($this->state);
			$this->state_machine->apply($this, TransitionType::DRAW_FROM_DISCARDED()->getValue());
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
	 * @throws MeldException
	 * @throws InvalidMeldException
	 */
	public function drawCardFromDiscardedPoolAndMeld(array $meld_with): void
	{
		$card = $this->state->getDiscardedPool()->drawTopCard();
		$cards = array_merge($meld_with, [$card]);

		foreach ($meld_with as $card)
		{
			$this->state->getPlayers()->getCurrentPlayer()->getHand()->drawCard($card);
		}

		$this->action_meld_cards->meld($this->state, $cards);

		$this->state->getPlayers()->getCurrentPlayer()->getHand()->addCards(
			$this->state->getDiscardedPool()->drawAllCards()
		);

		$this->state_machine->apply($this, TransitionType::DRAW_FROM_DISCARDED()->getValue());
	}

	/**
	 * @param CardInterface[] $cards
	 *
	 * @throws CardNotFoundException
	 * @throws MeldException
	 * @throws InvalidMeldException
	 * @throws HandException
	 * @throws UnmappedCardException
	 */
	public function meldCards(array $cards): void
	{
		foreach ($cards as $card)
		{
			$this->state->getPlayers()->getCurrentPlayer()->getHand()->drawCard($card);
		}

		if ($this->state->getPlayers()->getCurrentPlayer()->getHand()->getCardCount() < 1)
		{
			throw new HandException("Can not meld cards, at least one card must remain to discard.");
		}

		$this->action_meld_cards->meld($this->state, $cards);

		$this->state_machine->apply($this, TransitionType::MELD()->getValue());
	}

	/**
	 * @param int $meld_id
	 * @param CardInterface $card
	 *
	 * @throws CardNotFoundException
	 * @throws HandException
	 * @throws InvalidMeldException
	 * @throws MeldException
	 */
	public function extendMeld(int $meld_id, CardInterface $card): void
	{
		$this->state->getPlayers()->getCurrentPlayer()->getHand()->drawCard($card);

		if ($this->state->getPlayers()->getCurrentPlayer()->getHand()->getCardCount() < 1)
		{
			throw new HandException("Can not extend meld, at least one card must remain to discard.");
		}

		$this->action_extend_meld->extendMeld($this->state, $meld_id, $card);

		$this->state_machine->apply($this, TransitionType::EXTEND_MELD()->getValue());
	}

	/**
	 * @param CardInterface $card
	 *
	 * @return DiscardCardResultType|null
	 *
	 * @throws CardNotFoundException
	 * @throws DiscardCardException
	 * @throws NonUniqueResultException
	 * @throws ORMException
	 * @throws OptimisticLockException
	 * @throws PlayerNotFoundException
	 * @throws UnmappedCardException
	 */
	public function discardCard(CardInterface $card): ?DiscardCardResultType
	{
		if (
			$this->state->getPlayers()->getCurrentPlayer()->getHand()->getCardCount() > 1 &&
			$this->isCardQueenOfSpades($card)
		)
		{
			throw new DiscardCardException(sprintf(
					"Can not discard '%s' of '%s' at this time.",
					$card->getRank()->getName(),
					$card->getSuit()->getSymbol())
			);
		}

		if (
			$this->state->getPlayers()->getCurrentPlayer()->hasMelds() &&
			$this->score_calculator->calculatePlayerMeldsScore($this->state->getPlayers()->getCurrentPlayer()) < $this->state->getFirstMeldMinimumPoints())
		{
			$this->action_revert_meld->revert($this->id, $this->state->getPlayers()->getCurrentPlayer());
			$result = null;
//			throw new DiscardCardException(sprintf(
//					"Can not discard at this time, total meld score must me at least worth %d points, current meld score is %d.",
//					$this->state->getFirstMeldMinimumPoints(),
//					$meld_score
//				)
//			);

		}
		else
		{

			$this->action_discard_card->discard($this->state, $card);
			$result = $this->getResultAfterDiscardCard();
		}

		switch ($result)
		{
			case DiscardCardResultType::END_TURN():
				$this->nextPlayer();
				$this->state_machine->apply($this, TransitionType::DISCARD_END_TURN()->getValue());
				break;

			case DiscardCardResultType::END_ROUND():
				$this->state_machine->apply($this, TransitionType::DISCARD_END_ROUND()->getValue());
				break;

			case DiscardCardResultType::END_GAME():
				$this->state_machine->apply($this, TransitionType::DISCARD_END_GAME()->getValue());
				break;
		}

		return $result;
	}


	/**
	 * @return DiscardCardResultType
	 *
	 * @throws PlayerNotFoundException
	 * @throws UnmappedCardException
	 */
	private function getResultAfterDiscardCard(): DiscardCardResultType
	{
		if (0 === $this->state->getPlayers()->getCurrentPlayer()->getHand()->getCardCount())
		{
			$score = null;

			foreach ($this->state->getPlayers()->getFreshLoopIterator() as $player)
			{
				$player_score = $this->getPlayerScore($player, true);

				if (null === $score || $player_score > $score)
				{
					$score = $player_score;
				}

				if ($score > $this->state->getTargetScore())
				{
					break;
				}
			}

			if ($score >= $this->state->getTargetScore())
			{
				return DiscardCardResultType::END_GAME();
			}
			else
			{
				return DiscardCardResultType::END_ROUND();
			}
		}

		return DiscardCardResultType::END_TURN();
	}

	/**
	 * Advance the current player.
	 */
	public function nextPlayer(): void
	{
		$this->state->getPlayers()->nextPayer();
	}

	/**
	 * @param CardInterface $card
	 *
	 * @return bool
	 */
	private function isCardQueenOfSpades(CardInterface $card): bool
	{
		return $card->getRank() instanceof Queen && $card->getSuit() instanceof Spades;
	}

	/**
	 * @param PlayerInterface $player
	 * @param bool $include_current_round
	 *
	 * @return int
	 *
	 * @throws PlayerNotFoundException
	 * @throws UnmappedCardException
	 */
	private function getPlayerScore(PlayerInterface $player, bool $include_current_round): int
	{
		$game_score = $this->score_calculator->calculateGameScore($this->id);
		$score = $game_score->getTotalPlayerScore($player->getId());

		if ($include_current_round)
		{
			$current_score = $this->score_calculator->calculatePlayerRoundScore($player);
			$score += $current_score->getScore();
		}

		return $score;
	}

	/**
	 * @return string
	 */
	public function getId(): ?string
	{
		return $this->id;
	}

	/**
	 * @param string $id
	 */
	public function setId(string $id): void
	{
		$this->id = $id;
	}

	/**
	 * @return ScoreCalculator
	 */
	public function getScoreCalculator(): ScoreCalculator
	{
		return $this->score_calculator;
	}

	/**
	 * @param State $state
	 */
	public function setState(State $state): void
	{
		$this->state = $state;
	}

	/**
	 * @return State
	 */
	public function getState(): State
	{
		return $this->state;
	}

	/**
	 * @inheritDoc
	 */
	public function getName(): string
	{
		return self::NAME;
	}

	/**
	 * @return void
	 */
	function setCreatedMarking(): void
	{
		$this->state_machine->getMarkingStore()->setMarking($this, $this->state_machine->getMarking($this));
	}

	/**
	 * @return DeckRebuilderInterface
	 */
	public function getDeckRebuilder(): DeckRebuilderInterface
	{
		return $this->deck_rebuilder;
	}

	/**
	 * @param DeckRebuilderInterface $deck_rebuilder
	 */
	public function setDeckRebuilder(DeckRebuilderInterface $deck_rebuilder): void
	{
		$this->deck_rebuilder = $deck_rebuilder;
	}
}
