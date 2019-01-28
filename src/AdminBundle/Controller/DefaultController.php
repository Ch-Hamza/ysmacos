<?php

namespace AdminBundle\Controller;

use MessageBundle\Entity\Message;
use OrderBundle\Entity\Commande;
use OrderBundle\Entity\DevisItem;
use OrderBundle\Entity\OrderItem;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="index_admin_page")
     */
    public function indexAction()
    {
        $list = $this->getDoctrine()->getManager()->getRepository(DevisItem::class)->countprods();
        $count_orders = $this->getDoctrine()->getManager()->getRepository(Commande::class)->countOrders();
        $count_messages = $this->getDoctrine()->getManager()->getRepository(Message::class)->countMessages();
        $revnue = $this->getDoctrine()->getManager()->getRepository(OrderItem::class)->countRevenue();

        dump($revnue);
        return $this->render('admin/index.html.twig', array(
            'list' => $list,
            'count_orders' => $count_orders,
            'count_messages' => $count_messages,
            'revenue' => $revnue,
        ));
    }
}
