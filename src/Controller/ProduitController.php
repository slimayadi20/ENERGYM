<?php

namespace App\Controller;
use App\Entity\Produit;
use App\Form\ProduitFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{
    /**
     * @Route("/produit", name="produit")
     */
    public function index(): Response
    {
        $produit = $this->getDoctrine()->getRepository(Produit::class)->findAll();
        return $this->render('produit/index.html.twig', [
            'controller_name' => 'ProduitController',
            "produit"=>$produit,
        ]);
    }
    /**
     * @Route("/addproduit", name="addproduit")
     */
    public function addproduit(Request $request): Response
    {
        $produit = new produit();
        $form = $this->createForm(ProduitFormType::class,$produit);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($produit);
            $entityManager->flush();
            $this->addFlash('success' , 'L"action a été effectué');
            return $this->redirectToRoute("produit");

        }
        return $this->render("produit/ajouter.html.twig", [
            "form_title" => "Ajouter un produit",
            "form_produit" => $form->createView(),
        ]);
    }
    /**
     * @Route("/modifyproduit/{id}", name="modifyproduit")
     */
    public function modifyproduit(Request $request, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $produit = $entityManager->getRepository(produit::class)->find($id);
        $form = $this->createForm(produitFormType::class, $produit);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager->flush();
            $this->addFlash('success' , 'L"action a été effectué');
            return $this->redirectToRoute("produit");
        }

        return $this->render("produit/modifier.html.twig", [
            "form_title" => "Modifier un produit",
            "form_produit" => $form->createView(),
        ]);
    }
    /**
     * @Route("/deleteproduit/{id}", name="deleteproduit")
     */
    public function deleteproduit(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $produit = $entityManager->getRepository(produit::class)->find($id);
        $entityManager->remove($produit);
        $entityManager->flush();
        $this->addFlash('success' , 'L"action a été effectué');


        return $this->redirectToRoute("produit");
    }

}
