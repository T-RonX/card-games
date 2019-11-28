<?php

declare(strict_types=1);

namespace App\Games\Duizenden\Networking\Message;

use App\Mercure\SubscriberIdGenerator;
use Symfony\Component\Mercure\Update;

class MessageBuilder
{
	private const TOPIC_FORMAT = 'urn:%s:%s';

	private SubscriberIdGenerator $subscriber_id_generator;

	private GameEventMessageCompiler $compiler;

	public function __construct(
		SubscriberIdGenerator $subscriber_id_generator,
		GameEventMessageCompiler $compiler
	)
	{
		$this->subscriber_id_generator = $subscriber_id_generator;
		$this->compiler = $compiler;
	}

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