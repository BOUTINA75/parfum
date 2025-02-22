<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    // je suis sur le chemin racine / 
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        // la route renvoi sur le fichier index.html.twig dans mon dossier home
        return $this->render('home/index.html.twig');
    }
}
