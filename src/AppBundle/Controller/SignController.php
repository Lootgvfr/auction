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
     * @Route("/sign", name="sign")
     */
    public function signAction(Request $request)
    {
		$logger = $this->get('logger');
		$user = new User();
        $formRegister = $this->createForm(Register::class, $user);
		if('POST' === $request->getMethod())
		{
			if ($request->request->has('register'))
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
					
					return $this->redirectToRoute('sign');
				}
			}
			else if ($request->request->has('login'))
			{
				// LOGIN FORM HERE
			}
		}

        return $this->render('sign.html.twig', 
			array('form' => $formRegister->createView())
			);
    }
	
}
?>