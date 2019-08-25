<?php

namespace App\Security\Voter;

use App\Entity\Player;
use App\Lobby\Entity\Invitation;
use App\Lobby\Entity\Invitee;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class InvitationVoter extends Voter
{
	public const ACCEPT = 'accept';
	public const DECLINE = 'decline';
	public const CREATE_GAME = 'create_game';

	protected function supports($attribute, $subject): bool
	{
		return $subject instanceof Invitation && in_array($attribute, [
				self::ACCEPT,
				self::DECLINE,
				self::CREATE_GAME
			]);
	}

	/**
	 * @param Invitation $invitation
	 */
	protected function voteOnAttribute($permission, $invitation, TokenInterface $token): bool
	{
		$player = $token->getUser();

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