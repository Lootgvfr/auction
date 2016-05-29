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

		$lots = $this->getDoctrine()
			->getRepository('AppBundle:Lot')
			->findByAuthor($user);

		$comments = $this->getDoctrine()
        ->getRepository('AppBundle:CommentUser')
        ->findBy(
		array('status' => 'checked', 'seller' => $user)		);
		
        return $this->render('profile.html.twig', 
			array(
				'user'     => $user,
				'path'	   => $user->getWebPath(),
				'comments' => $comments,
				'lots'     => $lots
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
        $form = $this->createForm(new CommentUserForm());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Comment $comment */
            $comment = $form->getData();
            $comment->setAuthor($this->getUser());
			$comment->setSeller($seller);
			$comment->setStatus("Unconfirmed");
			$date = new \DateTime(); //->format('Y-m-d H:i:s')
			$comment->setDate($date);
			$comment->setRating(5);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('profile', array('username' => $seller->getUsername()));
        }

        return $this->render('comment_form_error.html.twig', array(
            'user' => $seller,
            'form' => $form->createView(),
        ));
    }

    /**
     *
     * @param Post $post
     *
     * @return Response
     */
    public function commentFormAction(User $user)
    {
        $form = $this->createForm(new CommentUserForm());

        return $this->render('_user_comment.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }
	
	
	
}
?>