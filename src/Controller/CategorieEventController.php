<?php

namespace App\Controller;

use App\Entity\CategoriesEvent;
use App\Form\CategoriesEventType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategorieEventController extends AbstractController
{
    /**
     * @Route("/categorie/event", name="categorie_event")
     */
    public function index(): Response
    {
        $CategoriesEvent = $this->getDoctrine()->getManager()->getRepository(CategoriesEvent::class)->findAll(); // select * from product

        return $this->render("categorie_event/index.html.twig",array("CategoriesEvent"=>$CategoriesEvent));

    }

    /**
     * @Route("/AjouterCategEvent", name="AjouterCategEvent")
     */
    public function AjouterCategEvent(Request  $request) {

        $prod = new CategoriesEvent(); // construct vide
        $form = $this->createForm(CategoriesEventType::class,$prod);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($prod); // Ajouter catégorie
            $em->flush(); // commit
            // Page ely fiha table ta3 affichage

            return $this->redirectToRoute('categorie_event'); // yhezo lel page ta3 affichage
        }
        return $this->render('categorie_event/AjouterCategorieEvent.html.twig',array('f'=>$form->createView())); // yab9a fi form

    }

    /**
     * @Route("/SupprimerCategEvent/{id}", name="SupprimerCategEvent")
     */
    public function  SupprimerCategEvent($id) {
        $em= $this->getDoctrine()->getManager();
        $i = $em->getRepository(CategoriesEvent::class)->find($id);

        $em->remove($i);
        $em->flush();

        return $this->redirectToRoute("categorie_event");

    }
    /**
     * @Route("/ModifierCategEvent/{id}", name="ModifierCategEvent")
     */
    public function ModifierCategEvent(Request $request, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $prod = $entityManager->getRepository(CategoriesEvent::class)->find($id);
        $form = $this->createForm(CategoriesEventType::class, $prod);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager->flush();
            $this->addFlash('success' , 'L"action a été effectué');
            return $this->redirectToRoute("categorie_event");
        }

        return $this->render("categorie_event/ModifierCategorieEvent.html.twig", [
            "form_title" => "Modifier un categorie_event",
            "f" => $form->createView(),
        ]);
    }

}
