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

class adminUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, ['attr'=>['class'=>'form-control']])
            ->add('roles', ChoiceType::class, ['attr'=>['class'=>'form-control'], 'choices'=>
                    [
                        'Administrateur'=>'ROLE_ADMIN',
                        'SuperAdmin'=>'ROLE_SUPER_ADMIN'
                    ],
                    'multiple'=>true
                ]
            )
            ->add('username', TextType::class, ['label'=>"Nom d'utilisateur",'attr'=>['class'=>'form-control']])
            ->add('plainPassword', RepeatedType::class,
                [
                    'type'=>PasswordType::class,
                    'options'=>[

                        'attr'=>
                            [
                                'class'=>'form-control'
                            ]
                    ],
                    'first_options'=>
                        [
                            'label'=>'Mot de passe'
                        ],
                    'second_options'=>
                        [
                            'label'=>'Retaper mot de passe'
                        ],
                    'invalid_message' => 'Les mots de passe de correspondent pas'
                ]
            )

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
