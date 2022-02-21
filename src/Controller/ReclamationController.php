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
     * @Route("/dashboard/reclamation", name="reclamation")
     */
    public function index(): Response
    {
        $reclamation = $this->getDoctrine()->getRepository(Reclamation::class)->findAll();

        return $this->render('reclamation/index.html.twig', [
            'controller_name' => 'ReclamationController',
            "reclamation" => $reclamation,

        ]);
    }
    /**
     * @Route("/dashboard/addReclamation", name="addReclamation")
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
     * @Route("/dashboard/modifyReclamation/{id}", name="modifyReclamation")
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
     * @Route("/dashboard/deleteReclamation/{id}", name="deleteReclamation")
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

<<<<<<< HEAD
}
=======
}
>>>>>>> daef2f058f84d19f446762f4265099089d16e6ff
