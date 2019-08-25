<?php

namespace App\Chat;

use App\Chat\Entity\ChatMessage;
use App\Chat\Entity\ChatRoom as ChatRoomEntity;
use App\Chat\Entity\ChatRoomPlayer;
use App\Entity\Player;
use App\Lobby\Lobby;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\Update;

class ChatRoom
{
	/**
	 * @var ChatRoomEntity
	 */
	private $chat_room_entity;

	/**
	 * @var EntityManagerInterface
	 */
	private $entity_manager;

	/**
	 * @var Publisher
	 */
	private $publisher;

	/**
	 * @param EntityManagerInterface $entity_manager
	 * @param ChatRoomEntity $chat_room_entity
	 * @param Publisher $publisher
	 */
	public function __construct(
		EntityManagerInterface $entity_manager,
		ChatRoomEntity $chat_room_entity,
		Publisher $publisher
	)
	{
		$this->entity_manager = $entity_manager;
		$this->chat_room_entity = $chat_room_entity;
		$this->publisher = $publisher;
	}

	/**
	 * @param string $message
	 * @param Player|null $player
	 *
	 * @throws Exception
	 */
	public function addMessage(string $message, Player $player): void
	{
		$message = $this->createMessage($message, $player);

		$this->saveMessage($message);
		$this->publishMessage($message);
	}

	/**
	 * @param string $message
	 * @param Player|null $player
	 *
	 * @return ChatMessage|string
	 *
	 * @throws Exception
	 */
	private function createMessage(string $message, Player $player)
	{
		$message = (new ChatMessage())
			->setChatRoom($this->chat_room_entity)
			->setPlayer($player)
			->setCreatedAt(new DateTimeImmutable())
			->setMessage($message);

		return $message;
	}

	/**
	 * @param ChatMessage $message
	 */
	private function saveMessage(ChatMessage $message): void
	{
		$this->chat_room_entity->getChatMessages()->add($message);

		$this->entity_manager->persist($message);
		$this->entity_manager->flush();
	}

	/**
	 * @param ChatMessage $message
	 */
	private function publishMessage(ChatMessage $message): void
	{
		$update = new Update(sprintf('urn:lobby:%s', Lobby::ID), json_encode($this->createMessageData($message)));

		($this->publisher)($update);
	}

	/**
	 * @param ChatMessage $message
	 *
	 * @return string[]
	 */
	private function createMessageData(ChatMessage $message): array
	{
		return [
			'name' => $this->getPlayerNameFromMessage($message),
			'message' => $message->getMessage(),
			'date' => $message->getCreatedAt()->format('d-m-Y H:i:s')
		];
	}

	/**
	 * @param ChatMessage $message
	 *
	 * @return string
	 */
	private function getPlayerNameFromMessage(ChatMessage $message): string
	{
		return null == $message->getPlayer() ? 'Anonymous' : $message->getPlayer()->getName();
	}

	/**
	 * @return string[][]
	 */
	public function getMessages(): array
	{
		$messages = [];

		foreach ($this->chat_room_entity->getChatMessages() as $message)
		{
			$messages[] = $this->createMessageData($message);
		}

		return $messages;
	}

	public function addPlayer(Player $player): void
	{
		$chat_room_player = new ChatRoomPlayer();
		$chat_room_player->setPlayer($player);
		$chat_room_player->setLastActivityAt(new DateTime());
		$this->entity_manager->persist($chat_room_player);

		$this->chat_room_entity->addChatRoomPlayer($chat_room_player);
		$this->entity_manager->flush();
	}

	/**
	 * @return Player[]
	 */
	public function getPlayers(): array
	{
		$players = [];

		foreach ($this->chat_room_entity->getChatRoomPlayers() as $chat_room_player)
		{
			$players[$chat_room_player->getPlayer()->getUuid()] = $chat_room_player->getPlayer();
		}

		return $players;
	}

	public function getChatRoomPlayer(Player $player): ?ChatRoomPlayer
	{
		foreach ($this->chat_room_entity->getChatRoomPlayers() as $chat_room_player)
		{
			if ($chat_room_player->getPlayer()->getUuid() === $player->getUuid())
			{
				return $chat_room_player;
			}
		}

		return null;
	}

	public function updatePlayerActivity(Player $player): void
	{
		$this->entity_manager->merge($player);
		$this->entity_manager->getUnitOfWork()->getEntityState($player);
		$chat_room_player = $this->getChatRoomPlayer($player);

		if (!$chat_room_player)
		{
			$chat_room_player = new ChatRoomPlayer();
			$chat_room_player->setPlayer($player);
		}

		$chat_room_player->setLastActivityAt(new DateTime());
		$this->chat_room_entity->addChatRoomPlayer($chat_room_player);

		$this->entity_manager->persist($chat_room_player);
		$this->entity_manager->flush();
	}
}
