<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class DatesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateEntree', DateType::class, [
                'widget' => 'choice',
                'input' => 'datetime_immutable',
                'format' => 'yyyy-MM-dd',
                'required' => true,
            ])

            ->add('dateSortie', DateType::class, [
                'widget' => 'choice',
                'input' => 'datetime_immutable',
                'format' => 'yyyy-MM-dd',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
