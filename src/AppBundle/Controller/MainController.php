<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MainController extends Controller
{
	private static $lots = [
		["category"=>"Notebooks", "name"=>"Lenovo HZ-1234", "description"=>"Good computer.", "startPrice"=>5000],
		["category"=>"Notebooks", "name"=>"Asus Transformer Super Pro 3000", "description"=>"100500 in 1!", "startPrice"=>10000],
		["category"=>"Notebooks", "name"=>"Samsung Quasar 1337", "description"=>"Cosmic price.", "startPrice"=>100000],
		["category"=>"Phones", "name"=>"Apple iPhone 50SS", "description"=>"Even better.", "startPrice"=>20000],
		["category"=>"Phones", "name"=>"Samsung Galaxy B35", "description"=>"Another one.", "startPrice"=>7000],
		["category"=>"Phones", "name"=>"Addle iFone 1000", "description"=>"Definitely not fake.", "startPrice"=>1]];
	
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