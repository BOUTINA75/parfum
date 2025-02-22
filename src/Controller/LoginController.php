<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/connexion', name: 'app_login')]
    // j'utilise une dependance que symfony c'est gére en total autonomie (AuthentictionUtils tjr la stock dans une variable) parfait pour faire fonctionné ma function, je recupére la data saisie dans le formulaire et on vérifie si tout est correcte ou erroné (propose par la doc)
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // gerer les erreurs
        $error = $authenticationUtils->getLastAuthenticationError();


        // dernier username (email) entré par l'utisateur
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('login/index.html.twig', [
            // je passe mes deux variable a mon fichier twig 
            'error' => $error,
            'last_username' => $lastUsername,
        ]);
    }

    // La je créer une route pour que mon utilisateur puisse ce déconnecté, c'est specifié la méthode, ici je dis a symfony c'étais route peux accepte qu'une méthode en GET donc tu peux pas soumettre de formulaire a cette route(voir la doc)
    #[Route('/deconnexion', name: 'app_logout', methods: ['GET'])]
    public function logout(): never
    {
        // 
        throw new \Exception('D\'t forget to activate logout in serity.yaml');
    }
    
}
