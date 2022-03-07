<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationFormType;
use App\Form\ReplyType;
use App\Entity\Reply;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\Promo ;
use App\Entity\Notification ;
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
        $uid = $user->getId();

        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationFormType::class, $reclamation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reclamation->setStatut('Encours') ;
            $reclamation->setDateCreation(new \DateTime()) ;
            $reclamation->setNomUser($user) ;
            $entityManager = $this->getDoctrine()->getManager();
            //begin notification
            $Notification= new Notification();
            $Notification->setTitre("Reclamation from".$this->getUser()->getNom() );
            $Notification->setType("Reclamation");
            $Notification->setCreatedAt(new \DateTime()) ;
            $entityManager->persist($Notification);
            // end notification
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
     * @Route("/dashboard/reply/{id}", name="reply")
     */
    public function reply(Request $request,$id, \Swift_Mailer $mailer): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $Reply = new Reply();

        $reclamation = $entityManager->getRepository(Reclamation::class)->find($id);
        $nomProduit=$reclamation->getProduit() ;
        $emailsender=$reclamation->getNomUser()->getEmail() ;
        $emailme=$this->getUser()->getEmail() ;
        $form = $this->createForm(ReplyType::class, $Reply);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $reclamation->setStatut('Repondu');
            $Reply->setEmailReceiver($emailsender);
            $Reply->setEmailSender($emailme);
            $data = $form->getData();
            $content = $data->getContenu() ;

            $entityManager->persist($Reply);
            $entityManager->flush();

            $this->addFlash('success', 'L"action a été effectué');
            $message = (new \Swift_Message('Reclamation du produit '.$nomProduit))
                //ili bech yeb3ath
                ->setFrom($emailme)
                //ili bech ijih l message
                ->setTo($emailsender) ;

            $img4 = $message->embed(\Swift_Image::fromPath('email/image-4.png'));
            $img5 = $message->embed(\Swift_Image::fromPath('email/image-5.png'));
            $img6 = $message->embed(\Swift_Image::fromPath('email/image-6.png'));
            $img8 = $message->embed(\Swift_Image::fromPath('email/image-8.jpeg'));

            $message->setBody(
                            $this->renderView(
                            // templates/emails/registration.html.twig
                                'emails/ReclamationEmail.html.twig',
                                [
                                    'contenu'=>$content,
                                    'img4'=>$img4,
                                    'img5'=>$img5,
                                    'img6'=>$img6,
                                    'img8'=>$img8,
                                ]
                            ),
                            'text/html'
                        )
        ;
            //on envoi l email
            $mailer->send($message) ;
            return $this->redirectToRoute("reclamation");

        }
        return $this->render("reclamation/reply.html.twig", [
            "form_title" => "Ajouter une reclamation",
            "form_reclamation" => $form->createView(),

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

