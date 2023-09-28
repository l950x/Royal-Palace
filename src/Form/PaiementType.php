<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\CardScheme;

class PaiementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('field_name', TextType::class, [
                'label' => 'Nom sur la carte',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer le nom sur la carte']),
                    new Length(['min' => 3, 'max' => 255]),
                ],
            ])
            ->add('numbers', TextType::class, [
                'label' => 'Numéro de carte',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer le numéro de carte']),
                    // new CardScheme(['schemes' => ['VISA', 'MASTERCARD'], 'message' => 'Le numéro de carte n\'est pas valide']),
                ],
            ])
            ->add('validite', DateType::class, [
                'label' => 'Date de validité',
                'widget' => 'single_text',
                'attr' => ['placeholder' => 'MM/YYYY'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer la date de validité']),
                ],
            ])
            ->add('ccv', TextType::class, [
                'label' => 'Code de sécurité (CCV)',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer le code de sécurité (CCV)']),
                    new Length(['min' => 3, 'max' => 4]),
                ],
            ])
            // ->add('dateEntree', DateType::class, [
            //     'label' => 'Date d\'entrée',
            //     'widget' => 'single_text',
            // ])
            // ->add('dateSortie', DateType::class, [
            //     'label' => 'Date de sortie',
            //     'widget' => 'single_text',
            // ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider le paiement',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Vous pouvez configurer des options supplémentaires du formulaire ici
        ]);
    }
}
