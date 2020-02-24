<?php

namespace App\Form;

use App\Entity\Dossier;
use App\Entity\SousDossier;
use App\Entity\ReportCatalog;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
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

class ReportCatalogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('mainFolder', EntityType::class, [
            'class' => Dossier::class,
            'placeholder'=>'Sélectionnez un domaine',
            'choice_label' => 'nomDossier',
            'label_attr'=>['class'=>'small'],
            'label'=>'Dossier',
            'attr'=>['class'=>'form-control mainFolder']
        ])
        
            ->add('Nom_Rapport', TextType::class, ['required'=>false,'label'=>'Nom du rapport','label_attr'=>['class'=>'small'],'attr'=>['class'=>'form-control']])
            ->add('statut', ChoiceType::class, ['choices'=>
        [
            'Actif'=>true,
            'Décommissionné'=>false
        ],
                'expanded'=>true,
                'choice_attr'=>[
                    'Actif'=>['class'=>'form-check-input'],
                    'Décommissionné'=>['class'=>'form-check-input']
                ]
            ])
            ->add('VersionActuelle', TextType::class, ['required'=>false, 'label'=>'Version actuelle','label_attr'=>['class'=>'small'],'attr'=>['class'=>'form-control']])
            ->add('Commentaire', TextareaType::class, ['required'=>false,'label'=>'Commentaire','label_attr'=>['class'=>'small'],'attr'=>['class'=>'form-control','rows'=>8]])
            ->add('Categorie', TextType::class, ['required'=>false,'label'=>'Catégorie','label_attr'=>['class'=>'small'],'attr'=>['class'=>'form-control']])
            ->add('Objectifs', TextareaType::class, ['required'=>false,'label'=>'Objectifs','label_attr'=>['class'=>'small'],'attr'=>['class'=>'form-control','rows'=>8]])
            ->add('Details', TextareaType::class, ['required'=>false,'label'=>'Détails','label_attr'=>['class'=>'small'],'attr'=>['class'=>'form-control','rows'=>8]])
            ->add('Sources', TextareaType::class, ['required'=>false,'label'=>'Sources','label_attr'=>['class'=>'small'],'attr'=>['class'=>'form-control','rows'=>8]])
            ->add('Parametres', TextareaType::class, ['required'=>false,'label'=>'Paramètres','label_attr'=>['class'=>'small'],'attr'=>['class'=>'form-control','rows'=>8]])
            ->add('Historique_Versions', TextareaType::class, ['required'=>false,'label'=>'Historique des versions','label_attr'=>['class'=>'small'],'attr'=>['class'=>'form-control','rows'=>8]])
        ; 

        /*
         * $builder->get('dossier')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event){
                $form = $event->getForm();
                $form->add('Sous_Dossier', EntityType::class, [
                    'class' => SousDossier::class,
                    'placeholder' => 'Sélectionnez un sous dossier (si nécessaire)',
                    'attr'=>['class'=>'form-control mainFolder'],
                    'label_attr'=>['class'=>'small'],
                    'choices' => $form->getData()->getSubFolders(),
                    'choice_label'=>'nomDossier'
                ]);
            }
        );


        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event){
                $form = $event->getForm();
                $data = $event->getData();
                $subFolders = $data->getDossier();

                $form->getParent()->add('')
            }
        );
         */
         $formModifier = function (FormInterface $form, Dossier $folder = null) {
            $subfolders = null === $folder ? [] : $folder->getSubFolders();
            $form->add('subFolder', EntityType::class, ['required'=>false,
                'class' => SousDossier::class,
                'placeholder' => 'Sous-domaine (si existant)',
                'attr'=>['class'=>'form-control mainFolder'],
                'label_attr'=>['class'=>'small'],
                'choices' => $subfolders,
                'label'=>'Sous-Dossier',
                'choice_label'=>'nomDossier'
            ]);
        };
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                $data = $event->getData();
                $form = $event->getForm();
                $formModifier($form, $data->getMainFolder());
            }
        );

        $builder->get('mainFolder')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $sport = $event->getForm()->getData();

                $formModifier($event->getForm()->getParent(), $sport);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ReportCatalog::class,
        ]);
    }
}
