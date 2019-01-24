<?php

namespace AppBundle\Controller;

use AppBundle\Form\MessageType;
use MessageBundle\Entity\Message;
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
    public function indexAction()
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
     * @Route("/{_locale}/produits", name="index_products_page_l", requirements={"_locale" = "%app.locales%"})
     */
    public function productAction()
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository(Product::class)->findBy(array('enabled' => true));
        return $this->render('default/list_products.html.twig', array(
           'products' => $products,
        ));
    }

    /**
     * @Route("/{_locale}/produits/{id}", name="product_details_page_l", requirements={"_locale" = "%app.locales%"})
     */
    public function productDetailsAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository(Product::class)->find($id);
        return $this->render('default/detail_product.html.twig', array(
            'product' => $product,
        ));
    }

    /**
     * @Route("/{_locale}/recettes", name="index_recipes_page_l", requirements={"_locale" = "%app.locales%"})
     */
    public function recipeAction()
    {
        $recipes = $this->getDoctrine()->getManager()->getRepository(Recipe::class)->findBy(array('enabled' => true));
        return $this->render('default/list_recipes.html.twig', array(
            'recipes' => $recipes
        ));
    }

    /**
     * @Route("/{_locale}/recettes/{id}", name="recipe_details_page_l", requirements={"_locale" = "%app.locales%"})
     */
    public function recipeDetailsAction($id)
    {
        $recipe = $this->getDoctrine()->getManager()->getRepository(Recipe::class)->find($id);
        return $this->render('default/detail_recipe.html.twig', array(
            'recipe' => $recipe
        ));
    }

    /**
     * @Route("/{_locale}/contact", name="contact_page_l", requirements={"_locale" = "%app.locales%"})
     */
    public function contactAction(Request $request)
    {
        $message = new Message();
        $message->setEnabled(true);
        $form = $this->get('form.factory')->create(MessageType::class, $message);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();
            return $this->redirectToRoute('contact_page_l');
        }
        return $this->render('default/contact.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
