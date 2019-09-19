<?php

namespace App\Games\Duizenden\Networking\Message;

use App\CardPool\CardPoolInterface;
use App\CardPool\Exception\EmptyCardPoolException;
use App\Common\Meld\Meld;
use App\Common\Meld\Melds;
use App\Games\Duizenden\Initializer\DiscardedCardPool;
use App\Games\Duizenden\Player\PlayerInterface;
use App\Mercure\SubscriberIdGenerator;
use Symfony\Component\Mercure\Update;

class MessageBuilder
{
	private const TOPIC_FORMAT = 'urn:%s:%s';

	/**
	 * @var SubscriberIdGenerator
	 */
	private $subscriber_id_generator;

	/**
	 * @var ActionFactory
	 */
	private $action_factory;

	public function __construct(
		SubscriberIdGenerator $subscriber_id_generator,
		ActionFactory $action_factory
	)
	{
		$this->subscriber_id_generator = $subscriber_id_generator;
		$this->action_factory = $action_factory;
	}

	public function createMessageBuilder(TopicType $topic, string $identifier, StatusType $status): GameEventMessage
	{
		return new GameEventMessage($this, $topic, $identifier, $status);
	}

	/**
	 * @param GameEventMessage $message
	 *
	 * @return Update
	 *
	 * @throws EmptyCardPoolException
	 * @throws InvalidActionException
	 */
	public function compile(GameEventMessage $message): Update
	{
		$topic = $this->createTopic($message);
		$data = json_encode([
			'status' => $message->getStatus()->getValue(),
			'message' => $message->getLogMessages(),
			'game_id' => $message->getIdentifier(),
			'source' => [
				'player' => $this->createPlayerIdData($message->getSourcePlayer()),
				'action' => $this->createActionData($message->getSourceAction()),
			],
			'game_state' => [
				'current_player' => $this->createPlayerIdData($message->getCurrentPlayer()),
				'allowed_actions' => $this->createActionsData($message->getAllowedActions()),
				'undrawn_pool' => $this->createCardPoolData($message->getUndrawnPool(), false),
				'discarded_pool' => $this->createDiscardedCardPoolData($message->getDiscardedPool()),
				'players' => $this->createPlayersData($message->getPlayers()),
			]
		]);

		return new Update($topic, $data);
	}


	/**
	 * @param CardPoolInterface $pool
	 * @param bool $show_all_cards
	 *
	 * @return string[]
	 */
	private function createCardPoolData(CardPoolInterface $pool, bool $show_all_cards): array
	{
		$data = [
			'count' => $pool->getCardCount()
		];

		if ($show_all_cards)
		{
			$data['cards'] = [];

			foreach ($pool->getCards() as $card)
			{
				$data['cards'][] = strtolower($card->getIdentifier());
			}
		}

		return $data;
	}

	/**
	 * @param DiscardedCardPool $pool
	 *
	 * @return string[]
	 *
	 * @throws EmptyCardPoolException
	 */
	private function createDiscardedCardPoolData(DiscardedCardPool $pool): array
	{
		$data = [
			'count' => $pool->getCardCount(),
			'top_card' => strtolower($pool->getTopCard()->getIdentifier()),
			'is_first_card' => $pool->isFirstCard()
		];

		return $data;
	}

	/**
	 * @param PlayerInterface[]|iterable $players
	 *
	 * @return string[]
	 */
	private function createPlayersData(iterable $players): array
	{
		$data = [];

		foreach ($players as $player)
		{
			$data[] = $this->createPlayerData($player);
		}

		return $data;
	}

	/**
	 * @param PlayerInterface $player
	 *
	 * @return string[]
	 */
	private function createPlayerData(PlayerInterface $player): array
	{
		return [
			'id' => $player->getId(),
			'name' => $player->getName(),
			'hand' => $this->createCardPoolData($player->getHand(), false),
			'melds' => $this->createMeldsData($player->getMelds()),
		];
	}

	/**
	 * @param Melds $melds
	 *
	 * @return string[]
	 */
	private function createMeldsData(Melds $melds): array
	{
		$data = [];

		foreach ($melds as $meld)
		{
			$data[] = $this->createMeldData($meld);
		}

		return $data;
	}

	/**
	 * @param Meld $meld
	 *
	 * @return string[]
	 */
	private function createMeldData(Meld $meld): array
	{
		return [
			'type' => $meld->getType()->getValue(),
			'cards' => $this->createCardPoolData($meld->getCards(), true),
		];
	}

	/**
	 * @param PlayerInterface $player
	 *
	 * @return string[]
	 */
	private function createPlayerIdData(PlayerInterface $player): array
	{
		return [
			'id' => $player->getId()
		];
	}

	/**
	 * @param ActionType[] $actions
	 *
	 * @return string[]
	 *
	 * @throws InvalidActionException
	 */
	private function createActionsData(array $actions): array
	{
		$data = [];

		foreach ($actions as $action)
		{
			$data[] = $this->createActionData($action);
		}

		return $data;
	}

	/**
	 * @param ActionType $action
	 *
	 * @return string[]
	 *
	 * @throws InvalidActionException
	 */
	public function createActionData(ActionType $action): array
	{
		$action = $this->action_factory->create($action);

		return [
			'id' => $action->getType()->getValue(),
			'title' => $action->getTitle(),
			'description' => $action->getDescription()
		];
	}

	private function createTopic(GameEventMessage $message): string
	{
		return sprintf(self::TOPIC_FORMAT, $message->getTopicType(), $this->subscriber_id_generator->generate($message->getIdentifier()));
	}
}