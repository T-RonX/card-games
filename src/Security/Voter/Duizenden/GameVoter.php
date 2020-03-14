<?php

declare(strict_types=1);

namespace App\Security\Voter\Duizenden;

use App\Entity\Player;
use App\Games\Duizenden\Dealer\DealerFinder;
use App\Games\Duizenden\Game;
use App\Games\Duizenden\Workflow\TransitionType;
use App\User\User\UserProvider;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
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

    private StateMachine $state_machine;

    private Player $player;

    private Game $game;

    private DealerFinder $dealer_finder;
    /**
     * @var UserProvider
     */
    private UserProvider $user_provider;

    public function __construct(
        StateMachine $state_machine,
        DealerFinder $dealer_finder,
        UserProvider $user_provider
    )
    {
        $this->state_machine = $state_machine;
        $this->dealer_finder = $dealer_finder;
        $this->user_provider = $user_provider;
    }

    protected function supports(string $attribute, $subject): bool
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
     * @param string $permission
     * @param Game $game
     * @param TokenInterface $token
     *
     * @throws NonUniqueResultException
     * @throws NoResultException
     * @return bool
     *
     */
    protected function voteOnAttribute($permission, $game, TokenInterface $token): bool
    {
        if (!$this->user_provider->getPlayer() instanceof Player)
        {
            return false;
        }

        /** @var Player $user */
        $user = $this->user_provider->getPlayer();
        $this->player = $user;
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

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
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
     * @throws NoResultException
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