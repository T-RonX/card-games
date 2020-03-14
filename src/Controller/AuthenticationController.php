<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\Authentication\AnonymousLoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationController extends AbstractController
{
    public function login(): Response
    {
        $form = $this->createForm(AnonymousLoginType::class);

        return $this->render('Authentication/name.html.twig', [
            'form' => $form->createView()
        ]);
    }
}