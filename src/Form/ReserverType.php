<?php

namespace App\Form;

use App\Entity\Reserver;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReserverType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Arrivee')
            ->add('Depart')
            ->add('prix')
            ->add('validite')
            ->add('nbPersonne')
            // ->add('user')
            // ->add('chambre')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reserver::class,
        ]);
    }
}
