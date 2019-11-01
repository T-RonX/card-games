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
use App\Games\Duizenden\StateCompiler\ActionType;
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
	use NotifyPlayersTrait;

	/**
	 * @var DiscardCard
	 */
	private $discard_card;

	/**
	 * @param DiscardCard $discard_card
	 */
	public function __construct(DiscardCard $discard_card)
	{
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
			case DiscardCardResultType::END_ROUND():
				$action = ActionType::DISCARD_END_ROUND();
				break;

			case DiscardCardResultType::END_GAME():
				$action = ActionType::DISCARD_END_GAME();
				break;

			case DiscardCardResultType::INVALID_FIRST_MELD():
				$action = ActionType::INVALID_FIRST_MELD();
				break;

			default:
				$action = ActionType::DISCARD_END_TURN();
				break;
		}

		$this->notifyPlayers($game, $current_player, $action);

		return $this->json([]);
	}

}