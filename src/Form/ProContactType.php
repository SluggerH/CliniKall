<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\DBAL\Types\TextType as TypesTextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('email',TextType::class,[
            'label'=>'Email'
             ])
        ->add('firstname',TextType::class,[
                'label'=>'Prénom'
                 ])
        ->add('lastname',TextType::class,[
                    'label'=>'Nom'
                     ])
        ->add('adress',TextType::class,[
                        'label'=>'Adresse'
                         ])
        ->add('city',TextType::class,[
                            'label'=>'Ville'
                             ])
        ->add('descriptionPatient',ChoiceType::class,[
                                'label'=>'Précisez votre spécialité',
                                'choices'  => [
                                    'dentiste' => 'dentiste',
                                    'kiné' => 'kiné',
                                    'médecin' => 'médecin',
                                ],
                            ])
        ->add('codePostal')
        ->add('phone')
        ->add('agreeTerms', CheckboxType::class, [
            'mapped' => false,
            'constraints' => [
                new IsTrue([
                    'message' => "Vous devez être d'accord.",
                ]),
            ],
        ])
        ->add('plainPassword', PasswordType::class, [
            // instead of being set onto the object directly,
            // this is read and encoded in the controller
            'label'=>'Mot de passe',
            'mapped' => false,
            'constraints' => [
                new NotBlank([
                    'message' => 'Entrez votre mot de passe',
                ]),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Votre mot de passe doit comporter {{ limit }} caractères',
                    // max length allowed by Symfony for security reasons
                    'max' => 4096,
                ]),
                ],
        ])    
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
