<?php

namespace AppBundle\Controller;

use AppBundle\Form\Edit_profile;
use AppBundle\Entity\User;
use AppBundle\Entity\CommentUser;
use AppBundle\Entity\CommentLot;
use AppBundle\Entity\Contact;
use AppBundle\Entity\Lot;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class ControlPanelController extends Controller
{
	
	/**
     * @Route("/control_panel/moderator/{page}", name="moderator", defaults={"page" = 1}, requirements={"page" : "\d+"})
     */
    public function moderatorAction($page)
    {
		
		$comments = $this->getDoctrine()
		->getRepository('AppBundle:CommentUser')
		->findByStatus('unconfirmed');
		
		$lot_comments = $this->getDoctrine()
		->getRepository('AppBundle:CommentLot')
		->findByStatus('unconfirmed');
			
		
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
		
		$comment->setStatus('confirmed');
				
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
	/**
     * @Route("/check_lot/{id}", name="check_lot")
     */
    public function checkLotAction($id)
    {
		$em = $this->getDoctrine()->getManager();
		$comment = $this->getDoctrine()
		->getRepository('AppBundle:CommentLot')
		->find($id);
		
		$comment->setStatus('confirmed');
				
		$em->flush();
		
		return $this->redirectToRoute('moderator');
		
	}
  
	/**
     * @Route("/delete_lot/{id}", name="delete_lot")
     */
    public function deleteLotAction($id)
    {
		$em = $this->getDoctrine()->getManager();
		$comment = $this->getDoctrine()
		->getRepository('AppBundle:CommentLot')
		->find($id);
				
		$em->remove($comment);
		$em->flush();
		
		return $this->redirectToRoute('moderator');
		
	}
	
	
	/**
     * @Route("/control_panel/manager", name="manager")
     */
    public function managerAction()
    {
		
		$lots = $this->getDoctrine()
		->getRepository('AppBundle:Lot')
		->findByStatus('unconfirmed');
		
		
		return $this->render('manager.html.twig', 
			array(
				'lots' => $lots
			));
		
	}
		/**
     * @Route("/control_panel/admin", name="admin")
     */
    public function adminAction()
    {
		
		$messages = $this->getDoctrine()
		->getRepository('AppBundle:Contact')
		->findAll();
				
		return $this->render('admin.html.twig', 
			array(
				'messages' => $messages
			));
		
	}
	/**
     * @Route("/delete_message/{id}", name="delete_message")
     */
    public function deleteMessageAction($id)
    {
		$em = $this->getDoctrine()->getManager();
		$message = $this->getDoctrine()
		->getRepository('AppBundle:Contact')
		->find($id);
				
		$em->remove($message);
		$em->flush();
		
		return $this->redirectToRoute('admin');
		
	}
	
	
	
}
?>