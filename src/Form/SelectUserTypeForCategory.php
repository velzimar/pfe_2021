<?php

namespace App\Form;

use App\Entity\ProductCategory;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SelectUserTypeForCategory extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('businessId', EntityType::class, [
                'class'=> User::class,
                'choice_label' => function (User $customer) {
                    return $customer->getNom() . ' ' . $customer->getId();
                },
                'multiple'=>false,
                'required'=>false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductCategory::class,
        ]);
    }
}
