<?php

namespace OrderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/orders", name="list_commandes_page")
     */
    public function indexAction()
    {
        return $this->render('admin/commandes:list.html.twig');
    }
}
