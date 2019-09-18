<?php

namespace App\Controller\Duizenden;

use App\Cards\Standard\Exception\InvalidCardIdException;
use App\Entity\Player;
use App\Enum\Exception\EnumConstantsCouldNotBeResolvedException;
use App\Enum\Exception\EnumNotDefinedException;
use App\Games\Duizenden\Actions\Hand\ReorderCard;
use App\Games\Duizenden\Game;
use App\Games\Duizenden\Notifier\GameNotifier;
use App\Games\Duizenden\Persistence\Exception\GameNotFoundException;
use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;
use App\Games\Duizenden\Player\PlayerInterface;
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

	/**
	 * @var GameNotifier
	 */
	private $game_notifier;
	/**
	 * @var ReorderCard
	 */
	private $reorder_card;

	/**
	 * @param ReorderCard $reorder_card
	 * @param GameNotifier $game_notifier
	 */
	public function __construct(
		ReorderCard $reorder_card,
		GameNotifier $game_notifier
	)
	{
		$this->game_notifier = $game_notifier;
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
	 * @throws PlayerNotFoundException
	 * @throws ORMException
	 * @throws OptimisticLockException
	 */
	public function reorderCard(int $source, int $target): Response
	{
		$game = $this->loadGame();
		$this->denyAccessUnlessGranted(GameVoter::REORDER_CARDS, $game);

		$this->reorder_card->reorder($game, $this->getGamePlayer($game), $source - 1, $target - 1);

		$this->game_notifier->notify($game->getId(), $game->getState()->getPlayers()->getCurrentPlayer()->getId(), 'reordered');

		return new JsonResponse(['success' => true], 200);
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