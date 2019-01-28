<?php

namespace AppBundle\Controller;

use MessageBundle\Form\MessageType;
use MessageBundle\Entity\Message;
use OrderBundle\Entity\Devis;
use OrderBundle\Entity\DevisItem;
use OrderBundle\Entity\OrderInfo;
use OrderBundle\Form\CommandeItemEditTypeClient;
use OrderBundle\Form\DevisType;
use OrderBundle\Form\PersonalInfoType;
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
            $queryBuilder->setMaxResults(3);
        $products = $queryBuilder->getQuery()->getResult();

        $queryBuilder = $em->getRepository(Recipe::class)->createQueryBuilder('r');
        $queryBuilder->setMaxResults(3);
        $recipes = $queryBuilder->getQuery()->getResult();

        $serializer = $this->get('jms_serializer');
        $session = $this->get('session');
        $cartLogo = 0;
        if ($session->has('cartElements')) {
            $commandeJson = $session->get('cartElements');
            $commande = $serializer->deserialize($commandeJson, Devis::class, 'json');
            $cartLogo = count($commande->getItems());
        }

        return $this->render('default/index.html.twig', array(
            'products' => $products,
            'recipes' => $recipes,
            'cartLogo' => $cartLogo,
        ));
    }

    /**
     * @Route("/{_locale}/produits", name="index_products_page_l", requirements={"_locale" = "%app.locales%"})
     */
    public function productAction()
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository(Product::class)->findBy(array('enabled' => true));

        $serializer = $this->get('jms_serializer');
        $session = $this->get('session');
        $cartLogo = 0;
        if ($session->has('cartElements')) {
            $commandeJson = $session->get('cartElements');
            $commande = $serializer->deserialize($commandeJson, Devis::class, 'json');
            $cartLogo = count($commande->getItems());
        }

        return $this->render('default/list_products.html.twig', array(
            'products' => $products,
            'cartLogo' => $cartLogo,
        ));
    }

    /**
     * @Route("/{_locale}/produits/{id}", name="product_details_page_l", requirements={"_locale" = "%app.locales%"})
     */
    public function productDetailsAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository(Product::class)->find($id);

        $serializer = $this->get('jms_serializer');
        $session = $this->get('session');
        $cartLogo = 0;
        if ($session->has('cartElements')) {
            $commandeJson = $session->get('cartElements');
            $commande = $serializer->deserialize($commandeJson, Devis::class, 'json');
            $cartLogo = count($commande->getItems());
        }

        $devisItem = new DevisItem();
        $cart_form = $this->get('form.factory')->create(CommandeItemEditTypeClient::class, $devisItem);
        if($request->isMethod('POST') && $cart_form->handleRequest($request)->isValid()) {

            $devisItem->setProduct($product);
            $session = $this->get('session');
            $em = $this->getDoctrine()->getManager();

            if ($session->has('cartElements')) {
                //add to existing commande
                $commandeJson = $session->get('cartElements');
                $commande = $serializer->deserialize($commandeJson, Devis::class, 'json');
                $database_commande = $this->getDoctrine()->getManager()->getRepository(Devis::class)->find($commande->getId());
                $database_commande->addItem($devisItem);
                $devisItem->setDevis($database_commande);
                $em->persist($database_commande);
                $em->flush();
                $jsonCommande = $serializer->serialize($database_commande, 'json');
                //die(var_dump($jsonCommande));
                $session->set('cartElements', $jsonCommande);
            }
            else{
                $commande = new Devis();
                $commande->setEnabled(false);
                $commande->setSaleDate(new \DateTime());
                $em->persist($commande);
                $em->flush();
                $commande->addItem($devisItem);
                $devisItem->setDevis($commande);
                $em->persist($devisItem);
                $em->flush();
                $jsonCommande = $serializer->serialize($commande, 'json');
                $session->set('cartElements', $jsonCommande);
            }

            return $this->redirectToRoute('product_details_page_l', array('id' => $id));
        }

        return $this->render('default/detail_product.html.twig', array(
            'product' => $product,
            'cartLogo' => $cartLogo,
            'cart_form' => $cart_form->createView(),
        ));
    }

    /**
     * @Route("/{_locale}/recettes", name="index_recipes_page_l", requirements={"_locale" = "%app.locales%"})
     */
    public function recipeAction()
    {
        $recipes = $this->getDoctrine()->getManager()->getRepository(Recipe::class)->findBy(array('enabled' => true));

        $serializer = $this->get('jms_serializer');
        $session = $this->get('session');
        $cartLogo = 0;
        if ($session->has('cartElements')) {
            $commandeJson = $session->get('cartElements');
            $commande = $serializer->deserialize($commandeJson, Devis::class, 'json');
            $cartLogo = count($commande->getItems());
        }

        return $this->render('default/list_recipes.html.twig', array(
            'recipes' => $recipes,
            'cartLogo' => $cartLogo,
        ));
    }

    /**
     * @Route("/{_locale}/recettes/{id}", name="recipe_details_page_l", requirements={"_locale" = "%app.locales%"})
     */
    public function recipeDetailsAction($id)
    {
        $recipe = $this->getDoctrine()->getManager()->getRepository(Recipe::class)->find($id);

        $serializer = $this->get('jms_serializer');
        $session = $this->get('session');
        $cartLogo = 0;
        if ($session->has('cartElements')) {
            $commandeJson = $session->get('cartElements');
            $commande = $serializer->deserialize($commandeJson, Devis::class, 'json');
            $cartLogo = count($commande->getItems());
        }

        $qb = $this->getDoctrine()->getManager()->getRepository(Recipe::class)->createQueryBuilder('r');
        $qb->select('count(r.id)');
        $count = $qb->getQuery()->getSingleScalarResult();

        return $this->render('default/detail_recipe.html.twig', array(
            'recipe' => $recipe,
            'cartLogo' => $cartLogo,
            'count' => $count,
        ));
    }

    /**
     * @Route("/{_locale}/cart-page", name="cart_page", requirements={"_locale" = "%app.locales%"})
     */
    public function cartAction(Request $request)
    {
        $serializer = $this->get('jms_serializer');
        $session = $this->get('session');
        if ($session->has('cartElements')) {
            $devisJson = $session->get('cartElements');
            $devis = $serializer->deserialize($devisJson, Devis::class, 'json');
            $data = $devis->getItems();
            $em = $this->getDoctrine()->getManager();
            $database_commande = $this->getDoctrine()->getManager()->getRepository(Devis::class)->find($devis->getId());
            $commande_form = $this->get('form.factory')->create(DevisType::class, $database_commande);
            if ($request->isMethod('POST') && $commande_form->handleRequest($request)->isValid()) {
                $em->persist($database_commande);
                $em->flush();
                return $this->redirectToRoute('checkout_page');
            }
        }
        else {
            return $this->redirectToRoute('index_page');
        }

        /*$session->clear();
        die('cleared');*/

        $serializer = $this->get('jms_serializer');
        $session = $this->get('session');
        $cartLogo = 0;
        if ($session->has('cartElements')) {
            $commandeJson = $session->get('cartElements');
            $commande = $serializer->deserialize($commandeJson, Devis::class, 'json');
            $cartLogo = count($commande->getItems());
        }

        return $this->render('default/cart-page.html.twig', array(
            'commande_form' => $commande_form->createView(),
            'cartLogo' => $cartLogo,
            'items1' => $data,
        ));
    }

    /**
     * @Route("/{_locale}/delete-cart-item-{id}", name="delete_cart_item", requirements={"_locale" = "%app.locales%"})
     */
    public function deleteCartItemAction($id) {

        $em = $this->getDoctrine()->getManager();
        $serializer = $this->get('jms_serializer');
        $session = $this->get('session');

        if ($session->has('cartElements')) {
            $commandeJson = $session->get('cartElements');
            $commande = $serializer->deserialize($commandeJson, Devis::class, 'json');

            $database_commande = $this->getDoctrine()->getManager()->getRepository(Devis::class)->find($commande->getId());
            $item = $this->getDoctrine()->getManager()->getRepository(DevisItem::class)->find($id);
            $database_commande->removeItem($item);

            $em->remove($item);
            $em->flush();

            $jsonCommande = $serializer->serialize($database_commande, 'json');
            $session->set('cartElements', $jsonCommande);
        }
        return $this->redirectToRoute('cart_page');
    }

    /**
     * @Route("/{_locale}/checkout-page", name="checkout_page", requirements={"_locale" = "%app.locales%"})
     */
    public function checkoutAction(Request $request)
    {
        $serializer = $this->get('jms_serializer');
        $session = $this->get('session');
        if ($session->has('cartElements')) {
            $commandeJson = $session->get('cartElements');
            $commande = $serializer->deserialize($commandeJson, Devis::class, 'json');
            $cartLogo = count($commande->getItems());

            $database_commande = $this->getDoctrine()->getManager()->getRepository(Devis::class)->find($commande->getId());
            if($database_commande->getOrderInfo()) {
                $personalinfo_form = $this->get('form.factory')->create(PersonalInfoType::class, $database_commande->getOrderInfo());
            }
            else {
                $personal_info = new OrderInfo();
                $database_commande->setOrderInfo($personal_info);
                $personalinfo_form = $this->get('form.factory')->create(PersonalInfoType::class, $personal_info);
            }
            if ($request->isMethod('POST') && $personalinfo_form->handleRequest($request)->isValid()) {
                $database_commande->setEnabled(true);
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                return $this->redirectToRoute('index_page');
            }
        }
        else {
            return $this->redirectToRoute('index_page');
        }

        return $this->render('default/validate_devis.html.twig', array(
            'personalinfo_form' => $personalinfo_form->createView(),
            'cartLogo' => $cartLogo,
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

        $serializer = $this->get('jms_serializer');
        $session = $this->get('session');
        $cartLogo = 0;
        if ($session->has('cartElements')) {
            $commandeJson = $session->get('cartElements');
            $commande = $serializer->deserialize($commandeJson, Devis::class, 'json');
            $cartLogo = count($commande->getItems());
        }

        return $this->render('default/contact.html.twig', array(
            'contact_form' => $form->createView(),
            'cartLogo' => $cartLogo,
        ));
    }
}
