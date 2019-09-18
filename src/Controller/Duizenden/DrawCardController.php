<?php

namespace App\Controller\Duizenden;

use App\CardPool\CardPool;
use App\CardPool\Exception\CardNotFoundException;
use App\CardPool\Exception\EmptyCardPoolException;
use App\Cards\Standard\Exception\InvalidCardIdException;
use App\Common\Meld\Exception\InvalidMeldException;
use App\Enum\Exception\EnumConstantsCouldNotBeResolvedException;
use App\Enum\Exception\EnumNotDefinedException;
use App\Games\Duizenden\Actions\DrawCard\FromDiscardedPool;
use App\Games\Duizenden\Actions\DrawCard\FromUndrawnPool;
use App\Games\Duizenden\Exception\DrawCardException;
use App\Games\Duizenden\Exception\HandException;
use App\Games\Duizenden\Exception\OutOfCardsException;
use App\Games\Duizenden\Meld\Exception\MeldException;
use App\Games\Duizenden\Notifier\GameNotifier;
use App\Games\Duizenden\Persistence\Exception\GameNotFoundException;
use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;
use App\Security\Voter\Duizenden\GameVoter;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DrawCardController extends AbstractController
{
	use LoadGameTrait;

	/**
	 * @var GameNotifier
	 */
	private $game_notifier;

	/**
	 * @var FromDiscardedPool
	 */
	private $draw_from_discarded_pool;

	/**
	 * @var FromUndrawnPool
	 */
	private $draw_from_undrawn_pool;

	/**
	 * @param FromDiscardedPool $draw_from_discarded_pool
	 * @param FromUndrawnPool $draw_from_undrawn_pool
	 * @param GameNotifier $game_notifier
	 */
	public function __construct(
		FromDiscardedPool $draw_from_discarded_pool,
		FromUndrawnPool $draw_from_undrawn_pool,
		GameNotifier $game_notifier
	)
	{
		$this->game_notifier = $game_notifier;
		$this->draw_from_discarded_pool = $draw_from_discarded_pool;
		$this->draw_from_undrawn_pool = $draw_from_undrawn_pool;
	}

	/**
	 * @return Response
	 *
	 * @throws EmptyCardPoolException
	 * @throws EnumConstantsCouldNotBeResolvedException
	 * @throws EnumNotDefinedException
	 * @throws GameNotFoundException
	 * @throws InvalidCardIdException
	 * @throws NonUniqueResultException
	 * @throws OutOfCardsException
	 * @throws PlayerNotFoundException
	 */
	public function drawFromUndrawn(): Response
	{
		$game = $this->loadGame();
		$this->denyAccessUnlessGranted(GameVoter::DRAW_FROM_UNDRAWN, $game);

		$card = $this->draw_from_undrawn_pool->draw($game);

		$this->game_notifier->notify($game->getId(), $game->getState()->getPlayers()->getCurrentPlayer()->getId());

		return $this->render('Duizenden\game.html.twig', [
			'game' => $game,
			'drawn_card' => $card,
		]);
	}

	/**
	 * @param CardPool $meld_cards
	 *
	 * @return Response
	 *
	 * @throws CardNotFoundException
	 * @throws DrawCardException
	 * @throws EmptyCardPoolException
	 * @throws EnumConstantsCouldNotBeResolvedException
	 * @throws EnumNotDefinedException
	 * @throws GameNotFoundException
	 * @throws InvalidCardIdException
	 * @throws InvalidMeldException
	 * @throws MeldException
	 * @throws NonUniqueResultException
	 * @throws PlayerNotFoundException
	 * @throws HandException
	 */
	public function drawFromDiscarded(CardPool $meld_cards = null): Response
	{
		$game = $this->loadGame($this->session->get('game_id'));
		$this->denyAccessUnlessGranted(GameVoter::DRAW_FROM_DISCARDED, $game);

		if (null !== $meld_cards && $meld_cards->hasCards())
		{
			$this->draw_from_discarded_pool->drawAndMeld($game, $meld_cards->getCards());
		}
		else
		{
			$this->draw_from_discarded_pool->draw($game);
		}

		$this->game_notifier->notify($game->getId(), $game->getState()->getPlayers()->getCurrentPlayer()->getId());

		return $this->render('Duizenden\game.html.twig', [
			'game' => $game
		]);
	}
}