<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    #[Route('/coordonateur', name: 'coordonateur')]
    public function coordonateur(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    #[Route('/rh', name: 'rh')]
    public function rh(): Response
    {
        return $this->render('rh/index.html.twig');
    }

    #[Route('/stagiaire', name: 'stagiaire')]
    public function stagiaire(): Response
    {
        return $this->render('stagiaire/index.html.twig');
    }

    #[Route('/login', name: 'login')]
    public function login(): Response
    {
        return $this->render('login.html.twig');
    }
}
