<?php

namespace ProductBundle\Controller;

use ProductBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/products", name="list_products_page")
     */
    public function indexAction()
    {
        $products = $this->getDoctrine()->getManager()->getRepository(Product::class)->findBy(array('enabled' => true));
        return $this->render('admin/products/list.html.twig', array(
            'products' => $products,
        ));
    }


}
