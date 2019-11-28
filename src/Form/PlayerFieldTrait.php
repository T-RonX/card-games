<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Player;
use Closure;

trait PlayerFieldTrait
{
	/**
	 * @var int[]
	 */
	private array $field_ids = [];

	private function getPlayerLabelCallback(): Closure
	{
		$unique = $this->createUnique();

		return fn (Player $player) => $this->createUniqueName($player, $unique);
	}

	private function getPlayerValueCallback(): Closure
	{
		return static fn (?Player $player) => $player ? $player->getUuid() : '';
	}

	private function createUnique(): int
	{
		do {
			$this->field_ids[] = $rand = rand(0, 100);
		}
		while (!in_array($rand, $this->field_ids));

		return $rand;
	}

	private function createUniqueName(Player $player, int $field_key): string
	{
		static $unique = [];

		$player_name = $player->getName();

		if (!isset($unique[$field_key][$player_name]))
		{
			$unique[$field_key][$player_name] = 0;

			return $player_name;
		}

		++$unique[$field_key][$player_name];

		return $player_name . " ({$unique[$field_key][$player_name]})";
	}
}