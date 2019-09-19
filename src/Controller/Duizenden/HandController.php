<?php

namespace App\Controller\Duizenden;

use App\CardPool\Exception\EmptyCardPoolException;
use App\Cards\Standard\Exception\InvalidCardIdException;
use App\Entity\Player;
use App\Enum\Exception\EnumConstantsCouldNotBeResolvedException;
use App\Enum\Exception\EnumNotDefinedException;
use App\Games\Duizenden\Actions\Hand\ReorderCard;
use App\Games\Duizenden\Game;
use App\Games\Duizenden\Networking\Message\Action\ReorderCardsAction;
use App\Games\Duizenden\Networking\Message\ActionType;
use App\Games\Duizenden\Networking\Message\InvalidActionException;
use App\Games\Duizenden\Persistence\Exception\GameNotFoundException;
use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;
use App\Games\Duizenden\Player\PlayerInterface;
use App\Games\Duizenden\Score\Exception\UnmappedCardException;
use App\Security\Voter\Duizenden\GameVoter;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class HandController extends AbstractController
{
	use LoadGameTrait;
	use NotifyPlayersTrait;

	/**
	 * @var ReorderCard
	 */
	private $reorder_card;

	/**
	 * @param ReorderCard $reorder_card
	 */
	public function __construct(ReorderCard $reorder_card)
	{
		$this->reorder_card = $reorder_card;
	}

	/**
	 * @param int $source
	 * @param int $target
	 *
	 * @return Response
	 *
	 * @throws EnumConstantsCouldNotBeResolvedException
	 * @throws EnumNotDefinedException
	 * @throws GameNotFoundException
	 * @throws InvalidCardIdException
	 * @throws NonUniqueResultException
	 * @throws ORMException
	 * @throws OptimisticLockException
	 * @throws PlayerNotFoundException
	 * @throws EmptyCardPoolException
	 * @throws UnmappedCardException
	 * @throws InvalidActionException
	 */
	public function reorderCard(int $source, int $target): Response
	{
		$game = $this->loadGame();
		$this->denyAccessUnlessGranted(GameVoter::REORDER_CARDS, $game);

		$this->reorder_card->reorder($game, $this->getGamePlayer($game), $source - 1, $target - 1);

		$this->notifyPlayers($game, $game->getState()->getPlayers()->getCurrentPlayer(), ActionType::REORDER_CARDS());

		return $this->json([]);
	}

	/**
	 * @param Game $game
	 * @param Player $player
	 *
	 * @return PlayerInterface
	 */
	private function getGamePlayer(Game $game, Player $player = null): PlayerInterface
	{
		return $game->getGamePlayerById($player ? $player->getUuid() : $this->getUser()->getUuid());
	}
}