<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Produit;
use App\Form\CategoriesFormType;
use App\Form\ProduitFormType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoriesRepository;

class CategoriesController extends AbstractController
{
    /**
     * @Route("/dashboard/categories", name="categories")
     */
    public function index( PaginatorInterface $paginator,CategoriesRepository $repository , Request $request): Response
    {
        $utilisateur = $this->getUser();
        $utilisateurid = $utilisateur->getId();
        if(in_array('ROLE_GERANT', $utilisateur->getRoles())){
            $Categories =  $paginator->paginate(
                $repository->findGerantCategorieswithpagination($utilisateurid),
                $request->query->getInt('page' , 1), // nombre de page
                1 //nombre limite
            );
            return $this->render('categories/index.html.twig', [
                "Categories"=>$Categories,

            ]);
        }
        else if(in_array('ROLE_ADMIN', $utilisateur->getRoles())){
            $Categories =  $paginator->paginate(
                $repository->findallwithpagination(),
                $request->query->getInt('page' , 1), // nombre de page
                3 //nombre limite
            );
            return $this->render('categories/index.html.twig', [
                "Categories"=>$Categories,

            ]);
        }
        return $this->redirectToRoute('dashboard');

    }
    /**
     * @Route("/dashboard/addCategories", name="addCategories")
     */
    public function addCategories(Request $request): Response
    {
        $utilisateur = $this->getUser();
        $Categories = new Categories();
        $form = $this->createForm(CategoriesFormType::class,$Categories);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $Categories->setUser($utilisateur);

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
     * @Route("/dashboard/modifyCategories/{id}", name="modifyCategories")
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
     * @Route("/dashboard/deleteCategories/{id}", name="deleteCategories")
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
