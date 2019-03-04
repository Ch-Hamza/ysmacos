<?php

namespace AppBundle\Controller;

use MessageBundle\Form\MessageType;
use MessageBundle\Entity\Message;
use OrderBundle\Entity\Devis;
use OrderBundle\Entity\DevisItem;
use OrderBundle\Entity\OrderInfo;
use OrderBundle\Form\CommandeItemEditTypeClient;
use OrderBundle\Form\CommandeItemEditTypeClient1;
use OrderBundle\Form\DevisType;
use OrderBundle\Form\PersonalInfoType;
use ProductBundle\Entity\Brand;
use ProductBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="index_page")
     */
    public function indexAction(Request $request)
    {
        $message = new Message();
        $message->setEnabled(true);
        $contact_form = $this->get('form.factory')->create(MessageType::class, $message);
        if ($request->isMethod('POST') && $contact_form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($message);
            $em->flush();
            return $this->redirectToRoute('contact_page');
        }

        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository(Brand::class)->createQueryBuilder('b');
        $queryBuilder->setMaxResults(10);
        $brands = $queryBuilder->getQuery()->getResult();

        $serializer = $this->get('jms_serializer');
        $session = $this->get('session');
        $cartLogo = 0;
        if ($session->has('cartElements')) {
            $commandeJson = $session->get('cartElements');
            $commande = $serializer->deserialize($commandeJson, Devis::class, 'json');
            $cartLogo = count($commande->getItems());
        }

        return $this->render('default/index.html.twig', array(
            'cartLogo' => $cartLogo,
            'contact_form' => $contact_form->createView(),
            'brands' => $brands,
        ));
    }

    /**
     * @Route("/products", name="index_products_page")
     */
    public function productAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository(Product::class)->createQueryBuilder('p');
        $queryBuilder->leftJoin('p.brand', 'b');
        $queryBuilder->where('p.enabled = true');
        if($request->query->getAlnum('filter')) {
            $queryBuilder->where('p.name LIKE :name')
                ->setParameter('name', '%'.$request->query->getAlnum('filter').'%');
        }
        if($request->query->getAlnum('brand')) {
            $queryBuilder->where('b.name LIKE :brand')
                ->setParameter('brand', '%'.$request->query->getAlnum('brand').'%');
        }

        $query = $queryBuilder->getQuery();
        $paginator = $this->get('knp_paginator');
        $result = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 9)
        );

        $serializer = $this->get('jms_serializer');
        $session = $this->get('session');
        $cartLogo = 0;
        if ($session->has('cartElements')) {
            $commandeJson = $session->get('cartElements');
            $commande = $serializer->deserialize($commandeJson, Devis::class, 'json');
            $cartLogo = count($commande->getItems());
        }

        //dump($result);
        return $this->render('default/list_products.html.twig', array(
            'products' => $result,
            'cartLogo' => $cartLogo,
        ));
    }

    /**
     * @Route("/products/{id}", name="product_details_page")
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
        $cart_form = $this->get('form.factory')->create(CommandeItemEditTypeClient1::class, $devisItem);
        if($request->isMethod('POST') && $cart_form->handleRequest($request)->isValid()) {

            $devisItem->setProduct($product);
            $session = $this->get('session');

            if ($session->has('cartElements')) {
                //add to existing commande
                $devisJson = $session->get('cartElements');
                $devis = $serializer->deserialize($devisJson, Devis::class, 'json');
                $database_devis = $this->getDoctrine()->getManager()->getRepository(Devis::class)->find($devis->getId());
                $database_devis->addItem($devisItem);
                $devisItem->setDevis($database_devis);
                $em->persist($database_devis);
                $em->flush();
                $jsonDevis = $serializer->serialize($database_devis, 'json');
                //die(var_dump($jsonCommande));
                $session->set('cartElements', $jsonDevis);
            } else {
                $devis = new Devis();
                $devis->setEnabled(false);
                $devis->setSaleDate(new \DateTime());
                $em->persist($devis);
                $em->flush();
                $devis->addItem($devisItem);
                $devisItem->setDevis($devis);
                $em->persist($devisItem);
                //die(var_dump($devisItem));
                $em->flush();
                $jsonDevis = $serializer->serialize($devis, 'json');
                $session->set('cartElements', $jsonDevis);
            }
            return $this->redirectToRoute('index_products_page');
        }

        return $this->render('default/detail_product.html.twig', array(
            'p' => $product,
            'cartLogo' => $cartLogo,
            'cart_form' => $cart_form->createView(),
        ));
    }

    /**
     * @Route("/add-to-cart/{id}", name="add_to_cart_page")
     */
    public function addCartAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository(Product::class)->find($id);
        $serializer = $this->get('jms_serializer');

        $devisItem = new DevisItem();
        if($request->isMethod('POST')) {
            $devisItem->setProduct($product);
            $devisItem->setQuantity(1);
            $session = $this->get('session');

            if ($session->has('cartElements')) {
                //add to existing commande
                $devisJson = $session->get('cartElements');
                $devis = $serializer->deserialize($devisJson, Devis::class, 'json');
                $database_devis = $this->getDoctrine()->getManager()->getRepository(Devis::class)->find($devis->getId());
                $database_devis->addItem($devisItem);
                $devisItem->setDevis($database_devis);
                $em->persist($database_devis);
                $em->flush();
                $jsonDevis = $serializer->serialize($database_devis, 'json');
                //die(var_dump($jsonCommande));
                $session->set('cartElements', $jsonDevis);
            }
            else{
                $devis = new Devis();
                $devis->setEnabled(false);
                $devis->setSaleDate(new \DateTime());
                $em->persist($devis);
                $em->flush();
                $devis->addItem($devisItem);
                $devisItem->setDevis($devis);
                $em->persist($devisItem);
                //die(var_dump($devisItem));
                $em->flush();
                $jsonDevis = $serializer->serialize($devis, 'json');
                $session->set('cartElements', $jsonDevis);
            }

            return $this->redirectToRoute('index_products_page');
        }
        return $this->redirectToRoute('index_products_page');
    }

    /**
     * @Route("/cart-page", name="cart_page")
     */
    public function cartAction(Request $request)
    {
        $serializer = $this->get('jms_serializer');
        $session = $this->get('session');
        if ($session->has('cartElements')) {
            $devisJson = $session->get('cartElements');
            $devis = $serializer->deserialize($devisJson, Devis::class, 'json');
            $em = $this->getDoctrine()->getManager();
            $database_commande = $em->getRepository(Devis::class)->find($devis->getId());
            $data = $devis->getItems();
            $commande_form = $this->get('form.factory')->create(DevisType::class, $database_commande);
            if ($request->isMethod('POST') && $commande_form->handleRequest($request)->isValid()) {
                //die(dump($data));
                $em->persist($database_commande);
                $em->flush();
                $jsonDevis = $serializer->serialize($database_commande, 'json');
                $session->set('cartElements', $jsonDevis);
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
     * @Route("/delete-cart-item-{id}", name="delete_cart_item")
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
     * @Route("/checkout-page", name="checkout_page")
     */
    public function checkoutAction(Request $request)
    {
        $serializer = $this->get('jms_serializer');
        $session = $this->get('session');
        if ($session->has('cartElements')) {
            $commandeJson = $session->get('cartElements');
            $commande = $serializer->deserialize($commandeJson, Devis::class, 'json');
            $cartLogo = count($commande->getItems());

            $data = $commande->getItems();
            dump($data);

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
            'items1' => $data,
        ));
    }

    /**
     * @Route("/contact", name="contact_page")
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
            return $this->redirect($request->getUri());
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
