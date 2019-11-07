<?php

namespace  App\Games\Duizenden\Actions\DiscardCard;

use App\CardPool\Exception\CardNotFoundException;
use App\Cards\Standard\Rank\Queen;
use App\Cards\Standard\Suit\Spades;
use App\Deck\Card\CardInterface;
use App\Games\Duizenden\Actions\StateChangeAction;
use App\Games\Duizenden\Actions\Meld\RevertMeld;
use App\Games\Duizenden\DiscardCardResultType;
use App\Games\Duizenden\Exception\DiscardCardException;
use App\Games\Duizenden\Game;
use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;
use App\Games\Duizenden\Player\PlayerInterface;
use App\Games\Duizenden\Score\Exception\UnmappedCardException;
use App\Games\Duizenden\Score\ScoreCalculator;
use App\Games\Duizenden\State;
use App\Games\Duizenden\Workflow\TransitionType;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Workflow\StateMachine;

class DiscardCard extends StateChangeAction
{
	/**
	 * @var RevertMeld
	 */
	private $revert_meld;

	/**
	 * @var ScoreCalculator
	 */
	private $score_calculator;

	public function __construct(
		StateMachine $state_machine,
		RevertMeld $revert_meld,
		ScoreCalculator $score_calculator
	)
	{
		parent::__construct($state_machine);

		$this->revert_meld = $revert_meld;
		$this->score_calculator = $score_calculator;
	}

	/**
	 * @param Game $game
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
	public function discard(Game $game, CardInterface $card): ?DiscardCardResultType
	{
		$state = $game->getState();

		if (
			$state->getPlayers()->getCurrentPlayer()->getHand()->getCardCount() > 1 &&
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
				$this->state_machine->apply($game, TransitionType::DISCARD_END_TURN()->getValue());
				break;

			case DiscardCardResultType::END_ROUND():
				$this->state_machine->apply($game, TransitionType::DISCARD_END_ROUND()->getValue());
				break;

			case DiscardCardResultType::END_GAME():
				$this->state_machine->apply($game, TransitionType::DISCARD_END_GAME()->getValue());
				break;
		}

		return $result;
	}

	/**
	 * @param Game $game
	 *
	 * @return DiscardCardResultType
	 *
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
	 * @param State $state
	 * @param CardInterface $card
	 *
	 * @throws CardNotFoundException
	 */
	private function discardCard(State $state, CardInterface $card): void
	{
		$card = $state->getPlayers()->getCurrentPlayer()->getHand()->drawCard($card);
		$state->getDiscardedPool()->addCard($card);
	}

	/**
	 * @param Game $game
	 * @param PlayerInterface $player
	 * @param bool $include_current_round
	 *
	 * @return int
	 *
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

	/**
	 * @param CardInterface $card
	 *
	 * @return bool
	 */
	private function isCardQueenOfSpades(CardInterface $card): bool
	{
		return $card->getRank() instanceof Queen && $card->getSuit() instanceof Spades;
	}
}