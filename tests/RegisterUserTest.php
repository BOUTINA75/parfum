<?php
// On test le comportement d'une application, qui permettent de simuler des connections utilisateurs et tester l'intégralité de l'application.  (application tests = test fonctionnelle)

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

// WebTestCase qui me permetde venir teste des comportement web, en l'occurrence ici remplir un formulaiire et voir si j'ai un morceau de code de disponibble
class RegisterUserTest extends WebTestCase
{
    public function testSomething(): void
    {
        /*
        *1. Créer un faux client (comportement = navigateur) de pointer vers une URL
        *2. Remplie les champs de mon formulaire d'inscription 
        *3. Est-ce que tu peux regarder si dans ma page j'ai le message (alerte) suivante : Votre compte est correctement créé, veuillez vous connecter.
        *4.
        */
        // nous permet d'émuler, de créer un faux navigateur (de faire semble que le comportement et un navigateur)
        $client = static::createClient();
        // Jai besoin que mon client aille vers une URL (requête) en paticulier premier paramétre la méthode (get comme si on était un navigateur) en deuxiéme paramètre la route
        $client->request('GET', '/inscription');

        // 2. (firstname, lastname, email, password, comfirmation du password), on a besoin des différents inputs pour que mon faux client sache exactement dans quoi il doit rentrer des informations je demende a mon client de soumettre un formulaire(submitForm)
        $client->submitForm('Valider', [
            'register_user[email]' => 'tina@email.fr',
            'register_user[plainPassword][first]' => '123456',
            'register_user[plainPassword][second]' => '123456',
            'register_user[firstname]' => 'Tina',
            'register_user[lastname]' => 'Doe'
        ]);

        // pour pousser notre client à suivre les redirection on dois aussi test la redirection (on utilise un nouvel assert qui s'appelle assertResponseRedirect) et on va lui donner la route vers laquelle le post notre formulaire doit rediriger (la route a tester)
        $this->assertResponseRedirects('/connexion');
        // (Follow) on demande a notre client Est-ce que tu peux suivre la redirection que va te proposer le controlleur (méthode: followRedirect)
        $client->followRedirect();

        // 3.
        // Est-ce que tu peux regarder si dans ma page j'ai un message, une alerte qui contient "Votre conpte est correctement créée. globale tous les test commence par assert et c'est cette méthode qui va nous permet de test si quelque chose contient quelque chose? ('div:contains voir la doc cette méthode permet d'aller chercher un élément dans mon dom html dans ma page et on lui donne l'élément qu'on veut aller chercher)
        $this->assertSelectorExists('div:contains("Votre compte est correctement créé, veuillez vous connecter.")');
    }
}
