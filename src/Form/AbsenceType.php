<?php

namespace App\Form;

use App\Entity\Absence;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class AbsenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('absenceDate', DateType::class, [
                'label' => 'Date de l\'absence',
                'widget' => 'single_text',
            ])

            ->add('reason', ChoiceType::class, [
                'label' => 'Motif',
                'placeholder' => 'Choisir un motif',
                'choices' => [
                    'Maladie' => 'illness',
                    'Sans motif' => 'unexcused',
                    'Absence légale' => 'legal_leave',
                    'Accident du travail' => 'work_accident',
                ],
            ])

            ->add('proofFile', FileType::class, [
                'label' => 'Justificatif PDF',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'application/pdf',
                        ],
                        'mimeTypesMessage' => 'Veuillez sélectionner un fichier PDF.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Absence::class,
        ]);
    }
}