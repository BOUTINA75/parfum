<?php

namespace App\Controller\Admin;

use App\Controller\Admin\UserCrudController;
use App\Entity\Carrier;
use App\Entity\Category;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // je souhaite rediriger ver la premier entité disponible (user) pour faire une redirection il me dt qu'il a besoin d'aller chercher l'instance, l'injection de dépendance (AdminUrlGenerator) ensuite il a besoin de nous faire une redirection en utilisant cette instance
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());

    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Projetparfum');
    }
    

    // cettte fonction va nous permettre de gérer les différents menuus de notre interface easyAdmin (méthode statique de la classe menuitem link to crud) qui nous permet de venir créer un nouvel item dans notre menu easyAdmin (le première paramètre de cette méthode statique link to crud, c'est le label, le nom de notre entrée menu. ensuite on a l'icone et ensuite on a l'entité liée)
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-list', User::class);
        yield MenuItem::linkToCrud('Catégories', 'fas fa-list', Category::class);
        yield MenuItem::linkToCrud('Produits', 'fas fa-list', Product::class);
        yield MenuItem::linkToCrud('Transporteurs', 'fas fa-list', Carrier::class);
        yield MenuItem::linkToCrud('Commandes', 'fas fa-list', Order::class);
    }
}
