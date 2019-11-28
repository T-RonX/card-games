<?php

declare(strict_types=1);

namespace App\Lobby\Event;

use App\Lobby\Entity\Invitation;
use Symfony\Contracts\EventDispatcher\Event;

class InvitationEvent extends Event
{
	public const EVENT_ALL_ACCEPTED = 'invitation.accepted_by_all';

	private Invitation $invitation;

	public function __construct(Invitation $invitation)
	{
		$this->invitation = $invitation;
	}

	public function getInvitation(): Invitation
	{
		return $this->invitation;
	}
}