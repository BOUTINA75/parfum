<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;

class RegisterUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    // permet de construire le formulaire (dans cette fonction j'ajoute plusieurs élément)
    {
        $builder
            //(Les typage, mais uniquement pour les formulaire de symfony) type de forme que je souhaite affiché, EmailType c'est un champ, champ de texte qui est rendu à l'aide de la <input type="email">balise HTML5.(regarde dans la doc)
            ->add('email', EmailType::class, [
                'label' => "Votre adresse email",
                // attr (renseigné les attribues que je veux afficher dans mon input)
                'attr' => [
                    // placeholder (un texte indicatif)
                    'placeholder' => "Indiquez votre adresse email"
                ],
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'max' => 255
                    ]),
                    new Email(message: "Le format de l'email est invalide.")
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                // Je rajoute une contrainte et je lui donne un tableau ensuite je fait un new de l'objet de la contrainte que je veux choisir j'ai envie de faire une contrainte sur la longeur de chaine de caractére (voir la doc) je rajoute deux paramètre 
                'constraints' => [
                    new Length([
                        'min' => 12,
                        'max' => 30
                    ])
                ],
                'first_options'  => [
                    'label' => 'Votre mot de passe',
                    'attr' => [
                    'placeholder' => "Choisissez votre mot de passe"
                    ],
                    // cela me permet de transite le MDP saissie dans le formulaire jusqu'au controller de maniere cripte et encodé
                    'hash_property_path' => 'password'
            ],
                'second_options' => [
                    'label' => 'Comfirmez votre mot de passe',
                    'attr' => [
                    'placeholder' => "Confirmez votre mot de passe"
                ]
                ],
                // J'ai ajouté un nouvelle input (plainPassword) mais qui correspond a rien dans mon entity pck jusqu'à present dans mon entity j'avais un champs qui s'appellais password donc pour forcé symfony a ne pas allé cherche un champ qui correspond a un entity on utilise (mappes => false)c'est pour dire n'essaye pas de faire le lien entre l'entity et le champ que je te donne
                'mapped' => false,
            ])
            ->add('firstname', TextType::class, [
                'label' => "Votre prenom",
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'max' => 30
                    ])
                ],
                'attr' => [
                    'placeholder' => "Indiquez votre prenom"
                ]
            ])
            ->add('lastname', TextType::class, [
                'label' => "Votre nom",
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'max' => 30
                    ])
                ],
                'attr' => [
                    'placeholder' => "Indiquez votre nom"
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Valider",
                'attr' => [
                    // attribue class de bootstrap
                    'class' => 'btn btn-custom w-100'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Cette contrainte vérifie une entré dans mon formulaire si elle est bien unique, dans mon tableau je specifie entity ensuite je notifie sur quel champ je veux que sa soie unique.
            'constraints' => [
                new UniqueEntity([
                    'entityClass' => User::class,
                    'fields' => 'email'
                ])
                ],
            'data_class' => User::class,
        ]);
    }
}
