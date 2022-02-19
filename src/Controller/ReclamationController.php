<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReclamationController extends AbstractController
{
    /**
     * @Route("/reclamation", name="reclamation")
     */
    public function index(): Response
    {
<<<<<<< HEAD
        return $this->render('reclamation/afficher.html.twig', [
=======
        $reclamation = $this->getDoctrine()->getRepository(Reclamation::class)->findAll();

        return $this->render('reclamation/index.html.twig', [
>>>>>>> 626f6ec78554c5771d3eebe4ad9b19945ac73135
            'controller_name' => 'ReclamationController',
            "reclamation" => $reclamation,

        ]);
    }
    /**
     * @Route("/addReclamation", name="addReclamation")
     */
    public function addReclamation(Request $request): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationFormType::class,$reclamation);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $reclamation->setDateCreation(new \DateTime()) ;
            $entityManager->persist($reclamation);
            $entityManager->flush();

            $this->addFlash('success' , 'L"action a été effectué');
            return $this->redirectToRoute("reclamation");

        }
        return $this->render("reclamation/ajouter.html.twig", [
            "form_title" => "Ajouter une reclamation",
            "form_reclamation" => $form->createView(),

        ]);
    }
    /**
     * @Route("/modifyReclamation/{id}", name="modifyReclamation")
     */
    public function modifyReclamation(Request $request, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $reclamation = $entityManager->getRepository(Reclamation::class)->find($id);
        $form = $this->createForm(ReclamationFormType::class, $reclamation);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager->flush();
            $this->addFlash('success' , 'L"action a été effectué');
            return $this->redirectToRoute("reclamation");
        }

        return $this->render("reclamation/modifier.html.twig", [
            "form_title" => "Modifier une reclamation",
            "form_reclamation" => $form->createView(),
        ]);
    }
    /**
     * @Route("/deleteReclamation/{id}", name="deleteReclamation")
     */
    public function deleteReclamation(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $reclamation = $entityManager->getRepository(reclamation::class)->find($id);
        $entityManager->remove($reclamation);
        $entityManager->flush();
        $this->addFlash('success' , 'L"action a été effectué');


        return $this->redirectToRoute("reclamation");
    }
}