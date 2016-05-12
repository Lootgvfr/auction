<?php

namespace AppBundle\Controller;

use AppBundle\Form\Register;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SignController extends Controller
{
    /**
     * @Route("/register", name="register")
     */
    public function registerAction(Request $request)
    {
		$user = new User();
        $formRegister = $this->createForm(Register::class, $user);
		if('POST' === $request->getMethod())
		{
			$formRegister->handleRequest($request);
			if ($formRegister->isSubmitted() && $formRegister->isValid()) 
			{
				$password = $this->get('security.password_encoder')
					->encodePassword($user, $user->getPlainPassword());
				$user->setPassword($password);
				$user->setGroup("User");
				
				$em = $this->getDoctrine()->getManager();
				$em->persist($user);
				$em->flush();
				
				return $this->redirectToRoute('login');
			}
		}
        return $this->render('register.html.twig', 
			array('form' => $formRegister->createView())
			);
    }
	
	/**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');

		$error = $authenticationUtils->getLastAuthenticationError();

		$lastUsername = $authenticationUtils->getLastUsername();

		return $this->render(
			'login.html.twig',
			array(
				'last_username' => $lastUsername,
				'error'         => $error,
			)
		);
    }
}
?>