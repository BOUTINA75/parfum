<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    #[Route('/mon-panier/{motif}', name: 'app_cart', defaults: [ 'motif' => null ])]
    public function index(Cart $cart, $motif): Response
    {
        if ($motif == "annulation") {
            $this->addflash(
                'info',
                'Paiement annulé : Vous pouvez mettre à jour votre panier et votre commande.'
            );
        }
        // La getCart elle va chercher la session en cours du panier
        return $this->render('cart/index.html.twig', [
            'cart' => $cart->getCart(),
            'totalWt' => $cart->getTotalWt()
        ]);
    }

    // Cette route va permet a l'utilisateur d'ajouter un produit a son panier, je rajoute un argument qui va me permettre de recuperer le produit concerne, en l'occurrence l'id. ensuite je renseigner ça en paramètre de la fonction associée à la route
    #[Route('/cart/add/{id}', name: 'app_cart_add')]

    // Cette fonction add, elle se retrouve avec une injection de dépendance de la classe carte (Je dis a ma fonction add je veux que tu fonction uniquement si tu injecte avec toi la dépendance de classe carte)
    public function add($id, Cart $cart, ProductRepository $productRepository, Request $request): Response
    {
        // Je veux que tu ajoutes dans une session (carte) le produit demandé par l'utilisateur et la quantité associée
        // lorsque j'ai besoin d'aller faire une requête à ma base de données, j'ai besoin d'un repository donc je vais chercher mon ProductRepository que je vais injecter à ma fonction add je mets ceci dans une variable qui porte le meme nom et ici ma variable product elle va faire une requêt en base de donné dans ma table product (elle va faire un findonebyid)
        $product = $productRepository->findOneById($id);

        // Et c'est ce product que je vais passer dans ma fonction add ici 
        $cart->add($product);

        // Flash message
        $this->addFlash(
            'success',
            "Produit correctement ajouté à votre panier."
        );

        return $this->redirect($request->headers->get('referer'));
    }

    #[Route('/cart/decrease/{id}', name: 'app_cart_decrease')]
    public function decrease($id, Cart $cart): Response
    {

        $cart->decrease($id);

        $this->addFlash(
            'success',
            "Produit correctement supprimée de votre panier."
        );

        return $this->redirectToRoute('app_cart');
    }

    // Je souhaite que cette route supprime mon panier
    #[Route('/cart/remove', name: 'app_cart_remove')]
    public function remove(Cart $cart): Response
    {
        // Il faut que je crée dans ma classe carte une fonction remove
        $cart->remove();
        // J'ai envie de renvoyer mon utilisateur vers ma homepage
        return $this->redirectToRoute('app_home');
    }
}
