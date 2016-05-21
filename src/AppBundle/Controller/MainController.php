<?php

namespace AppBundle\Controller;

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
        return $this->render('contacts.html.twig', array(
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