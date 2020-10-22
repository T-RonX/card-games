<?php

declare(strict_types=1);

namespace App\Controller\Duizenden;

use App\AI\Minimax\Minimax;
use App\Cards\Standard\Exception\InvalidCardIdException;
use App\Games\Duizenden\Event\GameEvent;
use App\Games\Duizenden\Event\GameEventType;
use App\Games\Duizenden\GameStarter;
use App\Entity\Player;
use App\Enum\Exception\EnumConstantsCouldNotBeResolvedException;
use App\Enum\Exception\EnumNotDefinedException;
use App\Form\Duizenden\CreateGameType;
use App\Games\Duizenden\Game;
use App\Games\Duizenden\GameDeleter;
use App\Games\Duizenden\GameManipulator;
use App\Games\Duizenden\Initializer\Exception\InvalidDealerPlayerException;
use App\Games\Duizenden\Notifier\GameNotifier;
use App\Games\Duizenden\Persistence\Exception\GameNotFoundException;
use App\Games\Duizenden\Player\Exception\EmptyPlayerSetException;
use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;
use App\Games\Duizenden\Player\PlayerInterface;
use App\Games\Duizenden\Score\Exception\UnmappedCardException;
use App\Games\Duizenden\StateBuilder\StateBuilder;
use App\Games\Duizenden\Actions\ActionType;
use App\Games\Duizenden\StateCompiler\StateData;
use App\Games\Duizenden\StateCompiler\TopicType;
use App\Games\Duizenden\Workflow\MarkingType;
use App\Lobby\Entity\Invitation;
use App\Lobby\LobbyNotifier;
use App\Repository\PlayerRepository;
use App\Security\Voter\Duizenden\GameVoter;
use App\Security\Voter\InvitationVoter;
use App\User\User\UserProvider;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GameController extends AbstractController
{
    use LoadGameTrait;

    private PlayerRepository $player_repository;
    private GameDeleter $game_deleter;
    private LobbyNotifier $lobby_notifier;
    private StateBuilder $state_builder;
    private GameManipulator $game_manipulator;
    private GameNotifier $game_notifier;
    private UserProvider $user_provider;
	private GameStarter $game_starter;
	private EventDispatcherInterface $event_dispatcher;

	public function __construct(
        PlayerRepository $player_repository,
        GameDeleter $game_deleter,
        GameManipulator $game_manipulator,
        LobbyNotifier $notifier,
        StateBuilder $state_builder,
        GameNotifier $game_notifier,
        UserProvider $user_provider,
		GameStarter $game_starter,
		EventDispatcherInterface $event_dispatcher
	)
    {
        $this->player_repository = $player_repository;
        $this->game_deleter = $game_deleter;
        $this->lobby_notifier = $notifier;
        $this->state_builder = $state_builder;
        $this->game_manipulator = $game_manipulator;
        $this->game_notifier = $game_notifier;
        $this->user_provider = $user_provider;
        $this->game_starter = $game_starter;
	    $this->event_dispatcher = $event_dispatcher;
    }

    public function newGame(?Invitation $invitation = null): Response
    {
        $form = $this->createForm(CreateGameType::class, null, [
            'available_players' => $this->getAvailablePlayers($invitation),
            'by_invitation' => null !== $invitation
        ]);

        return $this->render('Duizenden\new.html.twig', [
            'form' => $form->createView(),
            'invitation' => $invitation
        ]);
    }

    /**
     * @throws EmptyPlayerSetException
     * @throws InvalidDealerPlayerException
     */
    public function createGame(Request $request, ?Invitation $invitation = null): Response
    {
        $form = $this->createForm(CreateGameType::class, null, [
            'available_players' => $this->getAvailablePlayers($invitation),
            'by_invitation' => null !== $invitation
        ]);

        if ($form->handleRequest($request) && $form->isValid())
        {
            /**
             * @var Player[] $players
             * @var Player $first_dealer
             */
            $players = $form->has('players') ? $form['players']->getData() : $invitation->getAllPlayers(true);
            $num_ai_players = $form['ai_players']->getData();
            $initial_shuffle = $form['initial_shuffle']->getData();
            $initial_shuffle_algorithm = $form['initial_shuffle_algorithm']->getData();
            $is_dealer_random = $form['is_dealer_random']->getData();
            $first_dealer = $form['first_dealer']->getData();
            $target_score = $form['target_score']->getData();
            $first_meld_minimum_points = $form['first_meld_minimum_points']->getData();
            $round_finish_extra_points = $form['round_finish_extra_points']->getData();
            $allow_first_turn_round_end = $form['allow_first_turn_round_end']->getData();

	        $game = $this->game_starter->start(
	        	$players,
		        $num_ai_players,
		        $initial_shuffle,
		        $initial_shuffle_algorithm,
		        $first_dealer,
		        $is_dealer_random,
		        $target_score,
		        $first_meld_minimum_points,
		        $round_finish_extra_points,
		        $allow_first_turn_round_end,
		        $invitation
	        );

            $this->session->set('game_id', $game->getId());

            if ($invitation)
            {
                $this->lobby_notifier->publishInvitationGameStarted($invitation);
            }

            return $this->redirect($this->generateUrl('duizenden.play', [
                'uuid' => $game->getId()
            ]));
        }

        return $this->render('Duizenden\new.html.twig', [
            'form' => $form->createView(),
            'invitation' => $invitation
        ]);
    }

    /**
     * @throws EnumConstantsCouldNotBeResolvedException
     * @throws EnumNotDefinedException
     * @throws GameNotFoundException
     * @throws InvalidCardIdException
     * @throws NonUniqueResultException
     * @throws PlayerNotFoundException
     * @throws UnmappedCardException
     */
    public function playGame(string $uuid): Response
    {
        $this->session->set('game_id', $uuid);
        $game = $this->loadGame($uuid);
        $this->denyAccessUnlessGranted(GameVoter::ENTER_GAME, $game);

        $this->event_dispatcher->dispatch(new GameEvent($game), GameEventType::TURN_STARTED()->getValue());

        $state_data = $this->state_builder->createStateData($game);
        $this->complementStateData($game, $state_data);

        return $this->render('Duizenden\Play\play.html.twig', [
            'game' => $game,
            'state_data' => $state_data->create()
        ]);
    }

    private function complementStateData(Game $game, StateData $state_data): void
    {
        if (
            $game->getMarking()->has(MarkingType::ROUND_END) ||
            $game->getMarking()->has(MarkingType::GAME_END)
        )
        {
            foreach ($game->getState()->getPlayers()->getFreshLoopIterator() as $player)
            {
                $state_data->addPlayersFullCardPool($player->getId());
            }
        }
    }

    public function deleteGame(string $uuid): Response
    {
        $this->game_deleter->delete($uuid);

        return $this->redirect($this->generateUrl('game.saved'));
    }

    /**
     * @return PlayerInterface[]
     */
    private function getAvailablePlayers(?Invitation $invitation): array
    {
        if (null !== $invitation)
        {
            $this->denyAccessUnlessGranted(InvitationVoter::CREATE_GAME, $invitation);
            return $invitation->getAllPlayers(true);
        }

        return $this->player_repository->createQueryBuilder('p', 'p.uuid')->where("p.type = 'human'")->getQuery()->execute() ?? [];
    }

    /**
     * @throws EnumConstantsCouldNotBeResolvedException
     * @throws EnumNotDefinedException
     * @throws GameNotFoundException
     * @throws InvalidCardIdException
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws PlayerNotFoundException
     * @throws UnmappedCardException
     */
    public function undoLastAction(string $uuid): Response
    {
        $game = $this->loadGame($uuid);

        $this->game_manipulator->undoLastAction($uuid);
        $message = $this->game_notifier->createGameMessageBuilder($game->getId(), $game, TopicType::GAME_EVENT());
        $message->setSourceAction(ActionType::UNDO_LAST_ACTION());
        $message->setSourcePlayer($game->getGamePlayerById($this->user_provider->getPlayer()->getUuid()));

        $this->game_notifier->notifyMessage($message);

        return new Response();
    }
}