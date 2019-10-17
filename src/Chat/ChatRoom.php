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

	public function playerEntered(Player $player)
	{
		$this->publishPlayerEntered($player);
	}

	private function publishPlayerEntered(Player $player)
	{
		($this->publisher)($this->createPlayerEnteredLobbyUpdate($player));
	}

	/**
	 * @param Player $player
	 *
	 * @return Update
	 */
	private function createPlayerEnteredLobbyUpdate(Player $player): Update
	{
		$data = [
			'type' => 'player_joined',
			'data' => $this->createPlayerEnteredLobbyMessageData($player),
		];

		return new Update(sprintf('urn:lobby:%s', Lobby::ID), json_encode($data));

	}

	/**
	 * @param Player $player
	 *
	 * @return string[]
	 */
	private function createPlayerEnteredLobbyMessageData(Player $player): array
	{
		return [
			'name' => $player->getName(),
			'id' => $player->getUuid()
		];
	}

	/**
	 * @param string $message
	 * @param Player|null $player
	 *
	 * @throws Exception
	 */
	public function addMessage(string $message, Player $player): void
	{
		$message = $this->cleanMessage($message);
		$message = $this->createMessage($message, $player);

		$this->saveMessage($message);
		$this->publishMessage($message);
	}

	/**
	 * @param string $message
	 * @param Player|null $player
	 *
	 * @return ChatMessage
	 *
	 * @throws Exception
	 */
	private function createMessage(string $message, Player $player): ChatMessage
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

		($this->publisher)($this->createMessageUpdate($message));
	}

	private function createMessageUpdate(ChatMessage $message): Update
	{
		$data = [
			'type' => 'message_in',
			'data' => $this->createMessageData($message),
		];

		return new Update(sprintf('urn:lobby:%s', Lobby::ID), json_encode($data));
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
			'id' => $this->getPlayerIdFromMessage($message),
			'message' => $this->cleanMessage($message->getMessage()),
			'date' => $message->getCreatedAt()->format('H:i')
		];
	}

	private function cleanMessage(string $message): string
	{
		$message = strtr($message, [
			"\r\n" => "\n",
			"\n\r" => "\n",
			"\r" => "\n"
		]);

		$message = preg_replace("/\n\n\n+/", "\n\n", $message);

		return mb_strcut(trim($message), 0, 1000);
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
	 * @param ChatMessage $message
	 *
	 * @return string
	 */
	private function getPlayerIdFromMessage(ChatMessage $message): string
	{
		return null == $message->getPlayer() ? '' : $message->getPlayer()->getId();
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
