<?php

namespace AdminBundle\Controller;

use AdminBundle\Entity\User;
use AdminBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AdminBundle\Form\EditUserType;

class UserController extends Controller
{
    /**
     * @Route("/users", name="list_users_page")
     */
    public function indexAction()
    {
        $listUsers = $this->getDoctrine()
            ->getManager()
            ->getRepository(User::class)
            ->findAll()
        ;

        return $this->render('admin/users/list.html.twig', array(
            'listUsers' => $listUsers,
        ));
    }

    /**
     * @Route("/users/add", name="add_user_page")
     */
    public function addAction(Request $request)
    {
        $userManger = $this->get('fos_user.user_manager');
        $user = $userManger->createUser();
        $user->setEnabled(true);
        $user->addRole('ROLE_USER');

        $form = $this->createForm(UserType::class, $user);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            $encoder_service = $this->get('security.encoder_factory');
            $encoder = $encoder_service->getEncoder($user);
            $encoded_pass = $encoder->encodePassword($user->getPassword(), $user->getSalt());
            $user->setPassword($encoded_pass);

            $userManger->updateUser($user);
            return $this->redirectToRoute('list_users_page');
        }

        return $this->render('admin/users/add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/users/edit/{id}", name="edit_user_page")
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);

        $userManger = $this->get('fos_user.user_manager');
        $form = $this->createForm(EditUserType::class, $user);

        if($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
            $userManger->updateUser($user);
            return $this->redirectToRoute('list_users_page');
        }

        return $this->render('admin/users/edit.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/users/delete/{id}", name="delete_user_page")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('list_users_page');
    }

    /**
     * @Route("/users/enable/{id}", name="enable_user_page")
     */
    public function enableAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);

        if($user->isEnabled())
        {
            $user->setEnabled(false);
        }
        else
        {
            $user->setEnabled(true);
        }
        $em->flush();

        return $this->redirectToRoute('list_users_page');
    }
}
