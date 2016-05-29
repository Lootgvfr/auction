<?php

namespace AppBundle\Controller;

use AppBundle\Form\Edit_profile;
use AppBundle\Entity\User;
use AppBundle\Entity\CommentUser;
use AppBundle\Form\CommentUserForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends Controller
{
	
	/**
     * @Route("/profile/edit", name="edit")
     */
    public function editAction(Request $request)
    {
		
		$securityContext = $this->container->get('security.authorization_checker');
		if (!$securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
			
			return $this->redirectToRoute('login');
		}
		
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
				
				return $this->redirectToRoute('profile');
			}
		}
		
        return $this->render('profile_edit.html.twig', 
			array('form' => $formEdit->createView())
			);
    }
	
	/**
     * @Route("/profile/set_group", name="set_group")
     */
    public function setGroupAction(Request $request)
    {
		if('POST' === $request->getMethod())
		{
			$em = $this->getDoctrine()->getManager();
			$user = $this->getDoctrine()
			->getRepository('AppBundle:User')
			->findOneByUsername($_POST['username']);
			$user->setGroup($_POST['new_group']);
				
			$em->flush();
		}
		return $this->redirectToRoute('profile',  array('username' => $_POST['username']));
	}
	
	
    /**
     * @Route("/profile/{username}", name="profile")
     */
    public function profileAction($username = NULL)
    {
		if (!$username)
		{
			$securityContext = $this->container->get('security.authorization_checker');
			if (!$securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
				
				return $this->redirectToRoute('login');
			}
			$user =  $this->get('security.token_storage')->getToken()->getUser();
			$username = $user->getUsername();
			return $this->redirectToRoute('profile',  array('username' => $username));
		}
		else 
		$user = $this->getDoctrine()
        ->getRepository('AppBundle:User')
        ->findOneByUsername($username);

		$em = $this->getDoctrine()->getManager();
		$query = $em->createQuery(
			'SELECT AVG(p.rating)
			FROM AppBundle:CommentUser p
			WHERE p.status = :status AND
			p.seller= :id'
		)->setParameters(array(
			'status' => 'confirmed', 
			'id' => $user));

		$rating = $query->getResult();
		 
		var_dump($rating);
		
		$lots = $this->getDoctrine()
			->getRepository('AppBundle:Lot')
			->findByAuthor($user);

		$comments = $this->getDoctrine()
        ->getRepository('AppBundle:CommentUser')
        ->findBy(
		array('status' => 'confirmed', 'seller' => $user)		);
		
        return $this->render('profile.html.twig', 
			array(
				'user'     => $user,
				'path'	   => $user->getWebPath(),
				'comments' => $comments,
				'lots'     => $lots,
				'rating'   => $rating[0][1]
			));
    }
	
	/**
     * @Route("/comment/{sellerName}/new", name = "comment_new")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Method("POST")
     * @ParamConverter("seller", options={"mapping": {"sellerName": "username"}})
     */
	public function commentNewAction(Request $request, User $seller)
    {
		if (isset($_POST['submit']))
		{
			$comment = new CommentUser();
			$comment->setAuthor($this->getUser());
			$comment->setSeller($seller);
			$comment->setStatus("Unconfirmed");
			$date = new \DateTime(); //->format('Y-m-d H:i:s')
			$comment->setDate($date);
			$comment->setText($_POST['text']);
			$comment->setRating($_POST['caption']);
			
			$entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            
		}

        return $this->redirectToRoute('profile', array('username' => $seller->getUsername()));
    }

	
}
?>