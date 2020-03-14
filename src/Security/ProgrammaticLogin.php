<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class ProgrammaticLogin
{
    private TokenStorageInterface $token_storage;
    private EventDispatcherInterface $event_dispatcher;
    private RequestStack $request_stack;

    public function __construct(
        TokenStorageInterface $token_storage,
        EventDispatcherInterface $event_dispatcher,
        RequestStack $request_stack)
    {
        $this->token_storage = $token_storage;
        $this->event_dispatcher = $event_dispatcher;
        $this->request_stack = $request_stack;
    }

    public function login(UserInterface $user): void
    {
        $token = new UsernamePasswordToken($user, $user->getPassword(), "main", $user->getRoles());
        $this->token_storage->setToken($token);
        $this->fireInteractiveLoginEvent($token);
    }

    private function fireInteractiveLoginEvent(TokenInterface $token): void
    {
        if (!$request = $this->request_stack->getCurrentRequest())
        {
            return;
        }

        $event = new InteractiveLoginEvent($request, $token);
        $this->event_dispatcher->dispatch($event, "security.interactive_login");
    }
}