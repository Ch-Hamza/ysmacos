<?php

namespace OrderBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use OrderBundle\Entity\Commande;
use OrderBundle\Form\FullCommande;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/admin/orders", name="list_commandes_page")
     */
    public function indexAction()
    {
        $commandes = $this->getDoctrine()->getManager()->getRepository(Commande::class)
            ->findBy(array('enabled' => true, 'archived' => false), array('id' => 'desc'));
        return $this->render('admin/commandes/list.html.twig', array(
            'commandes' => $commandes,
        ));
    }

    /**
     * @Route("/admin/archive", name="list_commandes_archive")
     */
    public function archivelistAction()
    {
        $commandes = $this->getDoctrine()->getManager()->getRepository(Commande::class)->findBy(array('enabled' => true, 'archived' => true));
        return $this->render('admin/commandes/list.html.twig', array(
            'commandes' => $commandes,
        ));
    }

    /**
     * @Route("/admin/orders/add", name="add_commande_page")
     */
    public function addAction(Request $request)
    {
        $commande = new Commande();
        $commande->setEnabled(true);
        $commande->setArchived(false);
        $form = $this->get('form.factory')->create(FullCommande::class, $commande);

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
     * @Route("/admin/orders/edit/{id}", name="edit_orders_page")
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

        $form = $this->createForm(FullCommande::class, $order);
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
     * @Route("/admin/orders/enable/{id}", name="enable_orders_page")
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
     * @Route("/admin/orders/archive/{id}", name="archive_orders_page")
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
