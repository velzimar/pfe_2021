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
        $user = $options['userId'];
        //echo $user;
        $builder
            ->add('nom')
            ->add('description')
            ->add('prix')
            ->add('qtt')
        ;

        $role = $options['userRole'];
        //echo $role;
        if($role){
            $builder
                ->add('business')
                ->add('category', EntityType::class, [
                    'class'=> ProductCategory::class,
                    'multiple'=>false,
                    'required'=>false,
                    'query_builder' => function (ProductCategoryRepository $er) use ($user){
                        return $er->createQueryBuilder('u')
                            ->orderBy('u.nom', 'DESC')
                            ->andWhere('u.businessId = :param')
                            ->setParameter('param',$user)
                            ;
                    },
                ])
            ;
        }else{
            $builder
                ->add('business')
                ->add('category', EntityType::class, [
                    'class'=> ProductCategory::class,
                    'multiple'=>false,
                    'required'=>false,
                    'query_builder' => function (ProductCategoryRepository $er) use ($user){
                        return $er->createQueryBuilder('u')
                            ->orderBy('u.nom', 'DESC')
                            ->andWhere('u.businessId = :param')
                            ->setParameter('param',$user)
                            ;
                    },
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
        $resolver->setRequired([
            'userId',
            'userRole'
    ]);
    }
}
