<?php

namespace OrderBundle\Form;

use OrderBundle\Entity\Devis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DevisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('items', CollectionType::class, array(
                'entry_type' => CommandeItemEditTypeClient::class,
                'entry_options' => array('label' => false),
            ))
            ->add('save',  SubmitType::class, array(
                'attr' => array('class' => 'button button-xl button-dark button-fullwidth')
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Devis::class,
        ));
    }
}