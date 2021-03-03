<?php

namespace App\Form;

use App\Entity\Deal;
use App\Entity\DealCategory;
use App\Repository\DealCategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateIntervalType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
class DealType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['userId'];
        //echo $user;
        $builder
            ->add('nom')
            ->add('description')
            ->add('prix')
            ->add('qtt')
            ->add('date_add', DateTimeType::class, [
                'label'=>"Date d'activation",
                'required'=>true,
                'widget'=>'single_text',
                ])
            ->add('duration', DateIntervalType::class, [
                'label'=>"Durée",
                'required'=>true,
                'widget'=>'choice',
                'months' => array_combine(range(0, 12), range(0, 12)),
                'days' => array_combine(range(0, 30), range(0, 30)),
                'hours' => array_combine(range(0, 23), range(0, 23)),
                'minutes' => array_combine(range(0, 59), range(0, 59)),
                'with_years' => false,
                'with_months' => true,
                'with_days'   => true,
                'with_hours'  => true,
                'with_minutes'  => true,
                'labels' => [
                    'months' => "Mois",
                    'days' => "Jours",
                    'hours' => "Heures",
                    'minutes' => "Minutes",
                ]
            ])
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                //'download_label' => '...',
                //'download_uri' => false,
                //'image_uri' => true,
                'delete_label' => "Supprimer l'image",
            ]);/*
            ->add('imageFile', FileType::class, [
                'required' => false
            ]);*/

        $role = $options['userRole'];
        //echo $role;
        if ($role) {
            $builder
                ->add('business')
                ->add('category', EntityType::class, [
                    'class' => DealCategory::class,
                    'multiple' => false,
                    'required' => false,
                    'query_builder' => function (DealCategoryRepository $er) use ($user) {
                        return $er->createQueryBuilder('u')
                            ->orderBy('u.nom', 'DESC')
                            ->andWhere('u.businessId = :param')
                            ->setParameter('param', $user);
                    },
                ]);
        } else {
            $builder
                ->add('business')
                ->add('category', EntityType::class, [
                    'class' => DealCategory::class,
                    'multiple' => false,
                    'required' => false,
                    'query_builder' => function (DealCategoryRepository $er) use ($user) {
                        return $er->createQueryBuilder('u')
                            ->orderBy('u.nom', 'DESC')
                            ->andWhere('u.businessId = :param')
                            ->setParameter('param', $user);
                    },
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Deal::class,
        ]);
        $resolver->setRequired([
            'userId',
            'userRole'
        ]);
    }
}
