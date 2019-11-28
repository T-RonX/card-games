<?php

declare(strict_types=1);

namespace App\Lobby;

use App\Chat\ChatRoom;
use App\Chat\ChatRoomFactory;
use App\Entity\Player;
use Exception;

class Lobby
{
	public const ID = 'lobby';

	private ChatRoomFactory $chat_room_factory;

	private ChatRoom $chat_room;

	public function __construct(ChatRoomFactory $chat_room_factory)
	{
		$this->chat_room_factory = $chat_room_factory;
	}

	public function initialize(): void
	{
		static $is_initialized = false;

		if (!$is_initialized)
		{
			$this->chat_room = $this->chat_room_factory->create(self::ID);
			$is_initialized = true;
		}
	}

	/**
	 * @throws Exception
	 */
	public function addMessage(string $message, Player $player): void
	{
		$this->chat_room->addMessage($message, $player);
	}

	/**
	 * @return string[][]
	 */
	public function getMessages(): array
	{
		return $this->chat_room->getMessages();
	}

	public function updatePlayerActivity(Player $player): void
	{
		$this->chat_room->updatePlayerActivity($player);
	}

	public function playerEntered(Player $player): void
	{
		$this->chat_room->playerEntered($player);
	}

	/**
	 * @return Player[]
	 */
	public function getPlayers(): array
	{
		return $this->chat_room->getPlayers();
	}
}