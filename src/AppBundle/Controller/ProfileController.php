<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends Controller
{
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
				'group'    => $user->getGroup()
			));
    }
	/**
     * @Route("/profile/edit", name="edit")
     */
    public function editAction()
    {
		$user = $this->getDoctrine()
        ->getRepository('AppBundle:User')
        ->findOneByUsername(app.user.username);
		
		
        return $this->render('profile.html.twig', 
			array(
				'username' => $user->getUsername() ,
				'name'     => $user->getName(),
				'email'    => $user->getEmail(),
				'group'    => $user->getGroup()
			));
    }
	
	
}
?>