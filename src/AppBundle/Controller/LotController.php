<?php

namespace AppBundle\Controller;

<<<<<<< HEAD
use AppBundle\Entity\Category;
use AppBundle\Entity\Property;
use AppBundle\Entity\Value;
use AppBundle\Entity\Lot;
=======
>>>>>>> master
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class LotController extends Controller
{
	/**
     * @Route("/make_lot", name="make-lot")
     */
    public function makeLotAction(Request $request)
    {
<<<<<<< HEAD
		$em = $this->getDoctrine()->getManager();

		// tells Doctrine you want to (eventually) save the Product (no queries yet)
		$em->persist($product);

		// actually executes the queries (i.e. the INSERT query)
		$em->flush();
		
		$category = new Category();
		$category->setName("Notebooks");
		$category2 = new Category();
		$category2->setName("Phones");
		
		
=======
>>>>>>> master
        return $this->render('make-lot.html.twig', array(
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