<?php

namespace ProductBundle\Controller;

use ProductBundle\Entity\Product;
use ProductBundle\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/admin/products", name="list_products_page")
     */
    public function indexAction()
    {
        $products = $this->getDoctrine()->getManager()->getRepository(Product::class)->findBy(array('enabled' => true));
        return $this->render('admin/products/list.html.twig', array(
            'products' => $products,
        ));
    }

    /**
     * @Route("/admin/products/add", name="add_product_page")
     */
    public function addAction(Request $request)
    {
        $product = new Product();
        $product->setPromoEnabled(false);
        $product->setFeatured(false);
        $product->setEnabled(true);
        $form = $this->get('form.factory')->create(ProductType::class, $product);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('list_products_page');
        }
        return $this->render('admin/products/add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/admin/products/edit/{id}", name="edit_products_page")
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository(Product::class)->find($id);

        $form = $this->createForm(ProductType::class, $product);
        if($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
            $em->flush();
            return $this->redirectToRoute('list_products_page');
        }
        return $this->render('admin/products/edit.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/admin/products/delete/{id}", name="delete_product_page")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository(Product::class)->find($id);
        $product->setEnabled(false);
        $em->flush();
        return $this->redirectToRoute('list_products_page');
    }

    /**
     * @Route("/admin/products/feature/{id}", name="feature_product_page")
     */
    public function featureAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository(Product::class)->find($id);
        if($product->isFeatured()) {
            $product->setFeatured(false);
        } else {
            $product->setFeatured(true);
        }
        $em->flush();
        return $this->redirectToRoute('list_products_page');
    }

    /**
     * @Route("/admin/products/promo/{id}", name="promo_product_page")
     */
    public function promoAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository(Product::class)->find($id);
        if($product->isPromoEnabled()) {
            $product->setPromoEnabled(false);
        } else {
            $product->setPromoEnabled(true);
        }
        $em->flush();
        return $this->redirectToRoute('list_products_page');
    }
}
