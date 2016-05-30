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
		$recommended = array_slice($lots, 0, 4);
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
			if ($user->getGroup() == 'Admin' or $user->getGroup() == 'Content-manager')
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
						and p.endDate > :date
						and p.category = :cat'
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
						and p.category = :cat'
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
}
?>