<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Livraison;
use App\Form\LivraisonFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LivraisonController extends AbstractController
{
    /**
     * @Route("/livraison", name="livraison")
     */
    public function index(): Response
    {
        $livraisons = $this->getDoctrine()->getRepository(Livraison::class)->findAll();
        return $this->render('livraison/index.html.twig', [
            'controller_name' => 'LivraisonController',
            "livraisons" => $livraisons,
        ]);
    }
    /**
     * @Route("/addLivraison", name="addLivraison")
     */
    public function addCommande(Request $request): Response
    {
        $livraison = new Livraison();
        $form = $this->createForm(LivraisonFormType::class,$livraison);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($livraison);
            $entityManager->flush();
            $this->addFlash('success' , 'L"action a été effectué');
            return $this->redirectToRoute("livraison");

        }
        return $this->render("livraison/ajouter.html.twig", [
            "form_title" => "Ajouter une livraison",
            "form_livraison" => $form->createView(),
        ]);
    }
    /**
     * @Route("/modifyLivraison/{id}", name="modifyLivraison")
     */
    public function modifyLivraison(Request $request, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $liv = $entityManager->getRepository(Livraison::class)->find($id);
        $form = $this->createForm(LivraisonFormType::class, $liv);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager->flush();
            $this->addFlash('success' , 'L"action a été effectué');
            return $this->redirectToRoute("livraison");
        }

        return $this->render("livraison/modifier.html.twig", [
            "form_title" => "Modifier une Livraison",
            "form_livraison" => $form->createView(),
        ]);
    }
    /**
     * @Route("/deleteLivraison/{id}", name="deleteLivraison")
     */
    public function deleteLivraison(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $livraison = $entityManager->getRepository(Livraison::class)->find($id);
        $entityManager->remove($livraison);
        $entityManager->flush();
        $this->addFlash('success' , 'L"action a été effectué');


        return $this->redirectToRoute("livraison");
    }

}
