<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class Edit_profile extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
			->add('name', TextType::class, array(
				'attr' => array('placeholder' => 'Name', 'class' => 'text-input', 'value'=> $options.name),
				'label' => false
			 ))
            ->add('email', EmailType::class, array(
				'attr' => array('placeholder' => 'Email', 'class' => 'text-input', 'data'=>$options.email),
				'label' => false
			 ))
			->add('address', TextType::class, array(
				'attr' => array('placeholder' => 'Email', 'class' => 'text-input', 'data'=>$options.address),
				'label' => false
			 ))
			 ->add('phone', TextType::class, array(
				'attr' => array('placeholder' => 'Email', 'class' => 'text-input', 'data'=>$options.phone),
				'label' => false
			 ))
			 ->add('info', TextareaType::class, array(
				'attr' => array('placeholder' => 'Email', 'class' => 'textarea', 'data'=> $options.info),
				'label' => false
			 ))
            ->add('file', FileType::class, array(
			'attr' => array('data' => $options.file))
			)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
        ));
    }
}