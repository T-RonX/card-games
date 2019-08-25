<?php

namespace App\Form;

use App\Entity\Player;
use Closure;

trait PlayerFieldTrait
{
	private $field_ids = [];

	private function getPlayerLabelCallback(): Closure
	{
		$unique = $this->createUnique();

		return function (Player $player) use ($unique): string {
			return $this->createUniqueName($player, $unique);
		};
	}

	private function getPlayerValueCallback(): Closure
	{
		return static function (?Player $player): string { // @TODO: Why is $player null sometimes?
			return $player ? $player->getUuid() : '';
		};
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