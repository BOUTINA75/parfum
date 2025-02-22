<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class PasswordUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('actualpassword', PasswordType::class, [
                'label' => "Votre mot de passe actuel",
                'attr' => [
                    'placeholder' => "Indiquez votre mot de passe actuel"
                ],
                'mapped' => false
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints' => [
                    new Length([
                        'min' => 4,
                        'max' => 30
                    ])
                ],
                'first_options'  => [
                    'label' => 'Votre nouveau mot de passe',
                    'attr' => [
                        'placeholder' => "Choisissez votre nouveau mot de passe"
                    ],
                    'hash_property_path' => 'password'
            ],
                'second_options' => [
                    'label' => 'Comfirmez votre nouveau mot de passe',
                    'attr' => [
                        'placeholder' => "Confirmez votre nouveau mot de passe"
                ]
                ],
                'mapped' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Mettre à jour mon mot de passe",
                'attr' => [
                    'class' => 'btn btn-custom w-100'
                ]
            ])
            // La j'ai utiliser des events eventlistener (qui veux dire des moments au quel on veux intervenir avant que sa soie le contreller qui le face pour nous) on viens interface entre le formulaire et le controller a un moment présit (pour cela j'ai choisi mes moment en parcourant mes différent evenement du formulaire disponible) j'ai voulu m'interface au moment du submit (je suis aller cherche mon formulaire ensuite mon user actuel, vérifie l'en codage d'un MDP)
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event){
                $form =$event->getForm();
                $user = $form->getConfig()->getOptions()['data'];
                $passwordHasher = $form->getConfig()->getOptions()['passwordHasher'];

                // 1. Récupérer le mot de passe saisi par l'utilisateur et le comparer au MDP en base de donnée (dans entité)
                $isValid = $passwordHasher->isPasswordValid(
                    $user,
                    $form->get('actualpassword')->getData()
                );

                // 2. Récupérer le mot de passe actuel en BDD 
                // $actualPwdDtabase = $user->getPassword();

                
                // 3. Si c'est valide on va plus loin sinon tu envoie une erreur 
                if(!$isValid) {
                    $form->get('actualpassword')->addError(new FormError("Votre mot de passe actuel n'est pas conforme. Veuillez vérifier votre saisie"));
                }
                
                
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'passwordHasher' => null
        ]);
    }
}
