<?php

namespace App\Controller\Account;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    // Le compte de mon utilisateur
    #[Route('/compte', name: 'app_account')]
    public function index(OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->findBy([
            'user' => $this->getUser(),
            'state' => [2,3]
        ]);

        return $this->render('account/index.html.twig', [
            'order' => $order
        ]);
    }

}
