<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SignController extends Controller
{
    /**
     * @Route("/sign", name="sign")
     */
    public function homeAction(Request $request)
    {
        return $this->render('sign.html.twig', array(
        ));
    }
	
}
?>