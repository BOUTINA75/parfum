<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    //j'+ai besoin de dire à symfony ici, il y a un paramètre qui va changer de catégorie en catégorie, ce n'est pas le nom de la catégorie, mais c'est le slug. Je donne un argument que je souhaite utiliser dans l'URL, je le répliquer en argument de la fonction concernée, il y a donc une variable qui pourte le même nom dans ma fonction index et qui s'appelle slug
    #[Route('/categorie/{slug}', name: 'app_category')]

    // Repository c'est un fichier tout simple qui va nous permettre de faire un lien facile entre l'entité en base de donné et mon fichier php dans le controlleur (c'est une classe qui nous permet de faire des requêtes dans une table dédiée)
    public function index($slug, CategoryRepository $categoryRepository): Response
    {
        // 1. J'ouvrais une connexion avec ma BDD (CategoryRepository)
        // 2. Connecte toi à la table qui s'appelle Category
        // 3. Fais une action en base de donnée
        // findAll permet d'aller chercher toute mes catégorie moi j'ai besoin que de une catégorie dons j'ai findOneByslug (c'est à dire qu'on souhaite trouver un objet catégorie en fonction de quoi? en fonction du slug et à l'interieur je lui passe le slug)
        $category = $categoryRepository->findOneBySlug($slug);

        // si aucune catégorie n'existe redirige moi vers ma home
        if (!$category) {
            return $this->redirectToRoute('app_home');
        }
        


        // j'ai besoin de passer ma variable catégorie a ma vue twig
        return $this->render('category/index.html.twig', [
            'category' => $category,
        ]);
    }
}
