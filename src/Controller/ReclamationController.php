<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class ReclamationController extends AbstractController
{
    /**
     * @Route("/dashboard/reclamation", name="reclamation")
     */
    public function index(): Response
    {
        $reclamation = $this->getDoctrine()->getRepository(Reclamation::class)->findAll();
        $order = 1 ;

        return $this->render('reclamation/index.html.twig', [
            'controller_name' => 'ReclamationController',
            'reclamation' => $reclamation,
            'order'=>$order


        ]);
    }
    /**
     * @Route("/front/reclamation", name="reclamationFront")
     */
    public function reclamation(Request $request): Response
    {
        $user= $this->getUser() ;

        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationFormType::class, $reclamation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reclamation->setStatut('Encours') ;
            $reclamation->setDateCreation(new \DateTime()) ;
            $reclamation->setNomUser($user) ;
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reclamation);
            $entityManager->flush();

            return $this->redirectToRoute('front_office');
        }

        return $this->render('reclamation/AfficherReclamationFront.html.twig', [
            'reclamation' => $reclamation,
            'form_reclamation' => $form->createView(),

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
    /**
     * @Route ("/dashboard/triup", name="croissant")
     */
    public function orderStatusASC(){
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Reclamation::class) ;

        $order=2;
        $reclamations=$repository->triStatusASC();
        return $this->render('reclamation/index.html.twig', [
            'reclamation' => $reclamations,
            'order' => $order
        ]);
    }

    /**
     * @Route("/dashboard/tridown", name="decroissant")
     */
    public function orderStatusDESC(){
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Reclamation::class) ;
        $order=1;
        $reclamations=$repository->triStatusDESC();
        return $this->render('reclamation/index.html.twig', [
            'reclamation' => $reclamations,
            'order' => $order
        ]);
    }
    /**
     * @Route("/dashboard/reply", name="reply")
     */
    public function reply(Request $request): Response
    {

        return $this->render("reclamation/reply.html.twig", [
            "form_title" => "Ajouter une reclamation",

        ]);
    }

    /**
     * @Route("/displayreclamationMobile", name="displayreclamationMobile")
     */
    public function displayreclamationMobile(Request $request, SerializerInterface $serializer): Response
    {
        $reclamation = $this->getDoctrine()->getRepository(Reclamation::class)->findAll();
        $formatted = $serializer->normalize($reclamation,'json',['groups' => 'post:read']);
           return new Response(json_encode($formatted)) ;
    }
    /**
     * @Route("/addReclamationMobile", name="addReclamationMobile")
     */
    public function addReclamationMobile(Request $request, SerializerInterface $serializer): Response
    {

        $reclamation = new Reclamation();
        $titre=$request->query->get("titre") ;
        $contenu=$request->query->get("contenu") ;
            $reclamation->setStatut('Encours') ;
            $reclamation->setTitre($titre) ;
            $reclamation->setContenu($contenu) ;
            $reclamation->setDateCreation(new \DateTime()) ;
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($reclamation);
            $entityManager->flush();

            $formatted = $serializer->normalize($reclamation,'json',['groups' => 'post:read']);
            return new Response(json_encode($formatted)) ;



    }
    /**
     * @Route("/deleteReclamationMobile", name="deleteReclamationMobile")
     */
    public function deleteReclamationMobile(Request $request, SerializerInterface $serializer): Response
    {
        $id=$request->query->get("id") ;
        $entityManager = $this->getDoctrine()->getManager();
        $reclamation = $entityManager->getRepository(reclamation::class)->find($id);
        if($reclamation!=null){
        $entityManager->remove($reclamation);
        $entityManager->flush();
            $formatted = $serializer->normalize($reclamation,'json',['groups' => 'post:read']);
            return new Response(json_encode($formatted)) ;

        }


        return new Response("la reclamaton invalide") ;
    }
    /**
     * @Route("/updateReclamationMobile", name="updateReclamationMobile")
     */
    public function updateReclamationMobile(Request $request, SerializerInterface $serializer): Response
    {

        $entityManager = $this->getDoctrine()->getManager();

        $reclamation = $entityManager->getRepository(Reclamation::class)->find($request->get("id"));
        $titre=$request->query->get("titre") ;
        $contenu=$request->query->get("contenu") ;
        $reclamation->setStatut('Encours') ;
        $reclamation->setTitre($titre) ;
        $reclamation->setContenu($contenu) ;
        $reclamation->setDateCreation(new \DateTime()) ;
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($reclamation);
        $entityManager->flush();

        $formatted = $serializer->normalize($reclamation,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;



    }
}

