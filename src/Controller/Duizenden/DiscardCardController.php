<?php

namespace App\Controller\Duizenden;

use App\CardPool\Exception\CardNotFoundException;
use App\Cards\Standard\Card;
use App\Cards\Standard\Exception\InvalidCardIdException;
use App\Enum\Exception\EnumConstantsCouldNotBeResolvedException;
use App\Enum\Exception\EnumNotDefinedException;
use App\Games\Duizenden\Actions\DiscardCard\DiscardCard;
use App\Games\Duizenden\DiscardCardResultType;
use App\Games\Duizenden\Exception\DiscardCardException;
use App\Games\Duizenden\Notifier\GameNotifier;
use App\Games\Duizenden\Persistence\Exception\GameNotFoundException;
use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;
use App\Security\Voter\Duizenden\GameVoter;
use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DiscardCardController extends AbstractController
{
	use LoadGameTrait;

	/**
	 * @var GameNotifier
	 */
	private $game_notifier;

	/**
	 * @var DiscardCard
	 */
	private $discard_card;

	/**
	 * @param DiscardCard $discard_card
	 * @param GameNotifier $game_notifier
	 */
	public function __construct(
		DiscardCard $discard_card,
		GameNotifier $game_notifier
	)
	{
		$this->game_notifier = $game_notifier;
		$this->discard_card = $discard_card;
	}

	/**
	 * @param Card $card
	 *
	 * @return Response
	 *
	 * @throws CardNotFoundException
	 * @throws DiscardCardException
	 * @throws EnumConstantsCouldNotBeResolvedException
	 * @throws EnumNotDefinedException
	 * @throws GameNotFoundException
	 * @throws InvalidCardIdException
	 * @throws NonUniqueResultException
	 * @throws PlayerNotFoundException
	 * @throws Exception
	 */
	public function discard(Card $card): Response
	{
		$game = $this->loadGame();
		$this->denyAccessUnlessGranted(GameVoter::DISCARD, $game);

		$current_player = $game->getState()->getPlayers()->getCurrentPlayer();
		$result = $this->discard_card->discard($game, $card);

		switch ($result)
		{
			case DiscardCardResultType::END_TURN():
				break;

			case DiscardCardResultType::END_ROUND():
				break;

			case DiscardCardResultType::END_GAME():
				throw new Exception('Game ended');
		}

		$this->game_notifier->notify($game->getId(), $current_player->getId());

		return $this->render('Duizenden\game.html.twig', [
			'game' => $game
		]);
	}
}