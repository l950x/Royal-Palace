<?php

namespace App\Form;

use App\Entity\Chambre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChambreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Superficie')
            ->add('ChaineALaCarte')
            ->add('Tarif')
            ->add('VueSurMer')
            ->add('Climatisation')
            ->add('TelevisionAEcranPlat')
            ->add('Telephone')
            ->add('ChaineSatellite')
            ->add('ChaineDuCable')
            ->add('CoffreFort')
            ->add('MaterielDeRepassage')
            ->add('WifiGratuit')
            ->add('Type1')
            ->add('Type2')
            ->add('Type3')
            ->add('Hotel')
            ->add('hotel')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Chambre::class,
        ]);
    }
}
