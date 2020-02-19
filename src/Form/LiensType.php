<?php

namespace App\Form;

use App\Entity\Dossier;

use App\Entity\Liens;
use App\Entity\ReferentielObjets;
use App\Repository\ReferentielObjetsRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class LiensType extends AbstractType
{
    private $indicateurRepo;

    public function __construct(ReferentielObjetsRepository $indicateurRepo)
    {
        $this->indicateurRepo = $indicateurRepo;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Indicateur', EntityType::class, ['class'=>ReferentielObjets::class,
                'choices'=>$this->indicateurRepo->findByIndicateurs(),
                'choice_label'=>'nomObjet',
                'attr'=>['class'=>'form-control']
            ])
            ->add('Axes', EntityType::class,
                [
                    'class'=>ReferentielObjets::class,
                    'choices'=>$this->indicateurRepo->findByAxes(),
                    'choice_label'=>'nomObjet',
                    'multiple'=>true,
                    'attr'=>['class'=>'form-control','style'=>'height:100%','size'=>10]
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Liens::class,
        ]);
    }
}
