<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Product')
            ->setEntityLabelInPlural('Products')
        ;
    }
    public function configureFields(string $pageName): iterable
    {
        // En faisant cela, je dis à ImageField tu n'es pas requis si tu es dans le cadre de la page name edit mais par défaut tu es requit (Sa repond a ma problématique dans la création d'un produit mon image est bien requise et dans la modification d'un produit, mon image n'est plus requise)
        +
        $required = true;
        if($pageName == 'edit') {
            $required = false;
        }
        return [
            // TextEditorField par défaut easyAdmin m'embarque (wysiwig) un éditeur qui va nous permet de la mise en forme dans notre texte.
            // Dans mon ImageField je dois indique le répertoire d'upload, (c'est à dire en fat le répertoire dans lequel on va mettre les image chargées par les utilisateur) La méthode setUploadDir à un paramètre demandé,c'est le chemin du répertoire.
            // setBasePath on parle d'affichage donc par défaut ce base juste aprés mon serveur il va chercher uploads nom de l'image (on est dans le dossier plublic)
            // setUploadedFileNamePattern cette méthode, elle va nous permettre de définir un pattern, un schéma de naming de nommage pour nos fichier que nous allons télécharger  (sa va permet de choisir un nom ou code a nos images)
            TextField::new('name')->setLabel('Nom')->setHelp('Nom de votre produit'),
            SlugField::new('slug')->setTargetFieldName('name')->setLabel('URL')->setHelp('URL de votre catégorie générée automatique'),
            TextEditorField::new('description')->setHelp('Description de votre prosuit.'),
            ImageField::new('illustration')
            ->setLabel('Image')
            ->setHelp('Image du produit en 600x600px.')
            ->setUploadedFileNamePattern('[year]-[month]-[day]-[contenthash]. [extension]')
            ->setBasePath('/uploads')
            ->setUploadDir('/public/uploads')
            ->setRequired($required)
            ,
            NumberField::new('price')->setLabel('Prix H.T')->setHelp('Prix H.T du produit sans le sigle €'),
            ChoiceField::new('tva')->setLabel('Taux de TVA')->setchoices([
                '5,5%' => '5,5',
                '10%' => '10',
                '20%' => '20'
            ]),
            // AssociationFieldqui permet d'associer une propriété à une entité il est possible de mettre le label en deuxième paramètre de notre fonction new, Je crée une nouvelle petit fonction dans mon fichier category.php qui s'appelle(__toString) car pour le moment il sais pas quelle chaine de caractères il dois afficher
            AssociationField::new('category', 'Catégorie associée')
        
        ];
    }
}
