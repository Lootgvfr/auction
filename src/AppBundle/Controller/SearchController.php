<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contact;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends Controller
{
    /**
     * @Route("/search/{page}", name="search", defaults={"page" = 1}, requirements={"page" : "\d+"})
     */
    public function homeAction(Request $request, $page)
    {
        if (!isset ($_GET['parameters']))
            return $this->redirectToRoute("home");
        else{
            $parameter = $_GET['parameters'];
            $em = $this->getDoctrine()->getManager();
            $per_page = 10;
            $date = new \DateTime();
            $date->sub(new \DateInterval('P7D'));
            $pages_query = $em->createQuery(
                'SELECT COUNT(p.id)
						FROM AppBundle:Lot p
						WHERE p.status != :status
						and (p.name LIKE :parameter
						or p.description LIKE :parameter)
						and p.endDate > :date'
            )->setParameter('status', 'unconfirmed')
                ->setParameter('date', $date)
                ->setParameter('parameter', $parameter)

            ;
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

            $query = $em->createQuery(
                'SELECT p
						FROM AppBundle:Lot p
						WHERE p.status != :status
						and (p.name LIKE :parameter
						or p.description LIKE :parameter)
						and p.endDate > :date'
            )->setParameter('status', 'unconfirmed')
                ->setParameter('date', $date)
                ->setParameter('parameter', '%'.$parameter.'%')
                ->setFirstResult($start)
                ->setMaxResults($per_page);

            $lots = $query->getResult();
            return $this->render('category_display.html.twig', array(
                "lots" => $lots,
                "name" => $parameter,
                'start_pag' => $start_pag,
                'end_pag' => $end_pag,
                'page' => $page,
                'pages' => $pages
            ));
        
            
        }
        
    }
	
	
}
?>