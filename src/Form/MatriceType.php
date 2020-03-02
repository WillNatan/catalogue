<?php

namespace App\Form;

use App\Entity\Domaines;
use App\Entity\Matrice;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MatriceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('domaine', EntityType::class, ['class'=>Domaines::class,
                'choice_label'=>'nomDossier',
                'attr'=>['class'=>'form-control']
            ])
            ->add('liens', CollectionType::class, ['label'=>false,
                'entry_type' => LiensType::class,
                'entry_options' => ['label' => null],
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true

            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Matrice::class,
        ]);
    }
}
