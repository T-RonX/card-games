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
use App\Games\Duizenden\Game;
use App\Games\Duizenden\Meld\Exception\MeldException;
use App\Games\Duizenden\StateCompiler\ActionType;
use App\Games\Duizenden\StateCompiler\InvalidActionException;
use App\Games\Duizenden\StateCompiler\TopicType;
use App\Games\Duizenden\Persistence\Exception\GameNotFoundException;
use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;
use App\Games\Duizenden\Player\PlayerInterface;
use App\Games\Duizenden\Score\Exception\UnmappedCardException;
use App\Security\Voter\Duizenden\GameVoter;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DrawCardController extends AbstractController
{
	use LoadGameTrait;
	use NotifyPlayersTrait;

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
	 */
	public function __construct(
		FromDiscardedPool $draw_from_discarded_pool,
		FromUndrawnPool $draw_from_undrawn_pool
	)
	{
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
	 * @throws UnmappedCardException
	 */
	public function drawFromUndrawn(int $target = null): Response
	{
		$game = $this->loadGame();
		$this->denyAccessUnlessGranted(GameVoter::DRAW_FROM_UNDRAWN, $game);

		$this->draw_from_undrawn_pool->draw($game, $target);

		$this->notifyPlayersIndividually($game, ActionType::DRAW_FROM_UNDRAWN(), $target);

		return $this->json([]);
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
	 * @throws HandException
	 * @throws InvalidCardIdException
	 * @throws InvalidMeldException
	 * @throws MeldException
	 * @throws NonUniqueResultException
	 * @throws PlayerNotFoundException
	 * @throws UnmappedCardException
	 */
	public function drawFromDiscarded(CardPool $meld_cards = null, int $target = null): Response
	{
		$game = $this->loadGame();
		$this->denyAccessUnlessGranted(GameVoter::DRAW_FROM_DISCARDED, $game);

		if (null !== $meld_cards && $meld_cards->hasCards())
		{
			$this->draw_from_discarded_pool->drawAndMeld($game, $meld_cards->getCards());

			$action = ActionType::DRAW_FROM_DISCARDED_AND_MELD();
		}
		else
		{
			$this->draw_from_discarded_pool->draw($game, $target);
			$action = ActionType::DRAW_FROM_DISCARDED();
		}

		$this->notifyPlayersIndividually($game, $action, $target);

		return $this->json([]);
	}

	/**
	 * @param Game $game
	 * @param ActionType $action
	 *
	 * @throws PlayerNotFoundException
	 * @throws UnmappedCardException
	 */
	private function notifyPlayersIndividually(Game $game, ActionType $action, int $target = null)
	{
		$current_player = $game->getState()->getPlayers()->getCurrentPlayer();

		foreach ($game->getState()->getPlayers()->getFreshLoopIterator() as $player)
		{
			$message = $this->createNotifyPlayerMessage(
				$player->getId(),
				$game,
				$game->getState()->getPlayers()->getCurrentPlayer(),
				$action,
				TopicType::PLAYER_EVENT()
			);

			if ($action->getValue() === ActionType::DRAW_FROM_DISCARDED_AND_MELD)
			{
				$melds = $game->getState()->getPlayers()->getCurrentPlayer()->getMelds();
				$message->setExtras([
					'meld_id' => $melds->count() - 1,
					'cards_melted' => $melds->last()->getCards()->getIdentifiers(),
				]);
			}

			if (null !== $target)
			{
				$message->setExtras([
					'target' => $target,
				]);
			}

			if ($player->equals($current_player))
			{
				$message->addPlayersFullCardPool($player->getId());
			}

			$this->game_notifier->notifyMessage($message);
		}
	}
}