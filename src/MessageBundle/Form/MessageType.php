<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'attr' => array(
                    'placeholder' => 'Name',
                    'class' => 'form-control',
                    'label' => 'translate.form_name',
                )
            ))
            ->add('email', EmailType::class, array(
                'attr' => array(
                    'placeholder' => 'Email Address',
                    'class' => 'form-control',
                    'label' => 'translate.form_email',
                )
            ))
            ->add('message', TextareaType::class, array(
                'attr' => array(
                    'class' => 'form-control',
                    'rows' => 4,
                    'label' => 'translate.form_message',
                )
            ))
            ->add('save',  SubmitType::class, array(
                'label' => 'translate.form_save',
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => \MessageBundle\Entity\Message::class,
        ));
    }
}