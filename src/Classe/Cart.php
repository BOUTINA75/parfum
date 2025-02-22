<?php 

namespace App\Classe;

use Symfony\Component\HttpFoundation\RequestStack;

class Cart
{
    public function __construct(private RequestStack $requestStack)
    {

    }

        
    /**
     * add()
     * Fonction permettant l'ajout d'un produit au panier
     * et me permetttre de repondre a ma méthode que je mets dans mon controleur
     */
    public function add($product)
    {
        
        // Apeller la session de symfony doc
        // $session = $this->requestStack->getSession();

        //permet de recupérai le panier en cours et ensuite de lui injecter des nouvelles information,
        $cart = $this->getCart();

        // Ajouter une qtity +1 à mon produit
        // si mon produit et déjà dans mon panier à ce moment la tu me fai une quantité + 1 mais une quantité de ce qu'il y a déjà dans mon panier pour ce produit (tu va dans cart, tu va dans product get id, tu va chercher l'entré quantité et tu rajoute un +1) ça c'est dans le cas ou dans mon panier ici (if) j'ai déjà mon produit, sinon mon produit n'existe pas donc je le crée (iseet= est-ce que cette entré dans mon tableau existe bel et bien)
        if (isset($cart[$product->getId()])) {
            $cart[$product->getId()] = [
                'object' => $product,
                'qty' => $cart[$product->getId()]['qty'] + 1
            ];
        } else {
            $cart[$product->getId()] = [
                'object' => $product,
                'qty' => 1
            ];
        }
        
        // Créer ma session Cart (je lui passe deux paramètre a cette fonction set premier paramètre le nom de la session qu'on souhaite faire transiter chez notre utilisateur et en deuxième la valeur $cart qu'on retrouve dans mon tableau, a l'interieur de se tableau je retrouve mon objet avec mon produit etv la quantité)
        $this->requestStack->getSession()->set('cart', $cart);
    }
    
    /**
     * decrease()
     * Fonction permettant la suppression d'une quantity d'un produit au panier
     *
     */

    public function decrease($id)
    {
        $cart = $this->getCart();

        if($cart[$id]['qty'] > 1) {
            $cart[$id]['qty'] = $cart[$id]['qty'] - 1;
        } else {
            unset($cart[$id]);
        }

        $this->requestStack->getSession()->set('cart', $cart);
    }

    /**
     * fullQuantity()
     * Fonction retournant le nombre total de produit au pannier 
     *
     */

    public function fullQuantity()
    {
        $cart = $this->getCart();
        $quantity = 0;

        // Si mon panier n'existe pas tu me return la quantité (zéro)
        if (!isset($cart)) {
            return $quantity;
        }

        foreach ($cart as $product) {
            $quantity = $quantity + $product['qty'];
        }

        return $quantity;
    }

    /**
     * getTotalWt()
     * Fonction retournant le prix total des produits au panier
     *
     */

    public function getTotalWt()
    {
        $cart = $this->getCart();
        // Je crée une variable price à 0
        $price = 0;

        // SI le carte n'existe pas tu me return price
        if (!isset($cart)) {
            return $price;
        }

        // je demande ensuite de bouclé sur chaque entrée de notre panier (mon prix est égal au prix que j'ai déjà, donc à savoir pour le moment 0, mais a chaque fois qui rentre dans la boucle il garde l'ancien calcul qu'il a fait) le prix plus le prix de mon produit qui est accessible dans Object, j'accède au méthodes de mon objet (getPriceWT qui est le prix total TTC) et je fais fois product quantite
        foreach ($cart as $product) {
            $price = $price + ($product['object']->getPriceWt() * $product ['qty']);
        }

        return $price;
    }

    /**
     * remove()
     * Fonction permettant de supprimer totalement le panier 
     *
     */

    public function remove()
    {
        return $this->requestStack->getSession()->remove('cart');
    }

    /**
     * getCart()
     * Fonction retournant le panier en cour
     *
     */
    public function getCart()
    {
        return $this->requestStack->getSession()->get('cart');
    }
}

