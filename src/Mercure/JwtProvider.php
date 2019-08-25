<?php

namespace App\Mercure;

use Ahc\Jwt\JWT;

class JwtProvider
{
	/**
	 * @var JWT
	 */
	private $jwt;

	/**
	 * @param JWT $jwt
	 */
	public function __construct(JWT $jwt)
	{
		$this->jwt = $jwt;
	}

	public function __invoke(): string
	{
		$payload = $this->generatePayload(['publish' => '*']);
		$jwt = $this->jwt->encode($payload);

		return $jwt;
	}

	private function generatePayload(array $payload): array
	{
		return [
			'mercure' => $payload
		];
	}
}
