<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Form\CoursFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CoursController extends AbstractController
{
    /**
     * @Route("/cours", name="cours")
     */
    public function index(): Response
    {
        $cours = $this->getDoctrine()->getRepository(cours::class)->findAll();
        return $this->render('cours/index.html.twig', [
            'controller_name' => 'CoursController',
            "cour" => $cours,
        ]);
    }
    /**
     * @Route("/addCours", name="addCours")
     */
    public function addcours(Request $request): Response
    {
        $cours = new cours();
        $form = $this->createForm(CoursFormType::class,$cours);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($cours);
            $entityManager->flush();


        }
        return $this->render("cours/ajouter.html.twig", [
            "form_title" => "Ajouter un cours",
            "form_cours" => $form->createView(),
        ]);
    }

}
