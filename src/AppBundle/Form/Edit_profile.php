<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class Register extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
			->add('username', TextType::class, array(
				'attr' => array('placeholder' => 'Username', 'class' => 'text-input'),
				'label' => false
			 ))
            ->add('email', EmailType::class, array(
				'attr' => array('placeholder' => 'Email', 'class' => 'text-input'),
				'label' => false
			 ))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
				'invalid_message' => 'The password fields must match',
                'first_options'  => array('label' => false, 'attr' => array('placeholder' => 'Password', 'class' => 'text-input')),
                'second_options' => array('label' => false, 'attr' => array('placeholder' => 'Repeat password', 'class' => 'text-input')),
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
        ));
    }
}