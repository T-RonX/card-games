<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\User\CreateType;
use App\User\Creator;
use App\User\Exception\UsernameAlreadyInUseException;
use App\User\User\UserProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    private Creator $user_creator;
    private UserProvider $user_provider;

    public function __construct(
        Creator $user_creator,
        UserProvider $user_provider
    )
    {
        $this->user_creator = $user_creator;
        $this->user_provider = $user_provider;
    }

    public function newUser(): Response
    {
        $form = $this->createForm(CreateType::class);

        return $this->render('User\new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws UsernameAlreadyInUseException
     */
    public function createUser(Request $request): Response
    {
        $form = $this->createForm(CreateType::class);

        if ($form->handleRequest($request) && $form->isSubmitted() && $form->isValid())
        {
            $username = $form['username']->getData();
            $password = $form['password']->getData();

            $this->user_creator->createUserAndLogin($this->user_provider->getPlayer(), $username, $password);

            return $this->redirectToRoute('profile.view');
        }

        return $this->render('User\new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}