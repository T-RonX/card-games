<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\Profile\UpdateType;
use App\Profile\Updater;
use App\User\User\UserProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends AbstractController
{
    private UserProvider $user_provider;
    private Updater $updater;

    public function __construct(
        UserProvider $user_provider,
        Updater $updater
    )
    {
        $this->user_provider = $user_provider;
        $this->updater = $updater;
    }

    public function view(): Response
    {
        $player = $this->user_provider->getPlayer();
        $form = $this->createForm(UpdateType::class, null, [
            'playername' => $player->getName()
        ]);

        return $this->render('Profile\view.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function update(Request $request): Response
    {
        $player = $this->user_provider->getPlayer();
        $form = $this->createForm(UpdateType::class, null, [
            'playername' => $player->getName()
        ]);

        if ($form->handleRequest($request) && $form->isSubmitted() && $form->isValid())
        {
            $playername = $form['playername']->getData();

            $this->updater->updateProfile($player, $playername);
            
            return $this->redirectToRoute('profile.view');
        }

        return $this->render('Profile\view.html.twig', [
            'form' => $form->createView()
        ]);
    }
}