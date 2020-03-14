<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Player;
use App\Lobby\Entity\Invitation;
use App\User\User\UserProvider;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class InvitationVoter extends Voter
{
    public const ACCEPT = 'accept';
    public const DECLINE = 'decline';
    public const CREATE_GAME = 'create_game';

    private UserProvider $user_provider;

    public function __construct(UserProvider $user_provider)
    {
        $this->user_provider = $user_provider;
    }

    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof Invitation && in_array($attribute, [
                self::ACCEPT,
                self::DECLINE,
                self::CREATE_GAME
            ]);
    }

    /**
     * @param string $permission
     * @param Invitation $invitation
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute(string $permission, $invitation, TokenInterface $token): bool
    {
        $player = $this->user_provider->getPlayer();

        if (!$player instanceof Player)
        {
            return false;
        }

        switch ($permission)
        {
            case self::ACCEPT:
            case self::DECLINE:
                $invitee = $invitation->getInviteeByPlayer($player);
                return $invitee && $invitee->getPlayer()->getUuid() == $player->getUuid();

            case self::CREATE_GAME:
                return $this->isInvitationOwner($invitation, $player) && $invitation->allInviteesAccepted();
        }

        return false;
    }

    private function isInvitationOwner(Invitation $invitation, Player $player): bool
    {
        return $invitation->getInviter()->getPlayer()->getUuid() == $player->getUuid();
    }
}