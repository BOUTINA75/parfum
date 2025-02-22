<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Catégorie')
            ->setEntityLabelInPlural('Catégories')
        ;
    }

    
    public function configureFields(string $pageName): iterable
    {
        // (slug est une chain de caractère purifiés, qu'on va pouvoir faire transiter dans nos URL de manière bcp plus propre "SulgField" qui fait exactement ce travail là) setTargetFieldName il a besoin qu'on lui indique quelle est la propriété sur laquelle (j'aimerais ajoute un petit texte en dessous de mes input ce qu'on appelle un helper) 
        return [
            TextField::new('name')->setLabel('Title')->setHelp("Titre de la catégorie"),
            SlugField::new('slug')->setLabel('URL')->setTargetFieldName('name')->setHelp('URL de votre catégorie générée automatiquement')
        ];


        // exemple
        // Les Parfums Français -> nom
        // les-parfums-francais -> slug 
    }
    
}
