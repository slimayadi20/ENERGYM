<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Produit;
use App\Form\CategoriesFormType;
use App\Form\ProduitFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoriesController extends AbstractController
{
    /**
     * @Route("/categories", name="categories")
     */
    public function index(): Response
    {
        $Categories = $this->getDoctrine()->getRepository(Categories::class)->findAll();
        return $this->render('categories/index.html.twig', [
            'controller_name' => 'CategoriesController',
            "Categories"=>$Categories,

        ]);
    }
    /**
     * @Route("/addCategories", name="addCategories")
     */
    public function addCategories(Request $request): Response
    {
        $Categories = new Categories();
        $form = $this->createForm(CategoriesFormType::class,$Categories);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($Categories);
            $entityManager->flush();
            $this->addFlash('success' , 'L"action a été effectué');
            return $this->redirectToRoute("categories");

        }
        return $this->render("categories/ajouter.html.twig", [
            "form_title" => "Ajouter  Categories",
            "form_Categories" => $form->createView(),
        ]);
    }
    /**
     * @Route("/modifyCategories/{id}", name="modifyCategories")
     */
    public function modifyCategories(Request $request, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $Categories = $entityManager->getRepository(Categories::class)->find($id);
        $form = $this->createForm(CategoriesFormType::class, $Categories);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager->flush();
            $this->addFlash('success' , 'L"action a été effectué');
            return $this->redirectToRoute("categories");
        }

        return $this->render("categories/modifier.html.twig", [
            "form_title" => "Modifier Categories",
            "form_Categories" => $form->createView(),
        ]);
    }
    /**
     * @Route("/deleteCategories/{id}", name="deleteCategories")
     */
    public function deleteCategories(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $Categories = $entityManager->getRepository(Categories::class)->find($id);
        $entityManager->remove($Categories);
        $entityManager->flush();
        $this->addFlash('success' , 'L"action a été effectué');


        return $this->redirectToRoute("categories");
    }

}
