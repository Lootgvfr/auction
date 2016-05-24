<?php

namespace AppBundle\Controller;

use AppBundle\Form\Edit_profile;
use AppBundle\Entity\User;
use AppBundle\Entity\CommentUser;
use AppBundle\Entity\CommentLot;
use AppBundle\Entity\Lot;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ControlPanelController extends Controller
{
	
	/**
     * @Route("/control_panel/moderator", name="moderator")
     */
    public function moderatorAction()
    {
		
		$comments = $this->getDoctrine()
		->getRepository('AppBundle:CommentUser')
		->findByStatus('unchecked');
		
		$lot_comments = $this->getDoctrine()
		->getRepository('AppBundle:CommentLot')
		->findByStatus('unchecked');
		
		return $this->render('moder.html.twig', 
			array(
				'comments' => $comments,
				'lot_comments' => $lot_comments
			));
		
	}
	
	/**
     * @Route("/check_user/{id}", name="check_user")
     */
    public function checkUserAction($id)
    {
		$em = $this->getDoctrine()->getManager();
		$comment = $this->getDoctrine()
		->getRepository('AppBundle:CommentUser')
		->find($id);
		
		$comment->setStatus('checked');
				
		$em->flush();
		
		return $this->redirectToRoute('moderator');
		
	}
  
	/**
     * @Route("/delete_user/{id}", name="delete_user")
     */
    public function deleteUserAction($id)
    {
		$em = $this->getDoctrine()->getManager();
		$comment = $this->getDoctrine()
		->getRepository('AppBundle:CommentUser')
		->find($id);
				
		$em->remove($comment);
		$em->flush();
		
		return $this->redirectToRoute('moderator');
		
	}
	
	
}
?>