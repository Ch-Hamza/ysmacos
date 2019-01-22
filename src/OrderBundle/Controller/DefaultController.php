<?php

namespace OrderBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use OrderBundle\Form\ProductType;
use OrderBundle\Entity\Commande;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/orders", name="list_commandes_page")
     */
    public function indexAction()
    {
        $commandes = $this->getDoctrine()->getManager()->getRepository(Commande::class)->findBy(array('enabled' => true, 'archived' => false));
        return $this->render('admin/commandes/list.html.twig', array(
            'commandes' => $commandes,
        ));
    }

    /**
     * @Route("/archive", name="list_commandes_archive")
     */
    public function archivelistAction()
    {
        $commandes = $this->getDoctrine()->getManager()->getRepository(Commande::class)->findBy(array('enabled' => true, 'archived' => true));
        return $this->render('admin/commandes/list.html.twig', array(
            'commandes' => $commandes,
        ));
    }

    /**
     * @Route("/orders/add", name="add_commande_page")
     */
    public function addAction(Request $request)
    {
        $commande = new Commande();
        $commande->setEnabled(true);
        $commande->setArchived(false);
        $form = $this->get('form.factory')->create(ProductType::class, $commande);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($commande);
            foreach ($commande->getItems() as $item){
                $item->setCommande($commande);
            }
            $em->flush();
            return $this->redirectToRoute('list_commandes_page');
        }

        return $this->render('admin/commandes/add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/orders/edit/{id}", name="edit_orders_page")
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository(Commande::class)->find($id);

        $originalItems = new ArrayCollection();
        foreach ($order->getItems() as $item)
        {
            $originalItems->add($item);
        }

        $form = $this->createForm(ProductType::class, $order);
        if($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
            foreach ($originalItems as $item)
            {
                if(false === $order->getItems()->contains($item))
                {
                    $em->remove($item);
                }
            }
            /*foreach ($order->getItems() as $item){
                $item->setCommande($order);
            }*/
            $em->flush();
            return $this->redirectToRoute('list_commandes_page');
        }
        return $this->render('admin/commandes/edit.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/orders/enable/{id}", name="enable_orders_page")
     */
    public function enableAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository(Commande::class)->find($id);
        if($order->getEnabled())
        {
            $order->setEnabled(false);
        }
        $em->flush();
        return $this->redirectToRoute('list_commandes_page');
    }

    /**
     * @Route("/orders/archive/{id}", name="archive_orders_page")
     */
    public function archiveAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $order = $em->getRepository(Commande::class)->find($id);
        $order->setArchived(true);
        $em->flush();
        return $this->redirectToRoute('list_commandes_page');
    }
}
