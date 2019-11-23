<?php

namespace App\Lobby\Event;

use App\Lobby\LobbyNotifier;

class InvitationAcceptedByAllEventListener
{
	/**
	 * @var LobbyNotifier
	 */
	private $notifier;

	/**
	 * @param LobbyNotifier $notifier
	 */
	public function __construct(LobbyNotifier $notifier)
	{
		$this->notifier = $notifier;
	}

	public function __invoke(InvitationEvent $event)
	{
		$invitation = $event->getInvitation();
		$this->notifier->publishInvitationAcceptedByAll($invitation);
	}
}