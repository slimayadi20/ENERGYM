<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Promo;
use App\Form\PromoFormType;
class PromoController extends AbstractController
{
    /**
     * @Route("/promo", name="promo")
     */
    public function index(): Response
    {
        $promos = $this->getDoctrine()->getRepository(Promo::class)->findAll();
        return $this->render('promo/index.html.twig', [
            'controller_name' => 'PromoController',
            "promos"=>$promos,
        ]);
    }
    /**
     * @Route("/dashboard/addPromo", name="addPromo")
     */
    public function addPromo(Request $request): Response
    {
        $promos = new Promo();
        $form = $this->createForm(PromoFormType::class,$promos);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($promos);
            $entityManager->flush();
            $this->addFlash('success' , 'L"action a été effectué');
            return $this->redirectToRoute("promo");

        }
        return $this->render("promo/ajouter.html.twig", [
            "form_title" => "Ajouter  Promo",
            "form_promo" => $form->createView(),
        ]);
    }
    /**
     * @Route("/dashboard/modifyPromo/{id}", name="modifyPromo")
     */
    public function modifyPromo(Request $request, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $promo = $entityManager->getRepository(Promo::class)->find($id);
        $form = $this->createForm(PromoFormType::class, $promo);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager->flush();
            $this->addFlash('success' , 'L"action a été effectué');
            return $this->redirectToRoute("promo");
        }

        return $this->render("promo/modifier.html.twig", [
            "form_title" => "Modifier Promos",
            "form_promo" => $form->createView(),
        ]);
    }
    /**
     * @Route("/dashboard/deletePromo/{id}", name="deletePromo")
     */
    public function deletePromo(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $promo = $entityManager->getRepository(Promo::class)->find($id);
        $entityManager->remove($promo);
        $entityManager->flush();
        $this->addFlash('success' , 'L"action a été effectué');


        return $this->redirectToRoute("promo");
    }

}
