<?php

namespace App\Controller\Account;

use App\Classe\Cart;
use App\Entity\Address;
use App\Form\AddressUserType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class AddressController extends AbstractController
{
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    #[Route('/compte/adresses', name: 'app_account_addresses')]
    public function index(): Response
    {
        return $this->render('account/address/index.html.twig');
    }


    // Route pour supprimé l'adresse je mets l'id en argument de l'adresse que j'ai à supprimé et je l'ajoute à l'intérieur de ma fonction delete
    #[Route('/compte/adresses/delete/{id}', name: 'app_account_address_delete')]
    public function delete($id, AddressRepository $addressRepository): Response
    {
        $address = $addressRepository->findOneById($id);
        if (!$address OR $address->getUser() != $this->getUser()) {
            return $this->redirectToRoute('app_account_addresses');
        }
        $this->addFlash(
            'success',
            "Votre adresse est correctement supprimée."
        );
        // J'ai besoin de comunique avec ma basse de donnée, j'ai besoin du entityManager que j'ai déclaré dans le constructeur donc je peux y accéder partout dans mon controlleur j'utilise remove pour supprimé (quel objet? $address) et puis je sauvegarde en base de donnée
        $this->entityManager->remove($address);
        $this->entityManager->flush();

        // Je redirige vers app_account_adresse
        return $this->redirectToRoute('app_account_addresses');
    }


    // (cette route chercher crée une adresse et modifier une adresse) je rajoute à ma route compte/adresse/ajouter/ un argument id pour modifier l'adresse qu'on souhaite modifier (cet id, il est optionnel parce que si je ne mets pas l'ID, c'est que je souhaite créer une adresse mais si je mets un id et qu'il correspond à un objet adresse dans ma base de données,ça veux dire que je cherche à le modifier) je récupérer cet id et lui met un null par défaut dans mon tableau de route, dans ma annotation l'intitulé défaut qui va nous permettre d'indiquer par défaut quel est le comportement d'un argument qu'on souhaite transporter dans l'URL.
    #[Route('/compte/adresse/ajouter/{id}', name: 'app_account_address_form', defaults: ['id' => null])]
    public function form(Request $request, $id, AddressRepository $addressRepository, Cart $cart): Response
    {
        // si id (si on transmet un id l'adresse existe déjà) existe je vais aller rechercher l'objet adresse sinon je suis dans la création d'un nouvel objet adresse donc tu peux me crée ce nouveau objet adresse et set lui le user en cour
        if ($id){
            $address = $addressRepository->findOneById($id);
            //Sécurite je limite le risque et je rajoute de la securité dans la modification de ce formulaire (est-ce que l'adresse appartient a l'utilisateur en cour et est-ce que l'adresse est bien existante) si l'adresse n'existe pas ou que l'utilisateur de cette adresse est différent de l'utilisateur en cours. Ensuite je fais un returne this redirect to root et de rediriger l'utilisateur vers la liste de ses adresses à lui. 
            if (!$address OR $address->getUser() != $this->getUser()) {
                return $this->redirectToRoute('app_account_addresses');
            }
        } else {
            $address = new Address();
            $address->setUser($this->getUser());
        }

        
        $form = $this->createForm(AddressUserType::class, $address);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($address);
            $this->entityManager->flush();

            $this->addFlash(
                'success',
                "Votre adresse est correctement sauvegardée."
            );

            if ($cart->fullQuantity() > 0){
                return $this->redirectToRoute("app_order");
            }

            return $this->redirectToRoute("app_account_addresses");
        }

        return $this->render('account/address/form.html.twig', [
            'addressForm' => $form
        ]);
    }

}

?>