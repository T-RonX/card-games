<?php

namespace App\Games\Duizenden\Notifier;

use App\CardPool\Exception\EmptyCardPoolException;
use App\Game\GameInterface;
use App\Games\Duizenden\Game;
use App\Games\Duizenden\Networking\Message\Action\DealAction;
use App\Games\Duizenden\Networking\Message\Action\DiscardCardAction;
use App\Games\Duizenden\Networking\Message\Action\DrawCardAction;
use App\Games\Duizenden\Networking\Message\Action\ExtendMeldAction;
use App\Games\Duizenden\Networking\Message\Action\MeldCardsAction;
use App\Games\Duizenden\Networking\Message\ActionInterface;
use App\Games\Duizenden\Networking\Message\ActionType;
use App\Games\Duizenden\Networking\Message\GameEventMessage;
use App\Games\Duizenden\Networking\Message\InvalidActionException;
use App\Games\Duizenden\Networking\Message\MessageBuilder;
use App\Games\Duizenden\Networking\Message\StatusType;
use App\Games\Duizenden\Networking\Message\TopicType;
use App\Games\Duizenden\Player\Exception\PlayerNotFoundException;
use App\Games\Duizenden\Score\Exception\UnmappedCardException;
use App\Games\Duizenden\Workflow\TransitionType;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Workflow\StateMachine;

class GameNotifier
{
	/**
	 * @var Publisher
	 */
	private $publisher;

	/**
	 * @var MessageBuilder
	 */
	private $builder;

	/**
	 * @var StateMachine
	 */
	private $state_machine;

	public function __construct(
		Publisher $publisher,
		MessageBuilder $message_builder,
		StateMachine $state_machine
	)
	{
		$this->publisher = $publisher;
		$this->builder = $message_builder;
		$this->state_machine = $state_machine;
	}

	/**
	 * @param string $identifier
	 * @param Game $game
	 * @param TopicType $topic
	 * @param StatusType|null $status
	 *
	 * @return GameEventMessage
	 *
	 * @throws PlayerNotFoundException
	 * @throws UnmappedCardException
	 */
	public function createGameMessageBuilder(string $identifier, Game $game, TopicType $topic, ?StatusType $status = null): GameEventMessage
	{
		$state = $game->getState();

		$message = $this->createMessageBuilder($identifier, $topic, $status ?? StatusType::OK());
		$message->setGameId($game->getId());
		$message->setCurrentPlayer($state->getPlayers()->getCurrentPlayer());
		$message->setAllowedActions($this->createAllowedActions($game));
		$message->setUndrawnPool($state->getUndrawnPool());
		$message->setDiscardedPool($state->getDiscardedPool());
		$message->setPlayers($state->getPlayers()->getFreshLoopIterator());
		$this->addPlayerScores($message, $game);

		return $message;
	}

	/**
	 * @param GameEventMessage $message
	 * @param Game $game
	 *
	 * @throws UnmappedCardException
	 * @throws PlayerNotFoundException
	 */
	private function addPlayerScores(GameEventMessage $message, Game $game): void
	{
		$score = $game->getScoreCalculator()->calculateGameScore($game->getId());

		foreach ($message->getPlayers() as $player)
		{
			$message->setPlayerScore($player, $score->getTotalPlayerScore($player->getId()));
		}
	}

	/**
	 * @param Game $game
	 *
	 * @return ActionType[]
	 */
	private function createAllowedActions(Game $game): array
	{
		$actions = [];

		foreach ($this->state_machine->getEnabledTransitions($game) as $marking)
		{
			switch ($marking->getName())
			{
				case TransitionType::DEAL:
					$actions[] = ActionType::DEAL();
					break;

				case TransitionType::DISCARD_END_TURN:
				case TransitionType::DISCARD_END_ROUND:
				case TransitionType::DISCARD_END_GAME:
					$actions[] = ActionType::DISCARD_CARD();
					break;

				case TransitionType::DRAW_FROM_DISCARDED:
				case TransitionType::DRAW_FROM_UNDRAWN:
					$actions[] = ActionType::DRAW_CARD();
					break;

				case TransitionType::MELD:
					$actions[] = ActionType::MELD_CARDS();
					break;

				case TransitionType::EXTEND_MELD:
					$actions[] = ActionType::EXTEND_MELD();
					break;
			}
		}

		return array_unique($actions);
	}

	public function createMessageBuilder(string $identifier, TopicType $topic, ?StatusType $status = null): GameEventMessage
	{
		return $this->builder->createMessageBuilder($topic, $identifier, $status);
	}

	/**
	 * @param GameEventMessage $message
	 *
	 * @throws EmptyCardPoolException
	 * @throws InvalidActionException
	 */
	public function notifyMessage(GameEventMessage $message): void
	{
		($this->publisher)($message->create());
	}
}