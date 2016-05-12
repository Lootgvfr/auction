<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class LotController extends Controller
{
	/**
     * @Route("/make_lot", name="make-lot")
     */
    public function makeLotAction(Request $request)
    {
        return $this->render('make-lot.html.twig', array(
        ));
    }

	/**
     * @Route("/lots/{name}", name="lot")
     */
    public function lotAction(Request $request, $name)
    {
		$found = false;
		$lot = [];
		for ($i = 0; $i < count($this::$lots); $i++)
		{
			if ($this::$lots[$i]["name"] == $name)
			{
				$lot = $this::$lots[$i];
				$found = true;
			}
		}
        return $this->render('lot.html.twig', array(
			"found" => $found,
			"lot" => $lot
        ));
    }
}
?>