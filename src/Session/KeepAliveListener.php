<?php

declare(strict_types=1);

namespace App\Session;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * @TODO: should solve this with garbage collecting
 * @see https://github.com/symfony/symfony/issues/2171
 */
class KeepAliveListener
{
	public function __invoke(ResponseEvent $event): void
	{
		// todo
	}
}
