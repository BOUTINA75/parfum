<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    #[Route('/categorie/{slug}', name: 'app_category')]
    public function index($slug, CategoryRepository $categoryRepository): Response
    {
        // 1. J'ouvrais une connexion avec ma BDD (CategoryRepository)
        // 2. Connecte toi à la table qui s'appelle Category
        // 3. Fais une action en base de donnée
        $category = $categoryRepository->findOneBySlug($slug);
        


        return $this->render('category/index.html.twig', [
            'category' => $category,
        ]);
    }
}
