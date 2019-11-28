<?php

declare(strict_types=1);

namespace App\Session;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class SessionSecretListener
{
	const SECRET_KEY_KEY = 'secret_key';

	public function __invoke(RequestEvent $event): void
	{
		$session = $event->getRequest()->getSession();

		if (!$this->doSetSecret($session))
		{
			return;
		}

		$this->setSecret($session);
	}

	private function doSetSecret(SessionInterface $session): bool
	{
		return null === $session->get(self::SECRET_KEY_KEY);
	}

	private function setSecret(SessionInterface $session): void
	{
		$session->set(self::SECRET_KEY_KEY, $this->generateSecret());
	}

	private function generateSecret(): string
	{
		return md5(mt_rand().microtime(true));
	}
}
