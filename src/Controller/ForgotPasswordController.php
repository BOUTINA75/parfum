<?php

namespace App\Controller;

use DateTime;
use App\Classe\Mail;
use App\Repository\UserRepository;
use App\Form\ResetPasswordFormType;
use App\Form\ForgotPasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ForgotPasswordController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/mot-de-passe-oublie', name: 'app_password')]
    public function index(Request $request, UserRepository $userRepository): Response
    {
        // 1. Formulaire l'utilisateur renseigne sa boite mail
        $form = $this->createForm(ForgotPasswordFormType::class);
        
        $form->handleRequest($request);

        // 2. Traitement du formulaire (on check si la boite email existe)
        if ($form->isSubmitted() && $form->isValid()) {
             // 3. Si l'email renseigné par l'utilisateur est en base de donnée
            $email = $form->get('email')->getData();
            
            $user = $userRepository->findOneByEmail($email);
            
            // 4.Envoyer une notification à l'utinisateur
            $this->addFlash('success', "Si votre adresse email existe, vous recevrez un mail pour réinitialiser votre mot de passe.");

            // 5. Si user existe , on reset le password et on envoie par email le nouveau mot de passe (on lui envoie un email contenant un lien, un bouton sur le quel va pouvoir cliquer, lien dynamiue qui va permettre de l'envoyer sur une nouvelle page)
            if ($user) {
                //  5. a-Créer un token qu'on va stocker en BDD
                $token = bin2hex(random_bytes(15));
                $user->setToken($token);

                $date = new DateTime();
                $date->modify('+10 minute');

                $user->setTokenExpireAt($date);

                $this->em->flush();

                $mail = new Mail();
                $vars = [
                    'link' =>  $this->generateUrl('app_password_update', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL),
                ];
                $mail->send( $user->getEmail(), $user->getFirstname().' '.$user->getLastname(), "Modification de votre mot de passe", "forgotpassword.html", $vars);
            }
        }

        return $this->render('password/index.html.twig', [
            'forgotPassworForm' => $form->createView(),
        ]);
    }

    // cette nouvelle page mot de passe reset slashle token et ensuite on va faire un certain nombre de traitements pour modifier son mot de passe.(token = jeton, chaine de caractères qui a une expiration définie, ça laisse un certain temps à l'utilisateur pour modifier son mot de passe)
    #[Route('/mot-de-passe/reset/{token}', name: 'app_password_update')]
    public function update(Request $request, UserRepository $userRepository, $token): Response
    {
        // si token existe pas alors tu redirige vers ulr app_password pour qu'il recommence la procédure
        if(!$token) {
            return $this->redirectToRoute('app_password');
        }

        // si le token est transporté, on va cherche l'utilisateur
        $user = $userRepository->findOneByToken($token);

        $now = new DateTime();
        if(!$user || $now > $user->getTokenExpireAt()) {
            return $this->redirectToRoute('app_password');
        }

        $form = $this->createForm(ResetPasswordFormType::class, $user);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setToken(null);
            $user->setTokenExpireAt(null);
            $this->em->flush();
            $this->addFlash(
                'success',
                "Votre mot de passe est correctement mis à jour."
            );
            return $this->redirectToRoute('app_login');
        }
        return $this->render('password/reset.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
