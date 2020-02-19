<?php

namespace App\Form;

use App\Entity\Dossier;
use App\Entity\ReferentielObjets;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ObjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', TextareaType::class, ['required'=>false,'attr'=>['class'=>'form-control']])
            ->add('type', TextType::class, ['required'=>false,'attr'=>['class'=>'form-control']])
            ->add('qualification', ChoiceType::class, [
                'choices'=>[
                    'indicateur'=>'indicateur',
                    "axe d'analyse"=>"axe d'analyse"
                ],
                'required'=>false,'attr'=>['class'=>'form-control']])
            ->add('denomination', TextType::class, ['required'=>false,'attr'=>['class'=>'form-control']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ReferentielObjets::class,
        ]);
    }
}
