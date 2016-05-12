<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Property;
use AppBundle\Entity\Value;
use AppBundle\Entity\Lot;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class LotController extends Controller
{
	/**
     * @Route("/make_lot/{category}", name="make-lot-cat")
     */
    public function makeLotAction(Request $request, $category)
    {
		$error = '';
		$category = $this->getDoctrine()->getRepository('AppBundle:Category')->findOneByName($category);
		$properties = $category->getProperties();
        return $this->render('make-lot.html.twig', array(
			"properties" => $properties,
			"error" => $error
        ));
    }
	
	/**
     * @Route("/make_lot_choose", name="make-lot")
     */
    public function makeLotChooseAction(Request $request)
    {
		$categories = $this->getDoctrine()->getRepository('AppBundle:Category')->findAll();
        return $this->render('make-lot-choose.html.twig', array(
			"categories" => $categories
        ));
    }

	/**
     * @Route("/lots/{name}", name="lot")
     */
    public function lotAction(Request $request, $name)
    {
		$found = false;
		$lot = [];
		for ($i = 0; $i < count($this::$lots); $i++)
		{
			if ($this::$lots[$i]["name"] == $name)
			{
				$lot = $this::$lots[$i];
				$found = true;
			}
		}
        return $this->render('lot.html.twig', array(
			"found" => $found,
			"lot" => $lot
        ));
    }
}
?>