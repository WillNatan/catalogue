<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class firstLoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, ['attr'=>['class'=>'form-control', 'placeholder'=>'Adresse Email',]])
            ->add('plainPassword', RepeatedType::class,
                [
                    'type'=>PasswordType::class,
                    'first_options'=>
                    [
                        'attr'=>['class'=>'form-control', 'placeholder'=>'Mot de passe']
                    ],
                    'second_options'=>
                    [
                        'attr'=>['class'=>'form-control', 'placeholder'=>'Retaper mot de passe']
                    ]
                ]
            )
            ->add('username', TextType::class, ['attr'=>['class'=>'form-control', 'placeholder'=>"Nom d'utilisateur"]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
