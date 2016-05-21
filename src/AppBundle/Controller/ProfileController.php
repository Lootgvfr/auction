<?php

namespace AppBundle\Controller;

use AppBundle\Form\Edit_profile;
use AppBundle\Entity\User;
use AppBundle\Entity\CommentUser;
use AppBundle\Form\CommentUserForm;
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
		
		
        return $this->render('profile.html.twig', 
			array(
				'username' => $username,
				'name'     => $user->getName(),
				'email'    => $user->getEmail(),
				'group'    => $user->getGroup(),
				'path'	   => $user->getWebPath(),
				'address'  => $user->getAddress(),
				'phone'    => $user->getPhone(),
				'info'     => $user->getInfo(),
				'id'       => $user->getId()
			));
    }
	
	
	public function commentNewAction(Request $request, User $seller)
    {
        $form = $this->createForm(new CommentUserForm());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Comment $comment */
            $comment = $form->getData();
            $comment->setAuthor($this->getUser());
			$comment->setSeller($seller);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('profile', array('username' => $seller->username()));
        }

        return $this->render('blog/comment_form_error.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }

    /**
     * This controller is called directly via the render() function in the
     * blog/post_show.html.twig template. That's why it's not needed to define
     * a route name for it.
     *
     * The "id" of the Post is passed in and then turned into a Post object
     * automatically by the ParamConverter.
     *
     * @param Post $post
     *
     * @return Response
     */
    public function commentFormAction(Post $post)
    {
        $form = $this->createForm(new CommentType());

        return $this->render('blog/_comment_form.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }
	
	
	
}
?>