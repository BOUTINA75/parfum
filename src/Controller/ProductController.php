<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/produit/{slug}', name: 'app_product')]
    public function index($slug, ProductRepository $productRepository): Response
    {
        // variable $product qui contient une requête qui va aller chercher un findOnBySlug j'en veux qu'un seul et je veux quoi ba une slug(en paramètre le slug qu'il doit aller chercher mon produit) et ce slug, je le récupepère parce que je les mis en paramètre dans ma fonction et ce paramètre de fonction et lui même relié à l'argument qui se trouve dans l'URL qui peux aussi changer
        $product = $productRepository->findOneBySlug($slug);

        // si aucun produit n'existe à ce moment là, tu me redirige vers ma home
        if (!$product) {
            return $this->redirectToRoute('app_home');
        }
        
        // cette variable $product, je la fait transiter dans ma template
        return $this->render('product/index.html.twig', [
            'product' => $product,
        ]);
    }
}
