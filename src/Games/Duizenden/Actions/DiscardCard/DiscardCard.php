<?php

declare(strict_types=1);

namespace  App\Games\Duizenden\Actions\DiscardCard;

use App\CardPool\Exception\CardNotFoundException;
use App\Deck\Card\CardInterface;
use App\Games\Duizenden\Actions\Meld\RevertMeld;
use App\Games\Duizenden\Actions\QueenOfSpadesTrait;
use App\Games\Duizenden\Actions\StateChangeAction;
use App\Games\Duizenden\Dealer\DealerFinder;
use App\Games\Duizenden\DiscardCardResultType;
use App\Games\Duizenden\Event\GameEvent;
use App\Games\Duizenden\Event\GameEventType;
use App\Games\Duizenden\Exception\DiscardCardException;
use App\Games\Duizenden\Game;
use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;
use App\Games\Duizenden\Player\PlayerInterface;
use App\Games\Duizenden\Score\Exception\UnmappedCardException;
use App\Games\Duizenden\Score\ScoreCalculator;
use App\Games\Duizenden\State;
use App\Games\Duizenden\Workflow\TransitionType;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Workflow\StateMachine;

class DiscardCard extends StateChangeAction
{
    use QueenOfSpadesTrait;

	private RevertMeld $revert_meld;
	private ScoreCalculator $score_calculator;
	private DealerFinder $dealer_finder;
	private EventDispatcherInterface $event_dispatcher;

	public function __construct(
		StateMachine $state_machine,
		RevertMeld $revert_meld,
		ScoreCalculator $score_calculator,
		DealerFinder $dealer_finder,
		EventDispatcherInterface $event_dispatcher
	)
	{
		parent::__construct($state_machine);

		$this->revert_meld = $revert_meld;
		$this->score_calculator = $score_calculator;
		$this->dealer_finder = $dealer_finder;
		$this->event_dispatcher = $event_dispatcher;
	}

	/**
	 * @throws CardNotFoundException
	 * @throws DiscardCardException
	 * @throws NonUniqueResultException
	 * @throws ORMException
	 * @throws PlayerNotFoundException
	 * @throws UnmappedCardException
	 */
	public function discard(Game $game, CardInterface $card): ?DiscardCardResultType
	{
		$state = $game->getState();

		if (
			$this->isCardQueenOfSpades($card) &&
			$state->getPlayers()->getCurrentPlayer()->getHand()->getCardCount() > 1
		)
		{
			throw new DiscardCardException(sprintf(
					"Can not discard '%s' of '%s' at this time.",
					$card->getRank()->getName(),
					$card->getSuit()->getSymbol())
			);
		}

		if (
			$state->getPlayers()->getCurrentPlayer()->hasMelds() &&
			$this->score_calculator->calculatePlayerMeldsScore($state->getPlayers()->getCurrentPlayer()) < $state->getFirstMeldMinimumPoints())
		{
			$this->revert_meld->revert($game->getId(), $state->getPlayers()->getCurrentPlayer());
			$result = DiscardCardResultType::INVALID_FIRST_MELD();
		}
		else
		{
			$this->discardCard($state, $card);
			$result = $this->getResultAfterDiscardCard($game);
		}

		switch ($result)
		{
			case DiscardCardResultType::END_TURN():
				$state->getPlayers()->nextPayer();
				$this->state_machine->apply($game, TransitionType::DISCARD_END_TURN()->getValue(), [
				    'up_turn' => true
                ]);
				$this->triggerTurnStartedEvent($game);
				break;

			case DiscardCardResultType::END_ROUND():
			    if (!$state->getAllowFirstTurnRoundEnd() && $state->getTurn() === 1)
                {
                    $this->revert_meld->revert($game->getId(), $state->getPlayers()->getCurrentPlayer());
                    $result = DiscardCardResultType::INVALID_ROUND_END();
                    break;
                }

				$dealer = $this->dealer_finder->findNextDealer($game);
				$state->getPlayers()->setCurrentPlayer($dealer);
				$this->state_machine->apply($game, TransitionType::DISCARD_END_ROUND()->getValue(), [
                    'up_turn' => true
                ]);
				$this->triggerTurnStartedEvent($game);
				break;

			case DiscardCardResultType::END_GAME():
                if (!$state->getAllowFirstTurnRoundEnd() && $state->getTurn() === 1)
                {
                    $this->revert_meld->revert($game->getId(), $state->getPlayers()->getCurrentPlayer());
                    $result = DiscardCardResultType::INVALID_ROUND_END();
                    break;
                }

				$this->state_machine->apply($game, TransitionType::DISCARD_END_GAME()->getValue());
				break;
		}

		return $result;
	}

	/**
	 * @throws PlayerNotFoundException
	 * @throws UnmappedCardException
	 */
	private function getResultAfterDiscardCard(Game $game): DiscardCardResultType
	{
		$state = $game->getState();

		if (0 === $state->getPlayers()->getCurrentPlayer()->getHand()->getCardCount())
		{
			$score = null;

			foreach ($state->getPlayers()->getFreshLoopIterator() as $player)
			{
				$player_score = $this->getPlayerScore($game, $player, true);

				if (null === $score || $player_score > $score)
				{
					$score = $player_score;
				}

				if ($score > $state->getTargetScore())
				{
					break;
				}
			}

			if ($score >= $state->getTargetScore())
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
	 * @throws CardNotFoundException
	 */
	private function discardCard(State $state, CardInterface $card): void
	{
		$card = $state->getPlayers()->getCurrentPlayer()->getHand()->drawCard($card);
		$state->getDiscardedPool()->addCard($card);
	}

	/**
	 * @throws PlayerNotFoundException
	 * @throws UnmappedCardException
	 */
	private function getPlayerScore(Game $game, PlayerInterface $player, bool $include_current_round): int
	{
		$game_score = $this->score_calculator->calculateGameScore($game->getId(), $game->getState()->getRoundFinishExtraPoints());
		$score = $game_score->getTotalPlayerScore($player->getId());

		if ($include_current_round)
		{
			$current_score = $this->score_calculator->calculatePlayerRoundScore($player, true, $game->getState()->getRoundFinishExtraPoints());
			$score += $current_score->getScore();
		}

		return $score;
	}

	private function triggerTurnStartedEvent(Game $game): void
	{
		if (!$this->isSandboxed())
		{
			$this->event_dispatcher->dispatch(new GameEvent($game), GameEventType::TURN_STARTED()->getValue());
		}
	}
}