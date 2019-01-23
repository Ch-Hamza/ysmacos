<?php

namespace RecipeBundle\Controller;

use RecipeBundle\Entity\Recipe;
use RecipeBundle\Form\RecipeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/admin/recipes", name="list_recipe_page")
     */
    public function indexAction()
    {
        $recipes = $this->getDoctrine()->getManager()->getRepository(Recipe::class)->findBy(array('enabled' => true));
        return $this->render('admin/recipe/list.html.twig', array(
            'recipes' => $recipes,
        ));
    }

    /**
     * @Route("/admin/recipes/add", name="add_recipe_page")
     */
    public function addAction(Request $request)
    {
        $recipe = new Recipe();
        $recipe->setEnabled(true);
        $form = $this->get('form.factory')->create(RecipeType::class, $recipe);
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($recipe);
            $em->flush();
            return $this->redirectToRoute('list_recipe_page');
        }
        return $this->render('admin/recipe/add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/admin/recipes/edit/{id}", name="edit_recipe_page")
     */
    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $recipe = $em->getRepository(Recipe::class)->find($id);

        $form = $this->createForm(RecipeType::class, $recipe);
        if($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
            $em->flush();
            return $this->redirectToRoute('list_recipe_page');
        }
        return $this->render('admin/recipe/edit.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/admin/recipes/delete/{id}", name="delete_recipe_page")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository(Recipe::class)->find($id);
        $product->setEnabled(false);
        $em->flush();
        return $this->redirectToRoute('list_recipe_page');
    }
}
