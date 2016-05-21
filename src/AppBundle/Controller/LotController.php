<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Property;
use AppBundle\Entity\Value;
use AppBundle\Entity\Lot;
use AppBundle\Entity\Bid;
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
			return $this->redirectToRoute('login');
		}
		$error = '';
		$name = '';
		$description = '';
		$starting_price = '';
		$buyout_price = '';
		$duration = 0;
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
					if ($lot->getBuyoutPrice() < $lot->getStartPrice())
					{
						throw new \Exception('Buyout price cannot be lower than the starting price.');
					}
				}
				else
				{
					$lot->setBuyoutPrice(null);
				}
				$lot->setStatus('Unconfirmed');
				$lot->setAuthor($this->get('security.token_storage')->getToken()->getUser());
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
				$duration = intval($request->request->get('Duration'));
				if ($duration < 1)
				{
					throw new \Exception('Duration must be 1 day at least');
				}
				$now = new \DateTime();
				$lot->setStartDate($now);
				$diff = new \DateInterval("P" . strval($duration) . "D");
				$end = new \DateTime();
				$end->add($diff);
				$lot->setEndDate($end);
				
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
			'found' => true,
			'duration' => $duration
        ));
    }
	
	/**
     * @Route("/lot/{id}/edit", name="edit-lot")
     */
    public function editLotAction(Request $request, $id)
    {
		if( !$this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') )
		{
			return $this->redirectToRoute('login');
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
			$duration = 0;
			$cat = null;
			$properties = [];
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
			$duration = date_diff($lot->getStartDate(), $lot->getEndDate())->days;
			
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
						if ($lot->getBuyoutPrice() < $lot->getStartPrice())
						{
							throw new \Exception('Buyout price cannot be lower than the starting price.');
						}
					}
					else
					{
						$lot->setBuyoutPrice(null);
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
					$duration = intval($request->request->get('Duration'));
					if ($duration < 1)
					{
						throw new \Exception('Duration must be 1 day at least');
					}
					$now = new \DateTime();
					$lot->setStartDate($now);
					$diff = new \DateInterval("P" . strval($duration) . "D");
					$end = new \DateTime();
					$end->add($diff);
					$lot->setEndDate($end);
				
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
			'id' => $id,
			'duration' => $duration
        ));
    }
	
	/**
     * @Route("/make_lot_choose", name="make-lot")
     */
    public function makeLotChooseAction(Request $request)
    {
		if( !$this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') )
		{
			return $this->redirectToRoute('login');
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
		if( !$this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') )
		{
			return $this->redirectToRoute('login');
		}
		$em = $this->getDoctrine()->getManager();
		$lot = $this->getDoctrine()->getRepository('AppBundle:Lot')->findOneById($id);
		$em->remove($lot);
		$em->flush();
        return $this->redirectToRoute('home');
    }
	
	/**
     * @Route("/lots/{id}/confirm", name="confirm-lot")
     */
    public function confirmLotAction(Request $request, $id)
    {
		if( !$this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') )
		{
			return $this->redirectToRoute('login');
		}
		$em = $this->getDoctrine()->getManager();
		$lot = $this->getDoctrine()->getRepository('AppBundle:Lot')->findOneById($id);
		$lot->setStatus("Open");
		$diff = date_diff($lot->getStartDate(), $lot->getEndDate());
		$now = new \DateTime();
		$lot->setStartDate($now);
		$now = $now->add($diff);
		$lot->setEndDate($now);
		$em->persist($lot);
		$em->flush();
        return $this->redirectToRoute('lot', array('id'=>$id));
    }

	/**
     * @Route("/lots/{id}", name="lot")
     */
    public function lotAction(Request $request, $id)
    {
		$lot = $this->getDoctrine()->getRepository('AppBundle:Lot')->findOneById($id);
		$error = '';
		$price = 0;
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
			$lot->getCurrentPrice();
			$duration = date_diff($lot->getStartDate(), $lot->getEndDate())->days;
		}
		else
		{
			$found = false;
			$author = null;
			$properties = [];
			$duration = 0;
		}
		if('POST' === $request->getMethod())
		{
			if ($request->request->has('Bid'))
			{
				try
				{
					$em = $this->getDoctrine()->getManager();
					$now = new \DateTime();
					if ($now > $lot->getEndDate())
					{
						$lot->setStatus('Finished');
						$em->persist($lot);
						$em->flush();
						throw new \Exception('This lot is already closed');
					}
					$v = $request->request->get('Bid_value');
					$val = floatval($v);
					if ($val < $lot->getCurrentPrice()*1.02)
					{
						throw new \Exception('Your bid must be higher than the current price by 2% at least.');
					}
					$bid = new Bid();
					$bid->setValue($val);
					$bid->setLot($lot);
					$bid->setUser($this->get('security.token_storage')->getToken()->getUser());
					$bid->setDate(new \DateTime());
					$em->persist($bid);
					$em->persist($lot);
					$em->flush();
					$price = $val;
				}
				catch (\Exception $e)
				{
					$error = $e->getMessage();
				}
			}
			else if ($request->request->has('Buyout'))
			{
				try
				{
					$em = $this->getDoctrine()->getManager();
					$now = new \DateTime();
					if ($now > $lot->getEndDate())
					{
						$lot->setStatus('Finished');
						$em->persist($lot);
						$em->flush();
						throw new \Exception('This lot is already closed');
					}
					$bid = new Bid();
					$bid->setValue($lot->getBuyoutPrice());
					$bid->setLot($lot);
					$bid->setUser($this->get('security.token_storage')->getToken()->getUser());
					$bid->setDate(new \DateTime());
					$lot->setEndDate(new \DateTime());
					$lot->setStatus('Finished');
					$lot->setCurrentPrice($lot->getBuyoutPrice());
					$em->persist($bid);
					$em->persist($lot);
					$em->flush();
					$price = $lot->getBuyoutPrice();
				}
				catch (\Exception $e)
				{
					$error = $e->getMessage();
				}
			}
		}
        return $this->render('lot.html.twig', array(
			"found" => $found,
			"lot" => $lot,
			'properties' => $properties,
			'author' => $author,
			'error' => $error,
			'price' => $price,
			'duration' => $duration
        ));
    }
}
?>