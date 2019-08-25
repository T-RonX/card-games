<?php

namespace App\Security\Authentication\Provider;

use App\Security\UserProvider\AnonymousPlayerProvider as UserProvider;
use App\Security\Authentication\Token\AnonymousPlayerToken;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class AnonymousPlayerProvider implements AuthenticationProviderInterface
{
	/**
	 * @var UserProvider
	 */
	private $user_provider;

	public function __construct(UserProvider $user_provider)
	{
		$this->user_provider = $user_provider;
	}

	/**
	 * @throws NonUniqueResultException
	 */
	public function authenticate(TokenInterface $token): TokenInterface
	{
		$player = $this->user_provider->getOrCreatePlayer($token->getUsername());

		$auth_token = new AnonymousPlayerToken();
		$auth_token->setUser($player);
		$auth_token->setAuthenticated(true);

		return $auth_token;
	}

	public function supports(TokenInterface $token)
	{
		return $token instanceof AnonymousPlayerToken;
	}
}