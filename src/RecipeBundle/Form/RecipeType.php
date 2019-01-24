<?php

namespace RecipeBundle\Form;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use RecipeBundle\Entity\Recipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('ingredients', CKEditorType::class)
            ->add('directions', CKEditorType::class)
            ->add('imageFile', VichImageType::class, array(
                'download_link'     => false,
                'required'    => false,
                'allow_delete' => false,
            ))
            ->add('save',  SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Recipe::class,
        ));
    }
}