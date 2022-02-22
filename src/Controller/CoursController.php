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
     * @Route("/dashboard/cours", name="cours")
     */
    public function index(): Response
    {
        $cours = $this->getDoctrine()->getRepository(cours::class)->findAll();
        return $this->render('cours/index.html.twig', [
            'controller_name' => 'CoursController',
            "cours" => $cours,
        ]);
    }
    /**
     * @Route("/coursFront", name="coursFront")
     */
    public function coursFront(): Response
    {
        return $this->render('cours/afficherFront.html.twig', [
            'controller_name' => 'CoursController',
        ]);
    }
    /**
     * @Route("/dashboard/addCours", name="addCours")
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
            return $this->redirectToRoute("cours");


        }
        return $this->render("cours/ajouter.html.twig", [
            "form_title" => "Ajouter un cours",
            "form_cours" => $form->createView(),
        ]);
    }
    /**
     * @Route("/dashboard/modifyCours/{id}", name="modifyCours")
     */
    public function modifyCours(Request $request, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $cours = $entityManager->getRepository(cours::class)->find($id);
        $form = $this->createForm(CoursFormType::class, $cours);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager->flush();
            $this->addFlash('success' , 'L"action a été effectué');
            return $this->redirectToRoute("cours");
        }

        return $this->render("cours/modifier.html.twig", [
            "form_title" => "Modifier un cours",
            "form_cours" => $form->createView(),
        ]);
    }
    /**
     * @Route("/dashboard/deleteCours/{id}", name="deleteCours")
     */
    public function deleteCours(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $cours = $entityManager->getRepository(cours::class)->find($id);
        $entityManager->remove($cours);
        $entityManager->flush();
        $this->addFlash('success' , 'L"action a été effectué');


        return $this->redirectToRoute("cours");
    }


}
