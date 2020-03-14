<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\Authentication\AnonymousLoginType;
use App\Form\Authentication\UserLoginType;
use App\User\User\UserProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthenticationController extends AbstractController
{
    private UserProvider $user_provider;

    public function __construct(UserProvider $user_provider)
    {
        $this->user_provider = $user_provider;
    }

    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->user_provider->isAuthenticated())
        {
            $this->addFlash('notice', 'You are already logged in.');

            return $this->redirectToRoute('profile.view');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $last_username = $authenticationUtils->getLastUsername();
        $anonymous_form = $this->createForm(AnonymousLoginType::class);
        $user_form = $this->createForm(UserLoginType::class);

        ;

        return $this->render('Authentication/name.html.twig', [
            'last_username' => $last_username,
            'error' => $error,
            'anonymous_form' => $anonymous_form->createView(),
            'user_form' => $user_form->createView()
        ]);
    }
}