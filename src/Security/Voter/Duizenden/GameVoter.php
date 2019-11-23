<?php

namespace App\Security\Voter\Duizenden;

use App\Entity\Player;
use App\Games\Duizenden\Dealer\DealerFinder;
use App\Games\Duizenden\Game;
use App\Games\Duizenden\Workflow\TransitionType;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Workflow\StateMachine;

class GameVoter extends Voter
{
	public const ENTER_GAME = 'enter_game';
	public const DEAL = 'deal';
	public const DRAW_FROM_UNDRAWN = 'draw_from_undrawn';
	public const DRAW_FROM_DISCARDED = 'draw_from_discarded';
	public const MELD = 'meld';
	public const EXTEND_MELD = 'extend_meld';
	public const DISCARD = 'discard';
	public const REORDER_CARDS = 'reorder_cards';

	/**
	 * @var StateMachine
	 */
	private $state_machine;

	/**
	 * @var Player
	 */
	private $player;
	
	/**
	 * @var Game
	 */
	private $game;

	/**
	 * @var DealerFinder
	 */
	private $dealer_finder;

	public function __construct(
		StateMachine $state_machine,
		DealerFinder $dealer_finder
	)
	{
		$this->state_machine = $state_machine;
		$this->dealer_finder = $dealer_finder;
	}

	protected function supports($attribute, $subject): bool
	{
		return $subject instanceof Game && in_array($attribute, [
				self::ENTER_GAME,
				self::DEAL,
				self::DRAW_FROM_UNDRAWN,
				self::DRAW_FROM_DISCARDED,
				self::MELD,
				self::EXTEND_MELD,
				self::DISCARD,
				self::REORDER_CARDS
			]);
	}

	/**
	 * @param Game $game
	 */
	protected function voteOnAttribute($permission, $game, TokenInterface $token): bool
	{
		if (!$token->getUser() instanceof Player)
		{
			return false;
		}

		$this->player = $token->getUser();
		$this->game = $game;

		switch ($permission)
		{
			case self::ENTER_GAME:
				return $this->canEnterGame();

			case self::DEAL:
				return $this->canDeal();

			case self::DRAW_FROM_UNDRAWN:
				return $this->canDrawFromUndrawn();

			case self::DRAW_FROM_DISCARDED:
				return $this->canDrawFromDiscarded();
				break;

			case self::MELD:
				return $this->canMeld();
				break;

			case self::EXTEND_MELD:
				return $this->canExtendMeld();
				break;

			case self::DISCARD:
				return $this->canDiscard();
				break;

			case self::REORDER_CARDS:
				return $this->canReorderCards();
				break;
		}

		return false;
	}

	private function canEnterGame(): bool
	{
		return $this->isPlayerOfGame();
	}

	private function canDeal(): bool
	{
		return $this->isDealingPlayer() && $this->canTransitionTo(TransitionType::DEAL());
	}

	private function canDrawFromUndrawn(): bool
	{
		return $this->isCurrentPlayer() && $this->canTransitionTo(TransitionType::DRAW_FROM_UNDRAWN());
	}

	private function canDrawFromDiscarded(): bool
	{
		return $this->isCurrentPlayer() && $this->canTransitionTo(TransitionType::DRAW_FROM_DISCARDED());
	}

	private function canMeld(): bool
	{
		return $this->isCurrentPlayer() && $this->canTransitionTo(TransitionType::MELD());
	}

	private function canExtendMeld(): bool
	{
		return $this->isCurrentPlayer() && $this->canTransitionTo(TransitionType::EXTEND_MELD());
	}

	private function canDiscard(): bool
	{
		return $this->isCurrentPlayer() && $this->canTransitionTo(TransitionType::DISCARD_END_TURN());
	}

	private function canReorderCards(): bool
	{
		return $this->isPlayerOfGame();
	}

	private function isPlayerOfGame(): bool
	{
		return $this->game->getState()->getPlayers()->hasId($this->player->getUuid());
	}

	/**
	 * @throws NonUniqueResultException
	 */
	private function isDealingPlayer(): bool
	{
		return $this->player->getUuid() === $this->dealer_finder->findNextDealer($this->game)->getId();
	}

	private function isCurrentPlayer(): bool
	{
		return $this->player->getUuid() === $this->game->getState()->getPlayers()->getCurrentPlayer()->getId();
	}

	private function canTransitionTo(TransitionType $transition): bool
	{
		return $this->state_machine->can($this->game, $transition->getValue());
	}
}