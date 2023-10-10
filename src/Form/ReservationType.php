<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('arrivee', DateType::class, [
            'widget' => 'single_text',
            'html5' => true, // Activer l'option HTML5
            'input' => 'datetime_immutable',
            'format' => 'yyyy-MM-dd',
            'data' => new \DateTimeImmutable(),
            'required' => true,
        ])
    
        ->add('depart', DateType::class, [
            'widget' => 'single_text',
            'html5' => true, // Activer l'option HTML5
            'input' => 'datetime_immutable',
            'format' => 'yyyy-MM-dd',
            'data' => new \DateTimeImmutable('next day'),
            'required' => true,
        ])
        ->add('nbPersonne', ChoiceType::class, [
            'choices' => [
                '1 personne' => 1,
                '2 personnes' => 2,
                '3 personnes' => 3,
                '4 personnes' => 4,
            ],
            'required' => true,
                'attr' => [
                    'class' => 'menu2',
                ],

                ])
                 
                ->add('option', CheckboxType::class, [
                    'required' => false,
                    'attr' => [
                        'class' => 'cm-toggle blue',
                        
                    ],
                ])
                ->add('option2', CheckboxType::class, [
                    'required' => false,
                    'attr' => [
                        'class' => 'cm-toggle blue',
                        
                    ],
                ])
                ->add('option3', CheckboxType::class, [
                    'required' => false,
                    'attr' => [
                        'class' => 'cm-toggle blue',
                        
                    ],
                ])
                ->add('option4', CheckboxType::class, [
                    'required' => false,
                    'attr' => [
                        'class' => 'cm-toggle blue',
                        
                    ],
                ])
                ->add('option5', CheckboxType::class, [
                    'required' => false,
                    'attr' => [
                        'class' => 'cm-toggle blue',
                        
                    ],
                ])
                ->add('option6', CheckboxType::class, [
                    'required' => false,
                    'attr' => [
                        'class' => 'cm-toggle blue',
                        
                    ],
                ])
                ->add('option7', CheckboxType::class, [
                    'required' => false,
                    'attr' => [
                        'class' => 'cm-toggle blue',
                        
                    ],
                ])
                ->add('option8', CheckboxType::class, [
                    'required' => false,
                    'attr' => [
                        'class' => 'cm-toggle blue',
                        
                    ],
                ])
                ->add('option9', CheckboxType::class, [
                    'required' => false,
                    'attr' => [
                        'class' => 'cm-toggle blue',
                        
                    ],
                ])
                ->add('option10', CheckboxType::class, [
                    'required' => false,
                    'attr' => [
                        'class' => 'cm-toggle blue',
                        
                    ],
                ]);
        }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // 'data_class' => User::class,
        ]);
    }
}
