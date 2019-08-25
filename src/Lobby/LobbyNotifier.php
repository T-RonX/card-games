<?php

namespace App\Lobby;

use App\Entity\Player;
use App\Lobby\Entity\Invitation;
use App\Lobby\Entity\Invitee;
use App\Mercure\SubscriberIdGenerator;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\Update;

class LobbyNotifier
{
	/**
	 * @var Publisher
	 */
	private $publisher;

	/**
	 * @var SubscriberIdGenerator
	 */
	private $id_generator;

	/**
	 * @param Publisher $publisher
	 * @param SubscriberIdGenerator $id_generator
	 */
	public function __construct(
		Publisher $publisher,
		SubscriberIdGenerator $id_generator
	)
	{
		$this->publisher = $publisher;
		$this->id_generator = $id_generator;
	}

	/**
	 * @param Invitation $invitation
	 */
	public function publishInvitationAcceptedByAll(Invitation $invitation): void
	{
		($this->publisher)($this->createInvitationAcceptedByAllUpdate($invitation, $invitation->getInviter()->getPlayer()));
	}

	/**
	 * @param Invitation $invitation
	 * @param Player $inviter
	 *
	 * @return Update
	 */
	private function createInvitationAcceptedByAllUpdate(Invitation $invitation, Player $inviter): Update
	{
		$id = $this->id_generator->generate($inviter->getUuid());

		return new Update(sprintf('urn:player_event:%s', $id), json_encode($this->createInvitationAcceptedByAllMessageData($invitation)));
	}

	/**
	 * @param Invitation $invitation
	 *
	 * @return string[]
	 */
	private function createInvitationAcceptedByAllMessageData(Invitation $invitation): array
	{
		return [
			'type' => 'invitation_accepted_by_all',
			'data' => [
				'id' => $invitation->getUuid()
			]
		];
	}

	/**
	 * @param Invitation $invitation
	 */
	public function publishInvitation(Invitation $invitation): void
	{
		foreach ($invitation->getInvitees() as $player_invite)
		{
			if ($player_invite->isAccepted())
			{
				continue;
			}

			($this->publisher)($this->createPublishInvitationUpdate($invitation, $player_invite->getPlayer()));
		}
	}

	/**
	 * @param Invitation $invitation
	 * @param Player $invitee
	 *
	 * @return Update
	 */
	private function createPublishInvitationUpdate(Invitation $invitation, Player $invitee): Update
	{
		$id = $this->id_generator->generate($invitee->getUuid());

		return new Update(sprintf('urn:player_event:%s', $id), json_encode($this->createPublishInvitationMessageData($invitation)));
	}

	/**
	 * @param Invitation $invitation
	 *
	 * @return string[]
	 */
	private function createPublishInvitationMessageData(Invitation $invitation): array
	{
		return [
			'type' => 'new_invitation',
			'data' => [
				'id' => $invitation->getUuid(),
				'inviter' => $invitation->getInviter()->getPlayer()->getName(),
			]
		];
	}

	public function publishInvitationGameStarted(?Invitation $invitation)
	{
		foreach ($invitation->getAllPlayers(true) as $invitee)
		{
			($this->publisher)($this->createGameStartedUpdate($invitation, $invitee));
		}
	}

	/**
	 * @param Invitation $invitation
	 * @param Player $invitee
	 *
	 * @return Update
	 */
	private function createGameStartedUpdate(Invitation $invitation, Player $invitee): Update
	{
		$id = $this->id_generator->generate($invitee->getUuid());

		return new Update(sprintf('urn:player_event:%s', $id), json_encode($this->createGameStartedMessageData($invitation)));
	}

	/**
	 * @param Invitation $invitation
	 *
	 * @return string[]
	 */
	private function createGameStartedMessageData(Invitation $invitation): array
	{
		return [
			'type' => 'game_started',
			'data' => [
				'id' => $invitation->getGameId(),
				'inviter' => $invitation->getInviter()->getPlayer()->getName(),
			]
		];
	}

}