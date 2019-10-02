<?php

namespace App\Games\Duizenden\Networking\Message;

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
	 * @var GameEventMessageCompiler
	 */
	private $compiler;

	public function __construct(
		SubscriberIdGenerator $subscriber_id_generator,
		GameEventMessageCompiler $compiler
	)
	{
		$this->subscriber_id_generator = $subscriber_id_generator;
		$this->compiler = $compiler;
	}

	/**
	 * @param GameEventData $message
	 *
	 * @return Update
	 */
	public function compile(GameEventData $message): Update
	{
		$topic = $this->createTopic($message);
		$data = json_encode($message->create());

		return new Update($topic, $data);
	}

	private function createTopic(GameEventData $message): string
	{
		return sprintf(self::TOPIC_FORMAT, $message->getTopicType(), $this->subscriber_id_generator->generate($message->getIdentifier()));
	}
}