<?php

declare(strict_types=1);

namespace App\Mercure;

use Ahc\Jwt\JWT;

class JwtProvider
{
	private JWT $jwt;

	public function __construct(JWT $jwt)
	{
		$this->jwt = $jwt;
	}

	public function __invoke(): string
	{
		$payload = $this->generatePayload(['publish' => '*']);

		return $this->jwt->encode($payload);
	}

	private function generatePayload(array $payload): array
	{
		return [
			'mercure' => $payload
		];
	}
}
