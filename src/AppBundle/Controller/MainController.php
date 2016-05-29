<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contact;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MainController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function homeAction(Request $request)
    {
        return $this->render('home.html.twig', array(
        ));
    }
	
	/**
     * @Route("/contacts", name="contacts")
     */
    public function contactsAction(Request $request)
    {
        $message = "";
		if (isset($_POST['submit']))
        {
            $em = $this->getDoctrine()->getManager();
            $contact = new Contact();
            $author = $this->getDoctrine()->getRepository('AppBundle:User')->findOneByUsername($_POST['username']);
            $contact->setMessage($_POST['message']);
			$contact->setAuthor($author);
            $now = new \DateTime();
            $contact->setDate($now);
            $em->persist($contact);
            $em->flush();
            $message = "Your message was successfully sent";
        }
        return $this->render('contacts.html.twig',
            array('message' => $message
        ));
    }
	
	/**
     * @Route("/categories/{name}", name="category_display")
     */
    public function categoryAction(Request $request, $name)
    {
		$cat = $this->getDoctrine()->getRepository('AppBundle:Category')->findOneByName($name);
		$lots = $this->getDoctrine()->getRepository('AppBundle:Lot')->findByCategory($cat);
		foreach($lots as $lot)
		{
			$lot->getCurrentPrice();
		}
        return $this->render('category_display.html.twig', array(
			"lots" => $lots,
			"name" => $name
        ));
    }
	
	public function categoriesAction()
	{
		$categories = $this->getDoctrine()->getRepository('AppBundle:Category')->findAll();
		return $this->render('categories.html.twig', array(
			"categories" => $categories
        ));
	}
}
?>