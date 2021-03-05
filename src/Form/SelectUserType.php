<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\ProductCategory;
use App\Entity\User;
use App\Repository\ProductCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SelectUserType extends AbstractType
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', EntityType::class, [
                'class'=> User::class,
                'query_builder' => function() {
                    $qb = $this->em->createQueryBuilder();
                    return $qb->select('u')
                        ->from(User::class, 'u')
                        ->where('u.roles LIKE :roles')
                        ->setParameter('roles', '%"'."ROLE_SELLER".'"%');
                },
                'choice_label' => function (User $customer) {
                    return $customer->getNom() . '_' .$customer->getPrenom() . '#' . $customer->getId();
                },
                'multiple'=>false,
                'required'=>false,
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
