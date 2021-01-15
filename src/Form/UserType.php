<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\DBAL\Types\TextType as TypesTextType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
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
        ->add('codePostal')
        ->add('phone')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
