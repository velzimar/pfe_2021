<?php


namespace App\Form;

use App\Entity\ProductCategory;
use App\Entity\ProductOptions;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductOptionsType extends AbstractType{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class,[
                'mapped' => false,
                'required'=>false,
            ])
            ->add('choices', ChoiceType::class, [
                'mapped' => false,
                'required'=>false,
                'multiple'=> true,
                'attr' => ['readonly' => true],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductOptions::class,
        ]);
    }
}
