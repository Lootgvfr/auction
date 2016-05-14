<?php

namespace AppBundle\Controller;

use AppBundle\Form\Edit_profile;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends Controller
{
	
	/**
     * @Route("/profile/edit", name="edit")
     */
    public function editAction(Request $request)
    {
		$user =  $this->get('security.token_storage')->getToken()->getUser();

		
		$formEdit = $this->get('form.factory')->create(new Edit_profile(), $user, array(
			'user' => $user
		));
		if('POST' === $request->getMethod())
		{
			$formEdit->handleRequest($request);
			if ($formEdit->isSubmitted()) 
			{
				$em = $this->getDoctrine()->getManager();
				$user->upload();
				
				$em->flush();
				
				return $this->redirectToRoute('edit');
			}
		}
		
        return $this->render('profile_edit.html.twig', 
			array('form' => $formEdit->createView())
			);
    }
	
	
	
    /**
     * @Route("/profile/{username}", name="profile")
     */
    public function profileAction($username)
    {
		$user = $this->getDoctrine()
        ->getRepository('AppBundle:User')
        ->findOneByUsername($username);
		
		
        return $this->render('profile.html.twig', 
			array(
				'username' => $username,
				'name'     => $user->getName(),
				'email'    => $user->getEmail(),
				'group'    => $user->getGroup(),
				'path'	   => $user->getWebPath()
			));
    }
	
	
	
}
?>