<?php

namespace RecipeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/resp")
     */
    public function indexAction()
    {
        return $this->render('RecipeBundle:Default:index.html.twig');
    }
}
