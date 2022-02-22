<?php

namespace App\Controller;

use App\Entity\CategoriesEvent;
use App\Entity\Evenement;
use App\Form\CategoriesEventType;
use App\Form\EvenementType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EvénementController extends AbstractController
{
    /**
     * @Route("/dashboard/evenement", name="evenement")
     */
    public function index(): Response
    {
        $Evenement = $this->getDoctrine()->getManager()->getRepository(Evenement::class)->findAll(); // select * from product

        return $this->render("evénement/index.html.twig",array("Evenement"=>$Evenement));
    }
    /**
     * @Route("/EvenementFront", name="EvenementFront")
     */
    public function EvenementFront(): Response
    {
        return $this->render('evénement/AfficherEventFront.html.twig', [
        ]);
    }
    /**
     * @Route("/EvenementDetailFront", name="EvenementDetailFront")
     */
    public function EvenementDetailFront(): Response
    {
        return $this->render('evénement/AfficherEventDetailFront.html.twig', [
        ]);
    }
    /**
     * @Route("/dashboard/AjouterEvent", name="AjouterEvent")
     */
    public function AjouterEvent(Request  $request) {

        $prod = new Evenement(); // construct vide
        $form = $this->createForm(EvenementType::class,$prod);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($prod); // Ajouter catégorie
            $em->flush(); // commit
            // Page ely fiha table ta3 affichage

            return $this->redirectToRoute('evenement'); // yhezo lel page ta3 affichage
        }
        return $this->render('evénement/AjouterEvent.html.twig',array('f'=>$form->createView())); // yab9a fi form

    }

    /**
     * @Route("/dashboard/SupprimerEvent/{id}", name="SupprimerEvent")
     */
    public function  SupprimerEvent($id) {
        $em= $this->getDoctrine()->getManager();
        $i = $em->getRepository(Evenement::class)->find($id);

        $em->remove($i);
        $em->flush();

        return $this->redirectToRoute("evenement");

    }
    /**
     * @Route("/dashboard/ModifierEvent/{id}", name="ModifierEvent")
     */
    public function ModifierEvent(Request $req, $id) {
        $em= $this->getDoctrine()->getManager();
        $prod = $em->getRepository(Evenement::class)->find($id);
        $form = $this->createForm(EvenementType::class,$prod);
        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('evenement');

        }

        return $this->render('evénement/ModifierEvent.html.twig',array("f"=>$form->createView()));


    }


    /**
     * @Route("/dashboard/detail_produit/{id}", name="detail")
     */
    public function detailProduit(Request $req, $id) {
        $em= $this->getDoctrine()->getManager();
        $prod = $em->getRepository(Evenement::class)->find($id);


        return $this->render('evénement/DetailEvent.html.twig',array(
            'id'=>$prod->getId(),
            'NomEvent'=>$prod->getNomEvent(),
            'DescriptionEvent'=>$prod->getDescriptionEvent(),
            'LieuEvent'=>$prod->getLieuEvent(),
            'DateEvent'=>$prod->getDateEvent(),
            'NbrParticipantsEvent'=>$prod->getNbrParticipantsEvent(),
           // 'image'=>$prod->getImage()
        ));


    }

}
