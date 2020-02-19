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

class EditPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('oldPassword', PasswordType::class, ['attr'=>['class'=>'form-control'],'mapped'=>false])
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
            ->add('reponsesecrete', TextType::class, ['mapped'=>false, 'attr'=>['class'=>'form-control']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
