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
		$buyout_price = '';
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
				$buyout_price = $request->request->get('Buyout_price');
				
				$em = $this->getDoctrine()->getManager();
				if ($request->request->get('Name') == '' or $request->request->get('Description') == '' or $request->request->get('Starting_price') == '')
				{
					throw new \Exception('Some of the fields are empty');
				}
				$lot = new Lot();
				$lot->setName($request->request->get('Name'));
				$lot->setDescription($request->request->get('Description'));
				$lot->setStartPrice($request->request->get('Starting_price'));
				if ($buyout_price != '')
				{
					$lot->setBuyoutPrice($buyout_price);
				}
				$lot->setStatus('Unconfirmed');
				$lot->setAuthor($this->get('security.token_storage')->getToken()->getUser());
				$now = new \DateTime();
				$lot->setStartDate($now);
				$lot->setEndDate($now);
				$lot->setCategory($cat);
				$data = $this->getRequest()->request->all();
				$file = $this->getRequest()->files->get('file');
				if ($file)
				{
					$lot->setFile($file);
					$lot->upload();
				}

				for ($i = 0; $i < count($properties); $i++)
				{
					if ($properties[$i]['value'] == '')
					{
						$ex = "Some of the fields are empty (" . $properties[$i]['name'] . ")";
						throw new \Exception($ex);
					}
					$str = $properties[$i]['value'];
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
			'buyout_price' => $buyout_price,
			'edit' => false,
			'found' => true
        ));
    }
	
	/**
     * @Route("/make_lot_choose", name="make-lot")
     */
    public function makeLotChooseAction(Request $request)
    {
		if( !$this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') )
		{
			return $this->redirectToRoute('home');
		}
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
		$author = null;
		$path = '';
		if ($lot)
		{
			$found = true;
			$properties = [];
			$values = $lot->getValues();
			$author = $lot->getAuthor();
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
			'properties' => $properties,
			'author' => $author
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
			$error = '';
			$name = '';
			$description = '';
			$starting_price = '';
			$buyout_price = '';
			$cat = null;
			$propeties = [];
		}
		else
		{
			$found = true;
			$error = '';
			$name = $lot->getName();
			$description = $lot->getDescription();
			$starting_price = $lot->getStartPrice();
			$buyout_price = $lot->getBuyoutPrice();
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
					$buyout_price = $request->request->get('Buyout_price');
					
					$em = $this->getDoctrine()->getManager();
					if ($request->request->get('Name') == '' or $request->request->get('Description') == '' or $request->request->get('Starting_price') == '')
					{
						throw new \Exception('Some of the fields are empty');
					}
					$lot->setName($request->request->get('Name'));
					$lot->setDescription($request->request->get('Description'));
					$lot->setStartPrice($request->request->get('Starting_price'));
					$data = $this->getRequest()->request->all();
					$file = $this->getRequest()->files->get('file');
					if ($file)
					{
						$lot->setFile($file);
						$lot->upload();
					}
					
					if ($buyout_price != '')
					{
						$lot->setBuyoutPrice($buyout_price);
					}

					for ($i = 0; $i < count($vals); $i++)
					{
						if ($properties[$i]['value'] == '')
						{
							$ex = "Some of the fields are empty (" . $properties[$i]['name'] . ")";
							throw new \Exception($ex);
						}
						$str = $properties[$i]['value'];
						$vals[$i]->setVal($str);
						$em->persist($vals[$i]);
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
			'buyout_price' => $buyout_price,
			'edit' => true,
			'found' => $found,
			'id' => $id
        ));
    }
}
?>