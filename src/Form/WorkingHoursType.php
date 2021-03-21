<?php

namespace App\Form;

use App\Entity\WorkingHours;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class WorkingHoursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $params = [
            'label'=> false,
            'required'=>false,
            'widget'=>'choice',
            'hours' => array_combine(range(0, 23), range(0, 23)),
            'minutes' => array_combine(range(0, 59), range(0, 59)),
            'with_years' => false,
            'with_months' => false,
            'with_days'   => false,
            'with_hours'  => true,
            'with_minutes'  => true,
            'labels' => [
                'hours' => false,
                'minutes' =>false,
            ],
            'mapped' => false
        ];
        $ranges = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];

        foreach($ranges as $value){

            $builder
                ->add($value.'_S1_start', DateIntervalType::class,$params)
                ->add($value.'_S1_end', DateIntervalType::class,$params)
                ->add($value.'_S2_start', DateIntervalType::class,$params)
                ->add($value.'_S2_end', DateIntervalType::class,$params);
        }
        /*
        $builder
            //->add('isActive')
            ->add('monday_S1_start', DateIntervalType::class,$params)
            ->add('monday_S1_end', DateIntervalType::class,$params)
            ->add('monday_S2_start', DateIntervalType::class,$params)
            ->add('monday_S2_end', DateIntervalType::class,$params)

            ->add('tuesday_S1_start', DateIntervalType::class,$params)
            ->add('tuesday_S1_end', DateIntervalType::class,$params)
            ->add('tuesday_S2_start', DateIntervalType::class,$params)
            ->add('tuesday_S2_end', DateIntervalType::class,$params)

            ->add('wednesday_S1_start', DateIntervalType::class,$params)
            ->add('wednesday_S1_end', DateIntervalType::class,$params)
            ->add('wednesday_S2_start', DateIntervalType::class,$params)
            ->add('wednesday_S2_end', DateIntervalType::class,$params)

            ->add('thursday_S1_start', DateIntervalType::class,$params)
            ->add('thursday_S1_end', DateIntervalType::class,$params)
            ->add('thursday_S2_start', DateIntervalType::class,$params)
            ->add('thursday_S2_end', DateIntervalType::class,$params)

            ->add('friday_S1_start', DateIntervalType::class,$params)
            ->add('friday_S1_end', DateIntervalType::class,$params)
            ->add('friday_S2_start', DateIntervalType::class,$params)
            ->add('friday_S2_end', DateIntervalType::class,$params)

            ->add('saturday_S1_start', DateIntervalType::class,$params)
            ->add('saturday_S1_end', DateIntervalType::class,$params)
            ->add('saturday_S2_start', DateIntervalType::class,$params)
            ->add('saturday_S2_end', DateIntervalType::class,$params)

            ->add('sunday_S1_start', DateIntervalType::class,$params)
            ->add('sunday_S1_end', DateIntervalType::class,$params)
            ->add('sunday_S2_start', DateIntervalType::class,$params)
            ->add('sunday_S2_end', DateIntervalType::class,$params)
*/



    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => WorkingHours::class,
        ]);
    }
}
