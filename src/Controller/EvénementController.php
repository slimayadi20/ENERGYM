<?php

namespace App\Controller;

use App\Entity\CategoriesEvent;
use App\Entity\Evenement;
use App\Form\CategoriesEventType;
use App\Form\EvenementType;
use App\Entity\Participation;
use http\Message;
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
        $Evenement = $this->getDoctrine()->getManager()->getRepository(Evenement::class)->findAll();

        return $this->render("evénement/index.html.twig",array("Evenement"=>$Evenement));
    }
    /**
     * @Route("/EvenementFront", name="EvenementFront")
     */
    public function EvenementFront(): Response
    {
        $CategoriesEvent = $this->getDoctrine()->getManager()->getRepository(CategoriesEvent::class)->findAll();
        $Evenement = $this->getDoctrine()->getManager()->getRepository(Evenement::class)->findAll();
        return $this->render("evénement/AfficherEventFront.html.twig",array("Evenement"=>$Evenement,"CategoriesEvent"=>$CategoriesEvent));
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

        $Event = new Evenement(); // construct vide
        $form = $this->createForm(EvenementType::class,$Event);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $uploadFile = $form['image']->getData();
            $filename = md5(uniqid()) . '.' .$uploadFile->guessExtension();

            $uploadFile->move($this->getParameter('kernel.project_dir').'/public/uploads/Event_image',$filename);
            $Event->setImage($filename);

            $em = $this->getDoctrine()->getManager();
            $em->persist($Event); // Ajouter catégorie
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
        $Event = $em->getRepository(Evenement::class)->find($id);
        $form = $this->createForm(EvenementType::class,$Event);
        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid()) {

            $uploadFile = $form['image']->getData();
            $filename = md5(uniqid()) . '.' .$uploadFile->guessExtension();

            $uploadFile->move($this->getParameter('kernel.project_dir').'/public/uploads/Event_image',$filename);
            $Event->setImage($filename);



            $em->flush();

            return $this->redirectToRoute('evenement');

        }

        return $this->render('evénement/ModifierEvent.html.twig',array("f"=>$form->createView()));


    }


    /**
     * @Route("/dashboard/detail/{id}", name="detail")
     */
    public function detail(Request $req, $id) {
        $em= $this->getDoctrine()->getManager();
        $prod = $em->getRepository(Evenement::class)->find($id);


        return $this->render('evénement/DetailEvent.html.twig',array(
            'id'=>$prod->getId(),
            'NomEvent'=>$prod->getNomEvent(),
            'DescriptionEvent'=>$prod->getDescriptionEvent(),
            'LieuEvent'=>$prod->getLieuEvent(),
            'DateEvent'=>$prod->getDateEvent(),
            'NbrParticipantsEvent'=>$prod->getNbrParticipantsEvent(),
            'image'=>$prod->getImage(),
        ));


    }
    /**
     * @Route("/detailEventFront/{id}", name="detailFrontEvent")
     */
    public function detailEventFront(Request $req, $id) {
        $em= $this->getDoctrine()->getManager();
        $prod = $em->getRepository(Evenement::class)->find($id);


        return $this->render('evénement/AfficherEventDetailFront.html.twig',array(
            'id'=>$prod->getId(),
            'NomEvent'=>$prod->getNomEvent(),
            'DescriptionEvent'=>$prod->getDescriptionEvent(),
            'LieuEvent'=>$prod->getLieuEvent(),
            'DateEvent'=>$prod->getDateEvent(),
            'NbrParticipantsEvent'=>$prod->getNbrParticipantsEvent(),
            'image'=>$prod->getImage(),

        ));


    }
    /**
     * @Route("/ParticiperEvent/{id}", name="ParticiperEvent")
     */
    public function ParticiperEvent(Request $req, $id) {
        $user= $this->getUser() ;
        $iduser= $user->getId() ;
        $em= $this->getDoctrine()->getManager();
        $Event = $em->getRepository(Evenement::class)->find($id);

        //TEST PARTICIPATION:
        $currentEvt =$em->getRepository(Participation::class)->findBy(["idEvent"=>$id]);


        if(  !$currentEvt )
        {
            $Participation= new Participation();
            $em= $this->getDoctrine()->getManager();
            $Event = $em->getRepository(evenement::class)->find($id);
            $Participation->setIdUser($user);
            $Participation->setIdEvent($Event);
            $em->persist($Participation);
            $em->flush();
            return $this->render('evénement/AfficherEventDetailFront.html.twig',array(
                'id'=>$Event->getId(),
                'NomEvent'=>$Event->getNomEvent(),
                'DescriptionEvent'=>$Event->getDescriptionEvent(),
                'LieuEvent'=>$Event->getLieuEvent(),
                'DateEvent'=>$Event->getDateEvent(),
                'NbrParticipantsEvent'=>$Event->getNbrParticipantsEvent(),
                'image'=>$Event->getImage(),

            ));
        }

        return $this->redirectToRoute('ParticipationEffectue');
            }





    /**
     * @Route("/dashboard/AfficherParticipant", name="AfficherParticipant")
     */
    public function AfficherParticipant(): Response
    {
        $Participation = $this->getDoctrine()->getManager()->getRepository(Participation::class)->findAll();

        return $this->render("evénement/AfficherParticipation.html.twig",array("Participation"=>$Participation));

    }



    /**
     * @Route("/ParticipationEffectue", name="ParticipationEffectue")
     */
    public function ParticipationEffectue(): Response
    {
        return $this->render('evénement/ParticipationEffectue.html.twig', [
        ]);
    }



}
