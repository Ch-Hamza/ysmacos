<?php

namespace OrderBundle\Form;

use OrderBundle\Entity\OrderInfo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonalInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customerFirstName', TextType::class, array(
                'required' => true,
            ))
            ->add('customerLastName', TextType::class, array(
                'required' => true,
            ))
            ->add('customerCompany', TextType::class)
            ->add('customerEmail', TextType::class, array(
                'required' => true,
            ))
            ->add('customerPhone', NumberType::class, array(
                'required' => true,
            ))
            ->add('customerCity', TextType::class, array(
                'required' => true,
            ))
            ->add('customerAddress', TextType::class, array(
                'required' => true,
            ))
            ->add('postalCode', NumberType::class, array(
                'required' => true,
            ))
            ->add('save',  SubmitType::class, array(
                'label' => 'Send',
                'attr' => array('class' => 'send-button')
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => OrderInfo::class,
        ));
    }
}