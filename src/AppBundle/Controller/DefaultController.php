<?php

namespace AppBundle\Controller;

use ProductBundle\Entity\Product;
use RecipeBundle\Entity\Recipe;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="index_page_nolocale", defaults={"_locale":"%locale%"})
     * @Route("/{_locale}/", name="index_page", requirements={"_locale" = "%app.locales%"})
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository(Product::class)->createQueryBuilder('p');
            $queryBuilder->setMaxResults(4);
        $products = $queryBuilder->getQuery()->getResult();

        $queryBuilder = $em->getRepository(Recipe::class)->createQueryBuilder('r');
        $queryBuilder->setMaxResults(3);
        $recipes = $queryBuilder->getQuery()->getResult();

        return $this->render('default/index.html.twig', array(
            'products' => $products,
            'recipes' => $recipes,
        ));
    }

    /**
     * @Route("/produits", name="index_products_page")
     */
    public function productAction(Request $request)
    {

    }
}
