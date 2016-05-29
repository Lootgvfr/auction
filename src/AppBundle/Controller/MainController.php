<?php

namespace AppBundle\Controller;

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
		if (!$cm)
		{
			$query = $rep->createQueryBuilder('l')
				->where('l.endDate > :date')
				->andWhere('l.category = :cat')
				->andWhere('l.status != \'Unconfirmed\'')
				->setParameter('date', $date)
				->setParameter('cat', $cat)
				->orderBy('l.startDate', 'DESC')
				->orderBy('l.status', 'DESC')
				->getQuery();
		}
		else
		{
			$query = $rep->createQueryBuilder('l')
				->where('l.endDate > :date')
				->andWhere('l.category = :cat')
				->setParameter('date', $date)
				->setParameter('cat', $cat)
				->orderBy('l.startDate', 'DESC')
				->orderBy('l.status', 'DESC')
				->getQuery();
		}
		
		$lots = $query->getResult();
		foreach($lots as $lot)
		{
			$lot->getCurrentPrice();
		}
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