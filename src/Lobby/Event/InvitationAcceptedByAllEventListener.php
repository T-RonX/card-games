<?php

declare(strict_types=1);

namespace App\Lobby\Event;

use App\Lobby\LobbyNotifier;

class InvitationAcceptedByAllEventListener
{
	private LobbyNotifier $notifier;

	public function __construct(LobbyNotifier $notifier)
	{
		$this->notifier = $notifier;
	}

	public function __invoke(InvitationEvent $event): void
	{
		$invitation = $event->getInvitation();
		$this->notifier->publishInvitationAcceptedByAll($invitation);
	}
}