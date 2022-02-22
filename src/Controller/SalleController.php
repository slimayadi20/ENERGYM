<?php

namespace App\Controller;
use App\Entity\Salle;
use App\Form\SalleFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SalleController extends AbstractController
{
    /**
     * @Route("/salle", name="salle")
     */
    public function index(): Response
    {

        $salle = $this->getDoctrine()->getRepository(salle::class)->findAll();
        return $this->render('salle/index.html.twig', [
            'controller_name' => 'SalleController',
            "salle" => $salle,
        ]);
    }
    /**
     * @Route("/salleFront", name="salleFront")
     */
    public function salleFront(): Response
    {

        $salle = $this->getDoctrine()->getRepository(salle::class)->findAll();
        return $this->render('salle/afficherFront.html.twig', [
            'controller_name' => 'SalleController',
            "salle" => $salle,
        ]);
    }
    /**
     * @Route("/detailFront/{id}", name="detailFront")
     */
    public function detailFront(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $salle = $entityManager->getRepository(salle::class)->find($id);
        return $this->render('salle/detailFront.html.twig', [
            'controller_name' => 'SalleController',
            "salle" => $salle,
        ]);
    }

    /**
     * @Route("/addSalle", name="addSalle")
     */
    public function addSalle(Request $request): Response
    {
        $salle= new salle();
        $form = $this->createForm(SalleFormType::class,$salle);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($salle);
            $entityManager->flush();
            return $this->redirectToRoute("salle");


        }
        return $this->render("salle/ajouter.html.twig", [
            "form_title" => "Ajouter une salle",
            "form_salle" => $form->createView(),
        ]);
    }
    /**
     * @Route("/modifySalle/{id}", name="modifySalle")
     */
    public function modifySalle(Request $request, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $salle = $entityManager->getRepository(salle::class)->find($id);
        $form = $this->createForm(SalleFormType::class, $salle);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager->flush();
            $this->addFlash('success' , 'L"action a été effectué');
            return $this->redirectToRoute("salle");
        }

        return $this->render("salle/modifier.html.twig", [
            "form_title" => "Modifier une salle",
            "form_salle" => $form->createView(),
        ]);
    }
    /**
     * @Route("/deleteSalle/{id}", name="deleteSalle")
     */
    public function deleteSalle(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $salle = $entityManager->getRepository(Salle::class)->find($id);
        $entityManager->remove($salle);
        $entityManager->flush();
        $this->addFlash('success' , 'L"action a été effectué');


        return $this->redirectToRoute("salle");
    }
    /**
     * @Route("/detail_salle/{id}", name="detailsalle")
     */
    public function detailSalle(Request $req, $id) {
        $em= $this->getDoctrine()->getManager();
        $salle = $em->getRepository(Salle::class)->find($id);
        return $this->render('salle/DetailSalle.html.twig',array(
            'nom'=>$salle->getNom(),
            'adresse'=>$salle->getAdresse(),
            'tel'=>$salle->getTel(),
            'mail'=>$salle->getMail(),
            'description'=>$salle->getDescription(),
            'prix1'=>$salle->getPrix1(),
            'prix2'=>$salle->getPrix2(),
            'prix3'=>$salle->getPrix3(),
            'heureo'=>$salle->getHeureo(),
            'heuref'=>$salle->getHeuref(),
            // 'image'=>$salle->getImage()
        ));
    }
    /**
     * @Route("/SallecoursFront/{id}", name="SallecoursFront")
     */
    public function SallecoursFront($id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $salle = $entityManager->getRepository(Salle::class)->find($id);


        return $this->render('cours/afficherFront.html.twig', [
            "salle" => $salle,
        ]);
    }


}

