<?php

declare(strict_types=1);

namespace App\Mercure;

class SubscriberIdGenerator
{
	private string $secret;

	public function __construct(string $secret = '')
	{
		$this->secret = $secret;
	}

	public function generate(string $player_id): string
	{
		return md5($player_id.$this->secret);
	}
}