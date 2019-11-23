<?php

namespace App\Session;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class SessionSecretListener
{
	const SECRET_KEY_KEY = 'secret_key';

	/**
	 * @param RequestEvent $event
	 */
	public function __invoke(RequestEvent $event): void
	{
		$session = $event->getRequest()->getSession();

		if (!$this->doSetSecret($session))
		{
			return;
		}

		$this->setSecret($session);
	}

	/**
	 * @param SessionInterface $session
	 *
	 * @return bool
	 */
	private function doSetSecret(SessionInterface $session): bool
	{
		return null === $session->get(self::SECRET_KEY_KEY);
	}

	/**
	 * @param SessionInterface $session
	 */
	private function setSecret(SessionInterface $session): void
	{
		$session->set(self::SECRET_KEY_KEY, $this->generateSecret());
	}

	/**
	 * @return string
	 */
	private function generateSecret(): string
	{
		return md5(mt_rand().microtime(true));
	}
}
