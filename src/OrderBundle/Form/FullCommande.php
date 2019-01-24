<?php

namespace OrderBundle\Form;

use OrderBundle\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FullCommande extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('orderInfo', PersonalInfoType::class)
            ->add('saleDate', DateType::class, array(
                'data' => new \DateTime("now")
            ))
            ->add('items', CollectionType::class, array(
                'entry_type' => \OrderBundle\Form\ProductEntityType::class,
                'entry_options' => array('label' => false),
                'allow_add' => true,
                'by_reference' => false,
                'allow_delete' => true,
            ))
            ->add('save',  SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Commande::class,
        ));
    }
}