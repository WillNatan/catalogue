<?php

namespace App\Form;

use App\Entity\Domaines;
use App\Entity\SousDossier;
use App\Entity\Reports;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Doctrine\Common\Collections\ArrayCollection;

class SQLTextType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sqltext', TextareaType::class, ['required' => false, 'label' => 'Commentaire','label_attr' => ['class' => 'small'], 'attr' => ['class' => 'form-control', 'placeholder'=>'Ajoutez vos requêtes ici', 'rows' => 40]]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reports::class,
        ]);
    }
}
