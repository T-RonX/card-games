<?php

namespace App\Lobby\Event;

use App\Lobby\Entity\Invitation;
use Symfony\Contracts\EventDispatcher\Event;

class InvitationEvent extends Event
{
	public const EVENT_ALL_ACCEPTED = 'invitation.accepted_by_all';

	/**
	 * @var Invitation
	 */
	private $invitation;

	/**
	 * @param Invitation $invitation
	 */
	public function __construct(Invitation $invitation)
	{
		$this->invitation = $invitation;
	}

	/**
	 * @return Invitation
	 */
	public function getInvitation(): Invitation
	{
		return $this->invitation;
	}
}