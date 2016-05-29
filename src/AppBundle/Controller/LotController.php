<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Property;
use AppBundle\Entity\Value;
use AppBundle\Entity\Lot;
use AppBundle\Entity\Bid;
use AppBundle\Entity\CommentLot;
use AppBundle\Form\CommentLotForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LotController extends Controller
{
	private function prs($string)
	{
		return str_replace(" ", "_", $string);
	}

	/**
     * @Route("/make_lot/{category}", name="make-lot-cat")
	 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function makeLotAction(Request $request, $category)
    {
		$name = '';
		$description = '';
		$starting_price = '';
		$buyout_price = '';
		$duration = 0;
		$cat = $this->getDoctrine()->getRepository('AppBundle:Category')->findOneByName($category);
		$cur = '';
		$props = $cat->getProperties();
		
		$properties = [];
		$errors = [];
		$errors['general'] = '';
		$errors['name'] = '';
		$errors['duration'] = '';
		$errors['starting_price'] = '';
		$errors['buyout_price'] = '';
		$errors['currency'] = '';
		$currencies = $this->getDoctrine()->getRepository('AppBundle:Currency')->findAll();
		for ($i = 0; $i < count($props); $i++)
		{
			array_push($properties, array(
				'name' => $props[$i]->getName(),
				'und_name' => $this->prs($props[$i]->getName()),
				'value' => '',
				'error' => '',
				'example' => $props[$i]->getExample(),
				'regulars' => $props[$i]->getRegulars(),
				'is_nullable' => $props[$i]->getIsNullable()
				));
		}
		
		if('POST' === $request->getMethod())
		{
			try
			{
				$ok = true;
				$em = $this->getDoctrine()->getManager();
				
				// get entered properties from request
				for ($i = 0; $i < count($properties); $i++)
				{
					$str = $request->request->get($properties[$i]['und_name']);
					$properties[$i]['value'] = $str;
				}
				
				// get entered fields from request
				$name = $request->request->get('Name');
				$description = $request->request->get('Description');
				$starting_price = $request->request->get('Starting_price');
				$buyout_price = $request->request->get('Buyout_price');
				$cur = $request->request->get('Currency');
				
				// check duration
				try
				{ 
					$dur = $request->request->get('Duration');
					if ($dur == '')
					{
						throw new \Exception('This field is required');
					}
					$duration = intval($dur);
					if ($duration < 3)
					{
						throw new \Exception('Duration must be 3 days at least');
					}
				}
				catch (\Exception $e)
				{ 
					$errors['duration'] = $e->getMessage();
					$ok = false;
				}
				
				// check name for being empty
				if($name == '')
				{
					$errors['name'] = 'This field is required';
					$ok = false;
				}
				
				// check currency
				if($cur == '' or $cur == 'None')
				{
					$errors['currency'] = 'This field is required';
					$ok = false;
				}
				
				// check starting price
				if($starting_price == '')
				{
					$errors['starting_price'] = 'This field is required';
					$ok = false;
				}
				else
				{
					try
					{ 
						$strp = floatval($starting_price);
						if ($strp == 0)
						{
							throw new \Exception('Invalid input');
						}
						if ($strp < 0)
						{
							throw new \Exception('Can not be negative');
						}
					}
					catch (\Exception $e)
					{ 
						$errors['starting_price'] = $e->getMessage();
						$ok = false;
					}
				}
				
				// check buyout price
				try
				{ 
					if ($buyout_price != '')
					{
						$bp = floatval($buyout_price);
						if ($bp == 0)
						{
							throw new \Exception('Invalid input');
						}
						if ($bp < 0)
						{
							throw new \Exception('Can not be negative');
						}
						if ($bp < $strp)
						{
							throw new \Exception('Buyout price cannot be lower than the starting price');
						}
					}
				}
				catch (\Exception $e)
				{ 
					$errors['buyout_price'] = $e->getMessage();
					$ok = false;
				}
				
				// check properties
				for ($i = 0; $i < count($properties); $i++)
				{
					if ($properties[$i]['value'] == '')
					{
						if (!$properties[$i]['is_nullable'])
						{
							$properties[$i]['error'] = 'This field is required';
							$ok = false;
						}
					}
					else
					{
						$correct = false;
						foreach ($properties[$i]['regulars'] as $regular)
						{
							$reg = $regular->getExp();
							if (preg_match($reg, $properties[$i]['value']))
							{
								$correct = true;
							}
						}
						if (!$correct and count($properties[$i]['regulars']) > 0)
						{
							$properties[$i]['error'] = 'Invalid input (example: ' . $properties[$i]['example'] . ')';
							$ok = false;
						}
					}
				}
				
				// create lot
				if ($ok)
				{
					$lot = new Lot();
					$lot->setName($name);
					$lot->setDescription($description);
					$lot->setStartPrice($strp);
					if ($buyout_price != '')
					{
						$lot->setBuyoutPrice($bp);
					}
					$lot->setStatus('Unconfirmed');
					$lot->setAuthor($this->get('security.token_storage')->getToken()->getUser());
					$lot->setCategory($cat);
					
					$currency = $this->getDoctrine()->getRepository('AppBundle:Currency')->findOneByName($cur);
					$lot->setCurrency($currency);
					
					// get and upload file
					$data = $this->getRequest()->request->all();
					$file = $this->getRequest()->files->get('file');
					if ($file)
					{
						$lot->setFile($file);
						$lot->upload();
					}
					
					// save duration
					$now = new \DateTime();
					$lot->setStartDate($now);
					$diff = new \DateInterval("P" . strval($duration) . "D");
					$end = new \DateTime();
					$end->add($diff);
					$lot->setEndDate($end);
					
					// save properties
					for ($i = 0; $i < count($properties); $i++)
					{
						$val = new Value();
						$val->setVal($properties[$i]['value']);
						$val->setProperty($props[$i]);
						$val->setLot($lot);
						$em->persist($val);
					}
					
					$em->persist($lot);
					$em->flush();
					return $this->redirectToRoute('lot', array('id' => $lot->getId()));
				}
			}
			catch (\Exception $e)
			{
				$errors['general'] = $e->getMessage();
			}
		}
		
        return $this->render('make-lot.html.twig', array(
			"category" => $cat,
			"properties" => $properties,
			"name" => $name,
			'description' => $description,
			'starting_price' => $starting_price,
			'buyout_price' => $buyout_price,
			'edit' => false,
			'found' => true,
			'duration' => $duration,
			'errors' => $errors,
			'currencies' => $currencies,
			'cur' => $cur
        ));
    }
	
	/**
     * @Route("/lot/{id}/edit", name="edit-lot")
	 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function editLotAction(Request $request, $id)
    {
		$user = $this->get('security.token_storage')->getToken()->getUser();
		$lot = $this->getDoctrine()->getRepository('AppBundle:Lot')->findOneById($id);
		if (! ($user->getGroup() == 'Admin' or $user->getGroup() == 'Content-manager' or $user == $lot->getAuthor()))
		{
			$this->redirectToRoute('login');
		}
		$currencies = $this->getDoctrine()->getRepository('AppBundle:Currency')->findAll();
		$found = false;
		if (!$lot)
		{
			$errors = [];
			$errors['general'] = '';
			$errors['name'] = '';
			$errors['duration'] = '';
			$errors['starting_price'] = '';
			$errors['buyout_price'] = '';
			$errors['currency'] = '';
			$name = '';
			$description = '';
			$starting_price = '';
			$buyout_price = '';
			$duration = 0;
			$cur = '';
			$cat = null;
			$properties = [];
		}
		else
		{
			$found = true;
			$errors = [];
			$errors['general'] = '';
			$errors['name'] = '';
			$errors['duration'] = '';
			$errors['starting_price'] = '';
			$errors['buyout_price'] = '';
			$errors['currency'] = '';
			$name = $lot->getName();
			$description = $lot->getDescription();
			$starting_price = $lot->getStartPrice();
			$buyout_price = $lot->getBuyoutPrice();
			$cat = $lot->getCategory();
			$cur = $lot->getCurrency()->getName();
			$vals = $lot->getValues();
			$duration = intval(date_diff($lot->getStartDate(), $lot->getEndDate())->days);
			
			$properties = [];
			for ($i = 0; $i < count($vals); $i++)
			{
				array_push($properties, array(
					'name' => $vals[$i]->getProperty()->getName(),
					'und_name' => $this->prs($vals[$i]->getProperty()->getName()),
					'value' => $vals[$i]->getVal(),
					'error' => '',
					'example' => $vals[$i]->getProperty()->getExample(),
					'regulars' => $vals[$i]->getProperty()->getRegulars(),
					'is_nullable' => $vals[$i]->getProperty()->getIsNullable()
					));
			}
			
			if('POST' === $request->getMethod())
			{
				try
				{
					$ok = true;
					$em = $this->getDoctrine()->getManager();
					
					// get entered properties from request
					for ($i = 0; $i < count($properties); $i++)
					{
						$str = $request->request->get($properties[$i]['und_name']);
						$properties[$i]['value'] = $str;
					}
					
					// get entered fields from request
					$name = $request->request->get('Name');
					$description = $request->request->get('Description');
					$starting_price = $request->request->get('Starting_price');
					$buyout_price = $request->request->get('Buyout_price');
					$cur = $request->request->get('Currency');
					
					// check duration
					try
					{ 
						$dur = $request->request->get('Duration');
						if ($dur == '')
						{
							throw new \Exception('This field is required');
						}
						$duration = intval($dur);
						if ($duration < 3)
						{
							throw new \Exception('Duration must be 3 days at least');
						}
					}
					catch (\Exception $e)
					{ 
						$errors['duration'] = $e->getMessage();
						$ok = false;
					}
					
					// check name for being empty
					if($name == '')
					{
						$errors['name'] = 'This field is required';
						$ok = false;
					}
					
					// check currency
					if($cur == '' or $cur == 'None')
					{
						$errors['currency'] = 'This field is required';
						$ok = false;
					}
					
					// check starting price
					if($starting_price == '')
					{
						$errors['starting_price'] = 'This field is required';
						$ok = false;
					}
					else
					{
						try
						{ 
							$strp = floatval($starting_price);
							if ($strp == 0)
							{
								throw new \Exception('Invalid input');
							}
							if ($strp < 0)
							{
								throw new \Exception('Can not be negative');
							}
						}
						catch (\Exception $e)
						{ 
							$errors['starting_price'] = $e->getMessage();
							$ok = false;
						}
					}
					
					// check buyout price
					try
					{ 
						if ($buyout_price != '')
						{
							$bp = floatval($buyout_price);
							if ($bp == 0)
							{
								throw new \Exception('Invalid input');
							}
							if ($bp < 0)
							{
								throw new \Exception('Can not be negative');
							}
							if ($bp < $strp)
							{
								throw new \Exception('Buyout price cannot be lower than the starting price');
							}
						}
					}
					catch (\Exception $e)
					{ 
						$errors['buyout_price'] = $e->getMessage();
						$ok = false;
					}
					
					// check properties
					for ($i = 0; $i < count($properties); $i++)
					{
						if ($properties[$i]['value'] == '')
						{
							if (!$properties[$i]['is_nullable'])
							{
								$properties[$i]['error'] = 'This field is required';
								$ok = false;
							}
						}
						else
						{
							$correct = false;
							foreach ($properties[$i]['regulars'] as $regular)
							{
								$reg = $regular->getExp();
								if (preg_match($reg, $properties[$i]['value']))
								{
									$correct = true;
								}
							}
							if (!$correct and count($properties[$i]['regulars']) > 0)
							{
								$properties[$i]['error'] = 'Invalid input (example: ' . $properties[$i]['example'] . ')';
								$ok = false;
							}
						}
					}
					
					// save lot
					if ($ok)
					{
						$lot->setName($name);
						$lot->setDescription($description);
						$lot->setStartPrice($strp);
						if ($buyout_price != '')
						{
							$lot->setBuyoutPrice($bp);
						}
						else
						{
							$lot->setBuyoutPrice(null);
						}
						$currency = $this->getDoctrine()->getRepository('AppBundle:Currency')->findOneByName($cur);
						$lot->setCurrency($currency);
						
						// get and upload file
						$data = $this->getRequest()->request->all();
						$file = $this->getRequest()->files->get('file');
						if ($file)
						{
							$lot->setFile($file);
							$lot->upload();
						}
						
						// save duration
						$now = new \DateTime();
						$lot->setStartDate($now);
						$diff = new \DateInterval("P" . strval($duration) . "D");
						$end = new \DateTime();
						$end->add($diff);
						$lot->setEndDate($end);
						
						// save properties
						for ($i = 0; $i < count($properties); $i++)
						{
							$vals[$i]->setVal($properties[$i]['value']);
							$em->persist($vals[$i]);
						}
						
						$em->persist($lot);
						$em->flush();
						return $this->redirectToRoute('lot', array('id' => $lot->getId()));
					}
				}
				catch (\Exception $e)
				{
					$errors['general'] = $e->getMessage();
				}
			}
		}
		
        return $this->render('make-lot.html.twig', array(
			"category" => $cat,
			"properties" => $properties,
			"errors" => $errors,
			"name" => $name,
			'description' => $description,
			'starting_price' => $starting_price,
			'buyout_price' => $buyout_price,
			'edit' => true,
			'found' => $found,
			'id' => $id,
			'duration' => $duration,
			'currencies' => $currencies,
			'cur' => $cur
        ));
    }
	
	/**
     * @Route("/make_lot_choose", name="make-lot")
	 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
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
	 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function deleteLotAction(Request $request, $id)
    {
		$user = $this->get('security.token_storage')->getToken()->getUser();
		$em = $this->getDoctrine()->getManager();
		$lot = $this->getDoctrine()->getRepository('AppBundle:Lot')->findOneById($id);
		if (! ($user->getGroup() == 'Admin' or $user->getGroup() == 'Content-manager' or $user == $lot->getAuthor()))
		{
			$this->redirectToRoute('login');
		}
		$em->remove($lot);
		$em->flush();
        return $this->redirectToRoute('home');
    }
	
	/**
     * @Route("/check_lots", name="check-lots")
	 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function checkLotsAction(Request $request)
    {
		$user = $this->get('security.token_storage')->getToken()->getUser();
		if (!($user->getGroup() == 'Admin' or $user->getGroup() == 'Content-manager'))
		{
			$this->redirectToRoute('login');
		}
		$em = $this->getDoctrine()->getManager();
		$now = new \DateTime();
		$rep = $this->getDoctrine()->getRepository('AppBundle:Lot');
		$query = $rep->createQueryBuilder('l')
			->where('l.endDate < :now')
			->setParameter('now', $now)
			->getQuery();
		
		$lots = $query->getResult();
		foreach($lots as $lot)
		{
			$lot->setStatus('Finished');
			$em->persist($lot);
		}
		$em->flush();
        return $this->redirectToRoute('home');
    }
	
	/**
     * @Route("/lots/{id}/confirm", name="confirm-lot")
	 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function confirmLotAction(Request $request, $id)
    {	
		$em = $this->getDoctrine()->getManager();
		$lot = $this->getDoctrine()->getRepository('AppBundle:Lot')->findOneById($id);
		$user = $this->get('security.token_storage')->getToken()->getUser();
		if (!($user->getGroup() == 'Admin' or $user->getGroup() == 'Content-manager'))
		{
			$this->redirectToRoute('login');
		}
		$lot->setStatus("Open");
		$diff = date_diff($lot->getStartDate(), $lot->getEndDate());
		$now = new \DateTime();
		$lot->setStartDate($now);
		$next = clone $now;
		$next = $next->add($diff);
		$lot->setEndDate($next);
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
			if ($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY'))
			{
				$user = $this->get('security.token_storage')->getToken()->getUser();
				if ($user != $lot->getAuthor())
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
							if ($lot->getBuyoutPrice())
							{
								if ($val >= $lot->getBuyoutPrice())
								{
									throw new \Exception('Your bid can\'t be higher than the buyout price.');
								}
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
				else
				{
					$error = 'You can not buy your own lot';
				}
			}
			else
			{
				$error = 'You have to be logged in to buy a lot';
			}
		}
		
        return $this->render('lot.html.twig', array(
			"found" => $found,
			"lot" => $lot,
			'properties' => $properties,
			'author' => $author,
			'error' => $error,
			'price' => $price,
			'duration' => $duration,
			'id' => $id
        ));
		
    }
	
	
	
	/**
     * @Route("/comment/{lot_id}/lot_new", name = "lot_comment_new")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     *
     * @Method("POST")
     * @ParamConverter("lot", options={"mapping": {"lot_id": "id"}})
     */
	public function commentNewAction(Request $request, Lot $lot)
    {
        $form = $this->createForm(new CommentLotForm());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Comment $comment */
            $comment = $form->getData();
            $comment->setAuthor($this->getUser());
			$comment->setLot($lot);
			$comment->setStatus("unconfirmed");
			$date = new \DateTime(); //->format('Y-m-d H:i:s')
			$comment->setDate($date);
			$comment->setRating(5);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('lot', array('id' => $lot->getId()));
        }

        $form = $this->createForm(new CommentLotForm());

        return $this->render('_lot_comment.html.twig', array(
            'lot' => $lot,
            'form' => $form->createView()
        ));
    }

/**
     *
     * @param Post $post
     *
     * @return Response
     */
    public function commentLotFormAction(Lot $lot)
    {
        $form = $this->createForm(new CommentLotForm());

        return $this->render('_lot_comment.html.twig', array(
            'lot' => $lot,
            'form' => $form->createView()
        ));
    }
	
	
	
	
}
?>