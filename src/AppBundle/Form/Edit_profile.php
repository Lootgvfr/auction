<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class Edit_profile extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		
		$user = $options['user'];
        $builder
			->add('name', TextType::class, array(
				'attr' => array('placeholder' => 'Name', 'class' => 'text-input', 'value'=> $user->getName()),
				'label' => false, 'required' => false
			 ))
            ->add('email', EmailType::class, array(
				'attr' => array('placeholder' => 'Email', 'class' => 'text-input', 'data'=>$user->getEmail()),
				'label' => false, 'required' => false
			 ))
			->add('address', TextType::class, array(
				'attr' => array('placeholder' => 'Address', 'class' => 'text-input'/*, 'data'=>$options.address*/),
				'label' => false, 'required' => false
			 ))
			 ->add('phone', TextType::class, array(
				'attr' => array('placeholder' => 'Phone', 'class' => 'text-input'/*, 'data'=>$options.phone*/),
				'label' => false, 'required' => false
			 ))
			 ->add('info', TextareaType::class, array(
				'attr' => array('placeholder' => 'Info', 'class' => 'textarea'/*, 'data'=> $options.info*/),
				'label' => false, 'required' => false
			 ))
            ->add('file', FileType::class, array('required' => false)
			)
			
        ;
    }

	public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array(
            'user',
        ));
		 $resolver->setDefaults(array(
			'user' => null,
		));
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
        ));
    }
}