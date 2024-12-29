<?php

namespace App\Controller;

use DateTime;
use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetail;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    /*
    * 1ère étape du tunnel d'achat
    * choix de l'adresse de livraison et du transporteur 
    *
    */
    #[Route('/commande/livraison', name: 'app_order')]
    public function index(): Response 
    {
        // Le problème semble être que la variable $addresses est définie à partir de $this->getUser()->getAddresses(), mais la méthode getAddresses() retourne un objet de type Collection, tandis que la méthode count() que vous utilisez pour vérifier si une adresse est présente fonctionne bien avec des collections Doctrine.
        $addresses = $this->getUser()->getAddresses(); 
        // dd($addresses);


        if (count($addresses) == 0) {
            return $this->redirectToRoute('app_account_address_form');
        }
        
        
        $form = $this->createForm(OrderType::class, null, [
            'addresses' => $addresses,
            'action' => $this->generateUrl('app_order_summary'),
        ]);


        return $this->render('order/index.html.twig', [
            'deliverForm' => $form->createView(),
        ]);
    }
    /*
    *2ère étape du tunnel d'achat
    *Récap de la commande de l'utilisateur 
    *Insertion en base de donnée
    *Préparation du paiement vers stripe
    */
    #[Route('/commande/recapitulatif', name: 'app_order_summary')]
    public function add(Request $request, Cart $cart, EntityManagerInterface $entityManager): Response
    {

        if ($request->getMethod() != 'POST') {
            return $this->redirectToRoute('app_cart');
        }

        $products = $cart->getcart();

        $form = $this->createForm(OrderType::class, null, [
            'addresses' => $this->getUser()->getAddresses(),
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            // Stocker les information en BDD

            // Création de la chaine adresse
            $addressObj = $form->get('addresses')->getData();

            $address = $addressObj->getFirstname().' '.$addressObj->getLastname().'<br/>';
            $address .= $addressObj->getAddress().'<br/>';
            $address .= $addressObj->getPostal().' '.$addressObj->getCity().'<br/>';
            $address .= $addressObj->getCountry().'<br/>';
            $address .= $addressObj->getPhone();

            $order = new Order();
            $order->setUser($this->getUser());
            $order->setCreatedAt(new \DateTime());
            $order->setState(1);
            $order->setCarrierName($form->get('carriers')->getData()->getName());
            $order->setCarrierPrice($form->get('carriers')->getData()->getPrice());
            $order->setDelivery($address);

            foreach ($products as $product) {
                $orderDetail = new OrderDetail();
                $orderDetail->setProductName($product['object']->getName());
                $orderDetail->setProductIllustration($product['object']->getIllustration());
                $orderDetail->setProductPrice($product['object']->getPrice());
                $orderDetail->setProductTva($product['object']->getTva());
                $orderDetail->setProductQuantity($product['qty']);
                $order->addOrderDetail($orderDetail);
            }

            $entityManager->persist($order);
            $entityManager->flush();

        }



        return $this->render('order/summary.html.twig', [
            'choices' => $form->getData(),
            'cart' => $products,
            'order' => $order,
            'totalWt' => $cart->getTotalWt()
        ]);
    }

}


