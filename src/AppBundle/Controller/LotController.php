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
	private function prs($string)
	{
		return str_replace(" ", "_", $string);
	}

	/**
     * @Route("/make_lot/{category}", name="make-lot-cat")
     */
    public function makeLotAction(Request $request, $category)
    {
		if( !$this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') )
		{
			return $this->redirectToRoute('home');
		}
		$error = '';
		$name = '';
		$description = '';
		$starting_price = '';
		$cat = $this->getDoctrine()->getRepository('AppBundle:Category')->findOneByName($category);
		$props = $cat->getProperties();
		
		$properties = [];
		for ($i = 0; $i < count($props); $i++)
		{
			array_push($properties, array('name' => $props[$i]->getName(), 'und_name' => $this->prs($props[$i]->getName()), 'value' => '') );
		}
		
		if('POST' === $request->getMethod())
		{
			try
			{
				// TODO check input
				for ($i = 0; $i < count($properties); $i++)
				{
					$str = $request->request->get($properties[$i]['und_name']);
					$properties[$i]['value'] = $str;
				}
				$name = $request->request->get('Name');
				$description = $request->request->get('Description');
				$starting_price = $request->request->get('Starting_price');
				
				$em = $this->getDoctrine()->getManager();
				if ($request->request->get('Name') == '' or $request->request->get('Description') == '' or $request->request->get('Starting_price') == '')
				{
					throw new \Exception('Some of the fields are empty');
				}
				$lot = new Lot();
				$lot->setName($request->request->get('Name'));
				$lot->setDescription($request->request->get('Description'));
				$lot->setStartPrice($request->request->get('Starting_price'));
				$lot->setStatus('Unconfirmed');
				$now = new \DateTime();
				$lot->setStartDate($now);
				$lot->setEndDate($now);
				$lot->setCategory($cat);

				for ($i = 0; $i < count($properties); $i++)
				{
					if ($request->request->get($properties[$i]['und_name']) == '')
					{
						throw new \Exception('Some of the fields are empty (' + $properties[$i]['name'] + ')');
					}
					$str = $request->request->get($properties[$i]['und_name']);
					$val = new Value();
					$val->setVal($str);
					$val->setProperty($props[$i]);
					$val->setLot($lot);
					$em->persist($val);
				}
				
				$em->persist($lot);
				$em->flush();
				
				return $this->redirectToRoute('lot', array('id' => $lot->getId()));
				
			}
			catch (\Exception $e)
			{
				$error = $e->getMessage();
			}
		}
		
        return $this->render('make-lot.html.twig', array(
			"category" => $cat,
			"properties" => $properties,
			"error" => $error,
			"name" => $name,
			'description' => $description,
			'starting_price' => $starting_price,
			'edit' => false,
			'found' => true
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
     * @Route("/lots/{id}/delete", name="delete-lot")
     */
    public function deleteLotAction(Request $request, $id)
    {
		$em = $this->getDoctrine()->getManager();
		$lot = $this->getDoctrine()->getRepository('AppBundle:Lot')->findOneById($id);
		$em->remove($lot);
		$em->flush();
        return $this->redirectToRoute('home');
    }

	/**
     * @Route("/lots/{id}", name="lot")
     */
    public function lotAction(Request $request, $id)
    {
		$lot = $this->getDoctrine()->getRepository('AppBundle:Lot')->findOneById($id);
		$found = false;
		if ($lot)
		{
			$found = true;
			$properties = [];
			$values = $lot->getValues();
			for($i = 0; $i < count($values); $i++)
			{
				$name = $values[$i]->getProperty()->getName();
				$val = $values[$i]->getVal();
				array_push($properties, array('name' => $name, 'value' => $val) );
			}
		}
		else
		{
			$properties = [];
		}
        return $this->render('lot.html.twig', array(
			"found" => $found,
			"lot" => $lot,
			'properties' => $properties
        ));
    }
	
	/**
     * @Route("/lot/{id}/edit", name="edit-lot")
     */
    public function editLotAction(Request $request, $id)
    {
		if( !$this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') )
		{
			return $this->redirectToRoute('home');
		}
		$lot = $this->getDoctrine()->getRepository('AppBundle:Lot')->findOneById($id);
		$found = false;
		if (!$lot)
		{
			$found = true;
			$error = '';
			$name = '';
			$description = '';
			$starting_price = '';
			$cat = null;
			$propeties = [];
		}
		else
		{
			$error = '';
			$name = $lot->getName();
			$description = '';
			$starting_price = '';
			$cat = $lot->getCategory();
			$vals = $lot->getValues();
			
			$properties = [];
			for ($i = 0; $i < count($vals); $i++)
			{
				array_push($properties, array('name' => $vals[$i]->getProperty()->getName(),
					'und_name' => $this->prs($vals[$i]->getProperty()->getName()),
					'value' => $vals[$i]->getVal()) );
			}
			
			if('POST' === $request->getMethod())
			{
				try
				{
					// TODO check input
					for ($i = 0; $i < count($properties); $i++)
					{
						$str = $request->request->get($properties[$i]['und_name']);
						$properties[$i]['value'] = $str;
					}
					$name = $request->request->get('Name');
					$description = $request->request->get('Description');
					$starting_price = $request->request->get('Starting_price');
					
					$em = $this->getDoctrine()->getManager();
					if ($request->request->get('Name') == '' or $request->request->get('Description') == '' or $request->request->get('Starting_price') == '')
					{
						throw new \Exception('Some of the fields are empty');
					}
					$lot = new Lot();
					$lot->setName($request->request->get('Name'));
					$lot->setDescription($request->request->get('Description'));
					$lot->setStartPrice($request->request->get('Starting_price'));
					$lot->setStatus('Unconfirmed');
					$now = new \DateTime();
					$lot->setStartDate($now);
					$lot->setEndDate($now);
					$lot->setCategory($cat);

					for ($i = 0; $i < count($properties); $i++)
					{
						if ($request->request->get($properties[$i]['und_name']) == '')
						{
							throw new \Exception('Some of the fields are empty (' + $properties[$i]['name'] + ')');
						}
						$str = $request->request->get($properties[$i]['und_name']);
						$val = new Value();
						$val->setVal($str);
						$val->setProperty($props[$i]);
						$val->setLot($lot);
						$em->persist($val);
					}
					
					$em->persist($lot);
					$em->flush();
					
					return $this->redirectToRoute('lot', array('id' => $lot->getId()));
					
				}
				catch (\Exception $e)
				{
					$error = $e->getMessage();
				}
			}
		}
		
        return $this->render('make-lot.html.twig', array(
			"category" => $cat,
			"properties" => $properties,
			"error" => $error,
			"name" => $name,
			'description' => $description,
			'starting_price' => $starting_price,
			'edit' => true,
			'found' => $found,
			'id' => $id
        ));
    }
}
?>