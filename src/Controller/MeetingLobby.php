<?php

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
	/**
	 * @var Lobby
	 */
	private $lobby;

	/**
	 * @var Inviter
	 */
	private $inviter;

	/**
	 * @var LobbyNotifier
	 */
	private $notifier;

	/**
	 * @var SubscriberIdGenerator
	 */
	private $subscriber_id_generator;

	/**
	 * @var AuthorizationCheckerInterface
	 */
	private $authorization_checker;

	/**
	 * @param Lobby $lobby
	 * @param Inviter $inviter
	 * @param LobbyNotifier $notifier
	 * @param SubscriberIdGenerator $subscriber_id_generator
	 * @param AuthorizationCheckerInterface $authorization_checker
	 */
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

	/**
	 * @return Response
	 */
	public function show(): Response
	{
		$player = $this->getUser();
		$lobby = $this->getLobby();

		$lobby->updatePlayerActivity($player);

		$form_select_players = $this->createForm(InvitePlayersType::class, null, [
			'players' => $lobby->getPlayers()
		]);

		return $this->render('Lobby\lobby.html.twig', [
			'lobby_id' => Lobby::ID,
			'player_id' => $player->getId(),
			'name' => $player->getName(),
			'messages' => $lobby->getMessages(),
			'form' => $form_select_players->createView(),
		]);
	}

	/**
	 * @param Request $request
	 *
	 * @return Response
	 *
	 * @throws Exception
	 */
	public function invite(Request $request): Response
	{
		$lobby = $this->getLobby();
		$player = $this->getUser();

		$form = $this->createForm(InvitePlayersType::class, null, [
			'players' => $lobby->getPlayers()
		]);

		if ($form->handleRequest($request) && $form->isSubmitted() && $form->isValid())
		{
			$players = $form['players']->getData();
			$invitation = $this->inviter->createInvitation($player, $players);
			$this->notifier->publishInvitation($invitation);
		}
		else
		{
			$request->getSession()->getFlashBag()->add('error', $form->getErrors(true)[0]->getMessage());
		}

		return $this->redirect($this->generateUrl('lobby.show'));
	}

	/**
	 * @param Request $request
	 * @param Invitation $invitation
	 *
	 * @return Response
	 *
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
	 * @param Request $request
	 * @param Invitation $invitation
	 *
	 * @return Response
	 *
	 * @throws InvitationException
	 */
	public function declineInvitation(Request $request, Invitation $invitation): Response
	{
		$this->denyAccessUnlessGranted(InvitationVoter::DECLINE, $invitation);

		$player = $this->getUser();
		$this->inviter->declineInvitation($player, $invitation);

		return $request->isXmlHttpRequest() ? new Response() : $this->redirectToRoute('lobby.invitations');
	}

	/**
	 * @return Response
	 */
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

	/**
	 * @return Response
	 */
	public function newAnonymousPlayer(): Response
	{
		$form = $this->createForm(NameType::class);

		return $this->render('Lobby\name.html.twig', [
			'form' => $form->createView()
		]);
	}

	/**
	 * @param string $message
	 *
	 * @return Response
	 *
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

	/**
	 * @return Lobby
	 */
	private function getLobby(): Lobby
	{
		$this->lobby->initialize();

		return $this->lobby;
	}
}