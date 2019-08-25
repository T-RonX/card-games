<?php

namespace App\Mercure;

class SubscriberIdGenerator
{
	/**
	 * @var string
	 */
	private $secret;

	/**
	 * @param string $secret
	 */
	public function __construct(string $secret = '')
	{
		$this->secret = $secret;
	}

	/**
	 * @param string $player_id
	 *
	 * @return string
	 */
	public function generate(string $player_id): string
	{
		return md5($player_id.$this->secret);
	}
}