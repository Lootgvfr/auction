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
		$rep = $this->getDoctrine()->getRepository('AppBundle:Lot');
		$query = $rep->createQueryBuilder('l')
				->where('l.status = :open')
				->setParameter('open', 'open')
				->orderBy('l.startDate', 'DESC')
				->getQuery();
		$lots = $query->getResult();
		$recent = array_slice($lots, 0, 4);
		$user = null;
		if ($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY'))
		{
			$user = $this->get('security.token_storage')->getToken()->getUser();
		}
		$recommended = $this->getRecommendedLots($lots, $user);
        return $this->render('home.html.twig', array(
			'recent' => $recent,
			'recommended' => $recommended
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
     * @Route("/categories/{name}/{page}", name="category_display", defaults={"page" = 1}, requirements={"page" : "\d+"})
     */
    public function categoryAction(Request $request, $name, $page)
    {
		$cat = $this->getDoctrine()->getRepository('AppBundle:Category')->findOneByName($name);
		$rep = $this->getDoctrine()->getRepository('AppBundle:Lot');
		$date = new \DateTime();
		$date->sub(new \DateInterval('P7D'));
		$cm = false;
		if ($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY'))
		{
			$user = $this->get('security.token_storage')->getToken()->getUser();
			if ($user->getGroup() == 'Admin' or $user->getGroup() == 'Manager')
			{
				$cm = true;
			}
		}
		
		$em = $this->getDoctrine()->getManager();
		
		$per_page = 10;   
		if (!$cm)
		{
			$pages_query = $em->createQuery(
						'SELECT COUNT(p.id)
						FROM AppBundle:Lot p
						WHERE p.status != :status
						and p.endDate > :date
						and p.category = :cat'
					)->setParameter('status', 'unconfirmed')
					->setParameter('date', $date)
					->setParameter('cat', $cat);
		}
		else {
			$pages_query = $em->createQuery(
						'SELECT COUNT(p.id)
						FROM AppBundle:Lot p
						WHERE p.endDate > :date
						and p.category = :cat'
					)
					->setParameter('date', $date)
					->setParameter('cat', $cat);
		}
		$pages = $pages_query->getResult();
		
		$pages = ceil($pages[0][1] / $per_page);  
		$start = ($page - 1) * $per_page; 
		
		if ($pages > 5)
		{
			if ($page > 3)
			{
				if ($page < $page - 2)
				{
					$start_pag = $pages - 2;
					$end_pag = $pages + 2;
				}
				else if ($page > $page - 2)
				{
					$start_pag = $pages - 4;
					$end_pag = $pages;
				}
			}
			else {
				$start_pag = 1;
				$end_pag = 5;
			}
		}
		else
		{
			$start_pag = 1;
			$end_pag = $pages;
		}

		if (!$cm)
		{
			$query = $em->createQuery(
						'SELECT p
						FROM AppBundle:Lot p
						WHERE p.status != :status
						AND p.endDate > :date
						AND p.category = :cat
						ORDER BY p.status DESC, p.startDate DESC'
					)->setParameter('status', 'unconfirmed')
					->setParameter('date', $date)
					->setParameter('cat', $cat)
					->setFirstResult($start)
					->setMaxResults($per_page);
		}
		else {
			$query = $em->createQuery(
						'SELECT p
						FROM AppBundle:Lot p
						WHERE p.endDate > :date
						AND p.category = :cat
						ORDER BY p.status DESC, p.startDate DESC'
					)
					->setParameter('date', $date)
					->setParameter('cat', $cat)
					->setFirstResult($start)
					->setMaxResults($per_page);
		}
		

		$lots = $query->getResult();

		foreach($lots as $lot)
		{
			$lot->getCurrentPrice();
		}
        return $this->render('category_display.html.twig', array(
			"lots" => $lots,
			"name" => $name,
			'start_pag' => $start_pag,
			'end_pag' => $end_pag,
			'page' => $page,
			'pages' => $pages
        ));
    }
	
	public function categoriesAction()
	{
		$categories = $this->getDoctrine()->getRepository('AppBundle:Category')->findAll();
		return $this->render('categories.html.twig', array(
			"categories" => $categories
        ));
	}
	
	private function getRecommendedLots($lots, $user)
	{
		if ($user == null)
		{
			usort($lots, function($a, $b)
			{
				if($a->getRating() == $b->getRating())
					return 0;
				return $a->getRating() < $b->getRating()?1:-1;
			});
			$rec = array_slice($lots, 0, 4);
			return $rec;
		}
		else
		{
			$recs = [];
			foreach ($lots as $lot)
			{
				$recs[strval($lot->getId())] = [$lot, 0];
			}
			$userBids = $user->getBids();
			$prop_rep = $this->getDoctrine()->getRepository('AppBundle:Property');
			$em = $this->getDoctrine()->getManager();
			$users = [];
			foreach($userBids as $userBid)
			{
				$lot = $userBid->getLot();
				$cat = $lot->getCategory();
				$producer = '';
				foreach($lot->getValues() as $value)
				{
					if ($value->getProperty()->getName() == 'Producer')
					{
						$producer = $value->getVal();
						break;
					}
				}
				foreach($lots as $lt)
				{
					$prod = '';
					foreach($lt->getValues() as $value)
					{
						if ($value->getProperty()->getName() == 'Producer')
						{
							$prod = $value->getVal();
							break;
						}
					}
					if ($lt->getCategory() != $cat and $prod == $producer)
					{
						$recs[strval($lt->getId())][1] += 1;
					}
				}
				foreach($lot->getBids() as $bid)
				{
					$us = $bid->getUser();
					if ($us != $user)
					{
						array_push($users, $us);
					}
				}
			}
			array_unique($users);
			foreach($users as $us)
			{
				$bids = $us->getBids();
				$lts = [];
				foreach($bids as $bid)
				{
					array_push($lts, $bid->getLot());
				}
				array_unique($lts);
				foreach($lts as $lt)
				{
					if($lt->getStatus() == 'Open')
					{
						$recs[strval($lt->getId())][1] += 2;
					}
				}
			}
			usort($recs, function($a, $b)
			{
				if($a[1] == $b[1])
					return 0;
				return $a[1] < $b[1]?1:-1;
			});
			$rec = [];
			for ($i = 0; $i < 4; $i++)
			{
				array_push($rec, $recs[$i][0]);
			}
			return $rec;
		}
	}
}
?>