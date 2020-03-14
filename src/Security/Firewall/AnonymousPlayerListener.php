<?php

declare(strict_types=1);

namespace App\Security\Firewall;

use App\Security\Authentication\Token\AnonymousPlayerToken;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AnonymousPlayerListener
{
    private TokenStorageInterface $token_storage;
    private AuthenticationManagerInterface $authentication_manager;
    private string $identification_path;
    private string $validation_path;
    private string $success_path;
    private string $identification_form_type;
    private string $identification_form_field;
    private FormFactoryInterface $form_factory;

    public function __construct(
        TokenStorageInterface $token_storage,
        AuthenticationManagerInterface $authentication_manager,
        FormFactoryInterface $form_factory
    )
    {
        $this->token_storage = $token_storage;
        $this->authentication_manager = $authentication_manager;
        $this->form_factory = $form_factory;
    }

    public function __invoke(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$this->isUserAnonymous())
        {
            return;
        }

        try
        {
            if ($this->isAnonymousRequestAllowed($request))
            {
                return;
            } elseif ($this->isValidationRequest($request))
            {
                $this->handleToken($this->getNameFromForm($request));
                $this->setRedirectResponse($event, $this->success_path);
            } else
            {
                $this->setRedirectResponse($event, $this->identification_path);
            }
        } catch (AuthenticationException $e)
        {
            $this->resetToken();
            $session = $request->getSession();

            if ($session instanceof Session)
            {
                $session->getFlashBag()->add('error', $e->getMessage());
            }

            $this->setRedirectResponse($event, $this->identification_path);

            return;
        }
    }

    private function resetToken(): void
    {
        if ($this->token_storage->getToken() instanceof AnonymousPlayerToken)
        {
            $this->token_storage->setToken(null);
        }
    }

    private function handleToken(string $name): void
    {
        $token = new AnonymousPlayerToken($name);
        $auth_token = $this->authentication_manager->authenticate($token);
        $this->token_storage->setToken($auth_token);
    }

    private function getNameFromForm(Request $request): string
    {
        $form = $this->form_factory->create($this->identification_form_type);

        if ($form->handleRequest($request) && $form->isSubmitted() && $form->isValid())
        {
            return $form[$this->identification_form_field]->getData();
        }

        $error_messages = [];

        foreach ($form->getErrors(true, true) as $error)
        {
            $error_messages[] = $error->getMessage();
        }

        throw new AuthenticationException(implode(' ', $error_messages));
    }

    private function isUserAnonymous(): bool
    {
        $token = $this->token_storage->getToken();

        return null === $token || $token instanceof AnonymousPlayerToken;
    }

    private function isAnonymousRequestAllowed(Request $request): bool
    {
        $token = $this->token_storage->getToken();

        return ($token instanceof AnonymousPlayerToken && $token->isAuthenticated()) ||
            ($request->getPathInfo() == $this->identification_path);
    }

    private function isValidationRequest(Request $request): bool
    {
        return $request->getPathInfo() == $this->validation_path;
    }

    private function setRedirectResponse(RequestEvent $event, string $path): void
    {
        $response = new RedirectResponse($path);
        $event->setResponse($response);
    }

    public function setIdentificationPath(string $path): void
    {
        $this->identification_path = $path;
    }

    public function setValidationPath(string $path): void
    {
        $this->validation_path = $path;
    }

    public function setSuccessPath(string $path): void
    {
        $this->success_path = $path;
    }

    public function setIdentificationFormType(string $identification_form_type): void
    {
        $this->identification_form_type = $identification_form_type;
    }

    public function setIdentificationFormField(string $identification_form_field): void
    {
        $this->identification_form_field = $identification_form_field;
    }
}