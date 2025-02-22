<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    // configureCrud qui prend une injection de dépendance, c'est la classe crud et à interieur vous avez vu, on peut setter différents paramètres qui vont nous qui vont me permettre de gére les options si je puis dire. La personnalisation de l'affichage de notre entité users
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs')
        ;
    }

    // configureFields permet de gérer les différents champs qui sont configurables et visible pour nos différents administrateurs
    public function configureFields(string $pageName): iterable
    {
        // Qu'est-ce que nos administrateurs vont potentiellement avoir besoin et surtout le droit de modifier lorsqu'ils vont devoir gérer les utilisateurs (field = champs du nom de sa fonction vient pour typer les input easyAdmin) je passe les méthode avec set label (Pour que mon email s'affiche uniquement et que l'administrateur ne puisse pas modifier je rajoute une nouvelle méthode qui s'appelle onlyOnIndex)
        return [
            TextField::new('firstname')->setLabel('Prénom'),
            TextField::new('lastname')->setLabel('Nom'),
            TextField::new('email')->setLabel('Email')->onlyOnIndex(),
        ];
    }
    
}
