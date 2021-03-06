<?php

declare(strict_types=1);

namespace App\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class AnonymousPlayerToken extends AbstractToken
{
	public function __construct(string $name = null)
	{
		if (null !== $name)
		{
			$this->setUser($name);
		}

		parent::__construct([]);
	}

	/**
	 * @return string[]
	 */
	public function getCredentials(): array
	{
		return [];
	}
}