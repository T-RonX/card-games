<?php

declare(strict_types=1);

namespace App\Lobby;

use App\Entity\Player;
use App\Lobby\Entity\Invitation;
use App\Lobby\Entity\Invitee;
use App\Lobby\Event\InvitationEvent;
use App\Lobby\Exception\InvitationException;
use App\Lobby\Repository\InvitationRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Mercure\PublisherInterface;

class Inviter
{
	private EntityManagerInterface $entity_manager;

	private PublisherInterface $publisher;

	private EventDispatcherInterface $event_dispatcher;

	private InvitationRepository $invitation_repository;

	public function __construct(
		EntityManagerInterface $entity_manager,
		PublisherInterface $publisher,
		EventDispatcherInterface $event_dispatcher,
		InvitationRepository $invitation_repository
	)
	{
		$this->entity_manager = $entity_manager;
		$this->publisher = $publisher;
		$this->event_dispatcher = $event_dispatcher;
		$this->invitation_repository = $invitation_repository;
	}

	/**
	 * @param Player[] $invitees
	 *
	 * @return Invitation
	 *
	 * @throws Exception
	 */
	public function createInvitation(Player $inviter, iterable $invitees): Invitation
	{
		$player_inviter = $this->createPlayerInviteEntity($inviter, true);
		$this->entity_manager->persist($player_inviter);

		$invitation = $this->createInviteEntity($player_inviter)
			->addPlayerInvite($player_inviter);
		$this->entity_manager->persist($invitation);

		foreach ($invitees as $player)
		{
			if ($player->getUuid() === $inviter->getUuid())
			{
				continue;
			}

			$player_invite = $this->createPlayerInviteEntity($player, null);
			$invitation->addPlayerInvite($player_invite);
			$this->entity_manager->persist($player_invite);
		}

		$this->entity_manager->flush();

		$this->checkAcceptedByAll($invitation);

		return $invitation;
	}

	public function assignGame(Invitation $invitation, string $game_id)
	{
		$invitation->setGameId($game_id);
		$this->entity_manager->persist($invitation);
		$this->entity_manager->flush();
	}

	/**
	 * @throws Exception
	 */
	private function createInviteEntity(Invitee $inviter): Invitation
	{
		return (new Invitation())
			->setInviter($inviter)
			->setCreatedAt(new DateTime());
	}

	/**
	 * @return Invitee
	 */
	private function createPlayerInviteEntity(Player $player, ?bool $accepted): Invitee
	{
		return (new Invitee())
			->setPlayer($player)
			->setAccepted($accepted);
	}

	/**
	 * @throws InvitationException
	 */
	public function acceptInvitation(?Player $invitee, Invitation $invitation): void
	{
		$found = false;

		foreach ($invitation->getInvitees() as $player_invite)
		{
			if ($invitee->getUuid() === $player_invite->getPlayer()->getUuid())
			{
				if (true !== $player_invite->isAccepted())
				{
					$this->playerAcceptInvitation($player_invite);
				}

				$found = true;
			}
		}

		if (!$found)
		{
			throw new InvitationException(
				sprintf("Can not accept invitation '%s', player with id '%s' was not invited.",
					$invitation->getUuid(),
					$invitee->getUuid()
				));
		}

		$this->checkAcceptedByAll($invitation);
	}

	/**
	 * @throws InvitationException
	 */
	public function declineInvitation(?Player $invitee, Invitation $invitation): void
	{
		foreach ($invitation->getInvitees() as $player_invite)
		{
			if ($invitee->getUuid() === $player_invite->getPlayer()->getUuid())
			{
				if (false !== $player_invite->isAccepted())
				{
					$this->playerDeclineInvitation($player_invite);
				}

				return;
			}
		}

		throw new InvitationException(
			sprintf("Can not decline invitation '%s', player with id '%s' was not invited.",
				$invitation->getUuid(),
				$invitee->getUuid()
			));
	}

	private function checkAcceptedByAll(Invitation $invitation): void
	{
		foreach ($invitation->getInvitees() as $player_invite)
		{
			if (!$player_invite->isAccepted())
			{
				return;
			}
		}

		$event = new InvitationEvent($invitation);
		$this->event_dispatcher->dispatch($event, InvitationEvent::EVENT_ALL_ACCEPTED);
	}

	private function playerAcceptInvitation(Invitee $player_invite): void
	{
		$player_invite->setAccepted(true);
		$this->entity_manager->persist($player_invite);
		$this->entity_manager->flush();
	}

	private function playerDeclineInvitation(Invitee $player_invite): void
	{
		$player_invite->setAccepted(false);
		$this->entity_manager->persist($player_invite);
		$this->entity_manager->flush();
	}

	/**
	 * @return Invitation[]
	 */
	public function getInvitationsByInviter(Player $player): array
	{
		return $this->invitation_repository->getInvitationsByInviter($player);
	}

	/**
	 * @return Invitation[]
	 */
	public function getInvitationsByInvitee(Player $player): array
	{
		return $this->invitation_repository->getInvitationsByInvitee($player);
	}
}