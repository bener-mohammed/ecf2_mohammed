<?php

namespace App\Form;

use App\Entity\Trainee;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class TraineeType extends AbstractType
{
    public function buildForm(
        FormBuilderInterface $builder,
        array $options
    ): void {
        $builder
            ->add('afpaId', null, [
                'label' => 'Identifiant AFPA',
            ])

            ->add('firstName', null, [
                'label' => 'Prénom',
            ])

            ->add('lastName', null, [
                'label' => 'Nom',
            ])

            ->add('email', null, [
                'label' => 'Email',
            ])

            ->add('phone', null, [
                'label' => 'Téléphone',
            ])

            ->add('residence', null, [
                'label' => 'Lieu de résidence',
                'required' => false,
            ])

            ->add('birthDate', DateType::class, [
                'label' => 'Date de naissance',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])

            ->add('photoFile', FileType::class, [
                'label' => 'Photo d\'identité',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'accept' => 'image/jpeg,image/png,image/webp',
                ],
                'constraints' => [
                    new Image([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        'mimeTypesMessage' =>
                            'Veuillez sélectionner une image JPG, PNG ou WEBP.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(
        OptionsResolver $resolver
    ): void {
        $resolver->setDefaults([
            'data_class' => Trainee::class,
        ]);
    }
}