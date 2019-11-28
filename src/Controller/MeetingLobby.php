<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Player;
use App\Form\Lobby\InvitePlayersType;
use App\Form\Lobby\NameType;
use App\Lobby\Entity\Invitation;
use App\Lobby\Exception\InvitationException;
use App\Lobby\Inviter;
use App\Lobby\Lobby;
use App\Lobby\LobbyNotifier;
use App\Mercure\SubscriberIdGenerator;
use App\Security\Voter\InvitationVoter;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @method Player getUser()
 */
class MeetingLobby extends AbstractController
{
	private Lobby $lobby;

	private Inviter $inviter;

	private LobbyNotifier $notifier;

	private SubscriberIdGenerator $subscriber_id_generator;

	private AuthorizationCheckerInterface $authorization_checker;

	public function __construct(
		Lobby $lobby,
		Inviter $inviter,
		LobbyNotifier $notifier,
		SubscriberIdGenerator $subscriber_id_generator,
		AuthorizationCheckerInterface $authorization_checker
	)
	{
		$this->lobby = $lobby;
		$this->inviter = $inviter;
		$this->notifier = $notifier;
		$this->subscriber_id_generator = $subscriber_id_generator;
		$this->authorization_checker = $authorization_checker;
	}

	public function show(): Response
	{
		$player = $this->getUser();
		$lobby = $this->getLobby();

		$lobby->updatePlayerActivity($player);

		$form_select_players = $this->createForm(InvitePlayersType::class, null, [
			'players' => $lobby->getPlayers(),
			'current_player_id' => $player->getUuid()
		]);

		$this->lobby->playerEntered($player);

		return $this->render('Lobby\lobby.html.twig', [
			'lobby_id' => Lobby::ID,
			'player_id' => $player->getId(),
			'name' => $player->getName(),
			'messages' => $lobby->getMessages(),
			'form' => $form_select_players->createView(),
		]);
	}

	/**
	 * @throws Exception
	 */
	public function invite(Request $request): Response
	{
		$player = $this->getUser();
		$lobby = $this->getLobby();

		$form = $this->createForm(InvitePlayersType::class, null, [
			'players' => $lobby->getPlayers(),
			'current_player_id' => $player->getUuid()
		]);

		if ($form->handleRequest($request) && $form->isSubmitted() && $form->isValid())
		{
			$players = $form['players']->getData();
			$invitation = $this->inviter->createInvitation($player, $players);
			$this->notifier->publishInvitation($invitation);
		}
		else
		{
			$this->addFlash('error', $form->getErrors(true)[0]->getMessage());
		}

		return $this->redirect($this->generateUrl('lobby.show'));
	}

	/**
	 * @throws InvitationException
	 */
	public function acceptInvitation(Request $request, Invitation $invitation): Response
	{
		$this->denyAccessUnlessGranted(InvitationVoter::ACCEPT, $invitation);

		$player = $this->getUser();
		$this->inviter->acceptInvitation($player, $invitation);

		return $request->isXmlHttpRequest() ? new Response() : $this->redirectToRoute('lobby.invitations');
	}

	/**
	 * @throws InvitationException
	 */
	public function declineInvitation(Request $request, Invitation $invitation): Response
	{
		$this->denyAccessUnlessGranted(InvitationVoter::DECLINE, $invitation);

		$player = $this->getUser();
		$this->inviter->declineInvitation($player, $invitation);

		return $request->isXmlHttpRequest() ? new Response() : $this->redirectToRoute('lobby.invitations');
	}

	public function invitations(): Response
	{
		$player = $this->getUser();

		$invitations_create = $this->inviter->getInvitationsByInviter($player);
		$invitations_received = $this->inviter->getInvitationsByInvitee($player);

		return $this->render('Lobby\invitations.html.twig', [
			'player' => $player,
			'invitations_create' => $invitations_create,
			'invitations_received' => $invitations_received
		]);
	}

	public function newAnonymousPlayer(): Response
	{
		$form = $this->createForm(NameType::class);

		return $this->render('Lobby\name.html.twig', [
			'form' => $form->createView()
		]);
	}

	/**
	 * @throws Exception
	 */
	public function conveyMessage(string $message): Response
	{
		$lobby = $this->getLobby();
		$player = $this->getUser();

		$lobby->updatePlayerActivity($player);
		$lobby->addMessage($message, $player);

		return new Response('');
	}

	private function getLobby(): Lobby
	{
		$this->lobby->initialize();

		return $this->lobby;
	}
}