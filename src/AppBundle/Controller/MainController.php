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
			if ($user->getGroup() == 'Admin' or $user->getGroup() == 'Manager')
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