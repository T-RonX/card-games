<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends AbstractController
{
    public function view(): Response
    {
        return $this->render('Profile\view.html.twig');
    }
}