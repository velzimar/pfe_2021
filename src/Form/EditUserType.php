<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class EditUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('nom')
            ->add('prenom')
            ->add('cin')
            ->add('phone')
            ->add('businessName')
            ->add('businessDescription')
            ->add('latitude', HiddenType::class,array(
                'required'=>true,
                'attr' => array(
                    'readonly' => true,
                ),
            ))
            ->add('longitude', HiddenType::class, array(
                'required'=>true,
                'attr' => array(
                    'readonly' => true,
                ),
            ))
            ->add('category_id', EntityType::class, [
                'choice_label'=>'nom',
                'class'=> Category::class,
                'multiple'=>false,
                'required'=>false,
            ])
            ->add('imageFile', FileType::class, [
                'required'=>false
            ])

            ->add('admin', CheckboxType::class, [
                'mapped' => false,
                'required'=>false
            ])
            ->add('vendeur', CheckboxType::class, [
                'mapped' => false,
                'required'=>false
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
