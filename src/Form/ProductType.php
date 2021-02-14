<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\ProductCategory;
use App\Repository\ProductCategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('nom')
            ->add('description')
            ->add('prix')
            ->add('qtt')
            ->add('category', EntityType::class, [
                'class'=> ProductCategory::class,
                'multiple'=>false,
                'required'=>false,
                'query_builder' => function (ProductCategoryRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.nom', 'DESC')
                        ->andWhere('u.id = :param')
                        ->setParameter('param','1')
                        ;
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
