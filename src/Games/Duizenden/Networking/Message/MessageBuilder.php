<?php

namespace App\Games\Duizenden\Networking\Message;

use App\Mercure\SubscriberIdGenerator;
use Symfony\Component\Mercure\Update;

class MessageBuilder
{
	private const TOPIC_FORMAT = 'urn:%s:%s';
	private const TOPIC_TYPE = 'game_event';

	/**
	 * @var SubscriberIdGenerator
	 */
	private $subscriber_id_generator;

	public function __construct(SubscriberIdGenerator $subscriber_id_generator)
	{
		$this->subscriber_id_generator = $subscriber_id_generator;
	}

	public function createMessageBuilder(string $identifier, $status = 'ok', string $type = 'refresh'): GameEventMessage
	{
		return new GameEventMessage($this, self::TOPIC_TYPE, $identifier, $status, $type);
	}

	public function compile(GameEventMessage $message): Update
	{
		$topic = $this->createTopic($message);
		$data = json_encode([
			'status' => $message->getStatus(),
			'type' => $message->getType(),
			'data' => [
				'messages' => $message->getLogMessages(),
				'actions' => [],
				'cause_player' => $message->getCausePlayer(),
			]
		]);

		return new Update($topic, $data);
	}

	private function createTopic(GameEventMessage $message): string
	{
		return sprintf(self::TOPIC_FORMAT, $message->getTopicType(), $this->subscriber_id_generator->generate($message->getIdentifier()));
	}
}