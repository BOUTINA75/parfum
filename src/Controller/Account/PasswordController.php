<?php

namespace App\Controller\Account;

use App\Form\PasswordUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;




class PasswordController extends AbstractController
{
    
    private $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // le nom de ma route app_account_motify_pwd, j'ai créer une deuxiéme route
    #[Route('/compte/modifier-mot-de-passe', name: 'app_account_motify_pwd')]
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {

        $user = $this->getUser();
        
        // Dans cette deuxiéme route j'ai créer un formulaire PasswordUserType avec mes trois champs le MDP actuel, le nouveau MDP et la confirmation du nouveau MDP. je suis venue créer mon formulaire passer mon user, une option avec mon passwordHasher qui est lui meme lié a UserPasswordHasherInterface cette dependence qui nous permet d'encoder ou de vérifié des MDP
        $form =  $this->createForm(PasswordUserType::class, $user, [
            'passwordHasher' => $passwordHasher
        ]);

        $form->handleRequest($request);
        
        // Pas besoin de persist des donné elle exixte déjà juste enregistré (flush) La on demande est-ce que le formulaire est soumie,est-ce que il est valide donc sans erreur si oui tu sauvegarde en base de donné 
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            // $user = $form->getData();
            // si mon formulaire et soumie et qu'il est valide je veux pouser une notification qui dirais a mon utilisateur  votre compte a bien étais mie a jour(deux paramétre le premier c'est le typage de notification(doc bootstrap) et le deuxième c'est le message que je veux envoyer)
            $this->addFlash(
                'success',
                "Votre mot de passe est correctement mis à jour."
            );
        }
        
        // La vue associé
        return $this->render('account/password/index.html.twig', [
            'modifyPwd' => $form->createView()
        ]);
    }

}

?>