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
     * @Route("/control_panel/comments_user/{page}", name="comments_user", defaults={"page" = 1}, requirements={"page" : "\d+"})
     */
    public function commentUserAction($page)
    {
		$em = $this->getDoctrine()->getManager();
		
		$per_page = 10;  
		$pages_query = $em->createQuery(
						'SELECT COUNT(p.id)
						FROM AppBundle:CommentUser p
						WHERE p.status = :status'
					)->setParameter('status', 'unconfirmed');
		$pages = $pages_query->getResult();
		
		$pages = ceil($pages[0][1] / $per_page);   
		$start = ($page - 1) * $per_page;  
		
		if ($pages > 5)
		{
			if ($page > 3)
			{
				if ($page < $page - 2)
				{
					$start_pag = $pages - 2;
					$end_pag = $pages + 2;
				}
				else if ($page > $page - 2)
				{
					$start_pag = $pages - 4;
					$end_pag = $pages;
				}
			}
			else {
				$start_pag = 1;
				$end_pag = 5;
			}
		}
		else
		{
			$start_pag = 1;
			$end_pag = $pages;
		}

		$query = $em->createQuery(
			'SELECT p
			FROM AppBundle:CommentUser p
			WHERE p.status = :status
			ORDER BY p.date ASC'
		)->setParameter('status', 'unconfirmed')
		->setFirstResult($start)
        ->setMaxResults($per_page);

		$comments = $query->getResult();
		
		return $this->render('comments_user.html.twig', 
			array(
				'comments' => $comments,
				'start_pag' => $start_pag,
				'end_pag' => $end_pag,
				'page' => $page,
				'pages' => $pages
			));
		
		
		
	}
	
	/**
     * @Route("/control_panel/comments_lot/{page}", name="comments_lot", defaults={"page" = 1}, requirements={"page" : "\d+"})
     */
    public function commentLotAction($page)
    {
		
		$em = $this->getDoctrine()->getManager();
		
		$per_page = 10;   
		$pages_query = $em->createQuery(
						'SELECT COUNT(p.id)
						FROM AppBundle:CommentLot p
						WHERE p.status = :status'
					)->setParameter('status', 'unconfirmed');
		
		$pages = $pages_query->getResult();
		
		$pages = ceil($pages[0][1] / $per_page);  
		$start = ($page - 1) * $per_page; 
		
		if ($pages > 5)
		{
			if ($page > 3)
			{
				if ($page < $page - 2)
				{
					$start_pag = $pages - 2;
					$end_pag = $pages + 2;
				}
				else if ($page > $page - 2)
				{
					$start_pag = $pages - 4;
					$end_pag = $pages;
				}
			}
			else {
				$start_pag = 1;
				$end_pag = 5;
			}
		}
		else
		{
			$start_pag = 1;
			$end_pag = $pages;
		}
				
		
		
		
		$query = $em->createQuery(
			'SELECT p
			FROM AppBundle:CommentLot p
			WHERE p.status = :status
			ORDER BY p.date ASC'
		)->setParameter('status', 'unconfirmed')
		->setFirstResult($start)
        ->setMaxResults($per_page);

		$comments = $query->getResult();
		
		return $this->render('comments_lot.html.twig', 
			array(
				'lot_comments' => $comments,
				'start_pag' => $start_pag,
				'end_pag' => $end_pag,
				'page' => $page,
				'pages' => $pages
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
		
		return $this->redirectToRoute('comments_user');
		
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
		
		return $this->redirectToRoute('comments_user');
		
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
		
		return $this->redirectToRoute('comments_lot');
		
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
		
		return $this->redirectToRoute('comments_lot');
		
	}
	
	
	/**
     * @Route("/control_panel/manager/{page}", name="manager", defaults={"page" = 1}, requirements={"page" : "\d+"})
     */
    public function managerAction($page)
    {
		$em = $this->getDoctrine()->getManager();
		
		$per_page = 10;   
		$pages_query = $em->createQuery(
						'SELECT COUNT(p.id)
						FROM AppBundle:Lot p
						WHERE p.status = :status'
					)->setParameter('status', 'unconfirmed');
		$pages = $pages_query->getResult();
		
		$pages = ceil($pages[0][1] / $per_page);  
		$start = ($page - 1) * $per_page; 
		
		if ($pages > 5)
		{
			if ($page > 3)
			{
				if ($page < $page - 2)
				{
					$start_pag = $pages - 2;
					$end_pag = $pages + 2;
				}
				else if ($page > $page - 2)
				{
					$start_pag = $pages - 4;
					$end_pag = $pages;
				}
			}
			else {
				$start_pag = 1;
				$end_pag = 5;
			}
		}
		else
		{
			$start_pag = 1;
			$end_pag = $pages;
			if (!$end_pag)
			{
				$end_pag = 1;
				$pages = 1;
			}
		}

		$query = $em->createQuery(
			'SELECT p
			FROM AppBundle:Lot p
			WHERE p.status = :status'
		)->setParameter('status', 'unconfirmed')
		->setFirstResult($start)
        ->setMaxResults($per_page);

		$lots = $query->getResult();

		
		
		return $this->render('manager.html.twig', 
			array(
				'lots' => $lots,
				'start_pag' => $start_pag,
				'end_pag' => $end_pag,
				'page' => $page,
				'pages' => $pages
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