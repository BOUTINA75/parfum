<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    // injection de dépendance request
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Je créer un nouveau user (variable) qui est un nouvelle objet user qui conrrespond à mon entité
        $user = new User();
        // cette variable la(user), je la passé en deuxiéme paramètre dans ma méthode createForm (je me suis servie pour créer un formulaire)
        $form = $this->createForm(RegisterUserType::class,$user);
        
        // écoute la request (ce que l'utilisateur soumet dans le formulaire)
        $form->handleRequest($request);
 
        // verifie qu'il est soumie et valide (isSubmitted et isValid)
        if($form->isSubmitted() && $form->isValid()) {
            
            // Jai besoin de mon orm ($entityManager), Insert moi cet utilisateur dans ma table(persist: pour figer les donné, flush: pour les enregistrés) persist a besoin d'un objet qu'est en lien avec l'entity
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'success',
                "Votre compte est correctement créé, veuillez vous connecter."
            );
            // Envoie d'un email de confirmation d'inscription.
            $mail = new Mail();
            $vars = [
                'firstname' => $user->getFirstname(),
            ];
            $mail->send( $user->getEmail(), $user->getFirstname().' '.$user->getLastname(), 'Bienvenue sur florenza Création Artisanale', "welcome.html", $vars);
            
            // Je veux rediriger mon utilisateur vers une route aprés inscription (app_login)
            return $this->redirectToRoute('app_login');
        }
        
        // Pour passe mon formulaire a mon fichier twig je créer une variable et à l'interieure de cette variale je lui pousse du contenue, j'utilise une méthode createView propre a symfony (registerForm c'est le nom de la variable que je vais envoyé au fichier twig)
        return $this->render('register/index.html.twig',[
            'registerForm'=> $form->createView()
        ]);
    }
}

// USE
// je l'appelle
// NAMESPACE
// Je définie un répertoire
