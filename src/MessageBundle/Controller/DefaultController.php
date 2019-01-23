<?php

namespace MessageBundle\Controller;

use MessageBundle\Entity\Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/admin/messages", name="list_messages_page")
     */
    public function indexAction()
    {
        $messages = $this->getDoctrine()->getManager()->getRepository(Message::class)->findBy(array('enabled' => true));
        return $this->render('admin/messages/list.html.twig', array(
            'messages' => $messages
        ));
    }

    /**
     * @Route("/admin/messages/delete/{id}", name="delete_message_page")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $message = $em->getRepository(Message::class)->find($id);
        $message->setEnabled(false);
        $em->flush();
        return $this->redirectToRoute('list_messages_page');
    }
}
