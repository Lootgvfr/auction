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
     * @Route("/make_lot", name="make-lot")
     */
    public function makeLotAction(Request $request)
    {
		$category = new Category();
		$category->setName("Notebooks");
		$category2 = new Category();
		$category2->setName("Phones");
		
		$propertyn0 = new Property();
		$propertyn0->setRange("");
		$propertyn0->setCategory($category);
		$propertyn0->setName("Manufacturer");
		
		$propertyn1 = new Property();
		$propertyn1->setRange("");
		$propertyn1->setCategory($category);
		$propertyn1->setName("Screen size");
		
		$propertyn2 = new Property();
		$propertyn2->setRange("");
		$propertyn2->setCategory($category);
		$propertyn2->setName("Screen resolution");
		
		$propertyn3 = new Property();
		$propertyn3->setRange("");
		$propertyn3->setCategory($category);
		$propertyn3->setName("RAM Size");
		
		$propertyn4 = new Property();
		$propertyn4->setRange("");
		$propertyn4->setCategory($category);
		$propertyn4->setName("HDD Size");
		
		$propertyn5 = new Property();
		$propertyn5->setRange("");
		$propertyn5->setCategory($category);
		$propertyn5->setName("Weight");
		
		$propertyn6 = new Property();
		$propertyn6->setRange("");
		$propertyn6->setCategory($category);
		$propertyn6->setName("Processor manufacturer");
		
		$propertyn7 = new Property();
		$propertyn7->setRange("");
		$propertyn7->setCategory($category);
		$propertyn7->setName("Processor model");
		
		$propertyn8 = new Property();
		$propertyn8->setRange("");
		$propertyn8->setCategory($category);
		$propertyn8->setName("Processor cores");
		
		$propertyn9 = new Property();
		$propertyn9->setRange("");
		$propertyn9->setCategory($category);
		$propertyn9->setName("Processor frequency");
		
		$propertyn10 = new Property();
		$propertyn10->setRange("");
		$propertyn10->setCategory($category);
		$propertyn10->setName("Video card");
		
		$propertyn11 = new Property();
		$propertyn11->setRange("");
		$propertyn11->setCategory($category);
		$propertyn11->setName("Operating system");
		
		$propertyn12 = new Property();
		$propertyn12->setRange("");
		$propertyn12->setCategory($category);
		$propertyn12->setName("Battery capacity");
		
		$propertyp1 = new Property();
		$propertyp1->setRange("");
		$propertyp1->setCategory($category2);
		$propertyp1->setName("Manufacturer");
		
		$propertyp2 = new Property();
		$propertyp2->setRange("");
		$propertyp2->setCategory($category2);
		$propertyp2->setName("Screen size");
		
		$propertyp3 = new Property();
		$propertyp3->setRange("");
		$propertyp3->setCategory($category2);
		$propertyp3->setName("Screen resolution");
		
		$propertyp4 = new Property();
		$propertyp4->setRange("");
		$propertyp4->setCategory($category2);
		$propertyp4->setName("RAM Size");
		
		$propertyp5 = new Property();
		$propertyp5->setRange("");
		$propertyp5->setCategory($category2);
		$propertyp5->setName("Memory size");
		
		$propertyp6 = new Property();
		$propertyp6->setRange("");
		$propertyp6->setCategory($category2);
		$propertyp6->setName("Weight");
		
		$propertyp7 = new Property();
		$propertyp7->setRange("");
		$propertyp7->setCategory($category2);
		$propertyp7->setName("Processor cores");
		
		$propertyp8 = new Property();
		$propertyp8->setRange("");
		$propertyp8->setCategory($category2);
		$propertyp8->setName("Processor frequency");
		
		$propertyp9 = new Property();
		$propertyp9->setRange("");
		$propertyp9->setCategory($category2);
		$propertyp9->setName("Operating System");
		
		$propertyp10 = new Property();
		$propertyp10->setRange("");
		$propertyp10->setCategory($category2);
		$propertyp10->setName("Camera resolution");
		
		$propertyp11 = new Property();
		$propertyp11->setRange("");
		$propertyp11->setCategory($category2);
		$propertyp11->setName("Battery capacity");
		
		$em = $this->getDoctrine()->getManager();

		$em->persist($category);
		$em->persist($category2);
		$em->persist($propertyn0);
		$em->persist($propertyn1);
		$em->persist($propertyn2);
		$em->persist($propertyn3);
		$em->persist($propertyn4);
		$em->persist($propertyn5);
		$em->persist($propertyn6);
		$em->persist($propertyn7);
		$em->persist($propertyn8);
		$em->persist($propertyn9);
		$em->persist($propertyn10);
		$em->persist($propertyn11);
		$em->persist($propertyn12);
		$em->persist($propertyp1);
		$em->persist($propertyp2);
		$em->persist($propertyp3);
		$em->persist($propertyp4);
		$em->persist($propertyp5);
		$em->persist($propertyp6);
		$em->persist($propertyp7);
		$em->persist($propertyp8);
		$em->persist($propertyp9);
		$em->persist($propertyp10);
		$em->persist($propertyp11);

		$em->flush();
		
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