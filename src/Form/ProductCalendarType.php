<?php

namespace App\Form;

use App\Entity\ProductCalendar;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductCalendarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isActive')
            ->add('period', TextType::class, [
                "mapped" => false,
            ]) ->add('start', DateTimeType::class, [
                'label' => "Date d'activation",
                'required'=>false,
                'widget' => 'single_text',
            ])
            ->add('end', DateTimeType::class, [
                'label' => "Dernièr jour ouvert pour réservation",'required'=>false,
                'widget' => 'single_text',
            ])
            ->add('slots', TextareaType::class, [
                "mapped" => false,
            ]);

        /*
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $user = $event->getData();
            $form = $event->getForm();

            if (!$user) {
                return;
            }

            // checks whether the user has chosen to display their email or not.
            // If the data was submitted previously, the additional value that is
            // included in the request variables needs to be removed.
            if (isset($user['isActive']) && $user['isActive']) {
                $form
                    ->add('start', DateTimeType::class, [
                    'label' => "Date d'activation",
                    'required' => true,
                    'widget' => 'single_text',
                    ])
                    ->add('end', DateTimeType::class, [
                        'required' => true,
                        'label' => "Dernièr jour ouvert pour réservation",
                        'widget' => 'single_text',
                    ]);
            } else {
                unset($user['start']);
                unset($user['end']);
                $event->setData($user);
            }
        });

        */
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductCalendar::class,
        ]);
    }
}
