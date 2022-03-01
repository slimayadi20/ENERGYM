<?php

namespace App\Controller;
use App\Entity\Salle;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Annotation\Groups ;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


class MobileController extends AbstractController
{
    /**
     * @Route("/mobile", name="mobile")
     */
    public function index(): Response
    {
        return $this->render('mobile/index.html.twig', [
            'controller_name' => 'MobileController',
        ]);
    }
    /**
     * @Route("/displaySalleMobile", name="displaysalleMobile")
     */
    public function displaysalleMobile(Request $request, SerializerInterface $serializer): Response
    {
        $salle = $this->getDoctrine()->getRepository(Salle::class)->findAll();
        $formatted = $serializer->normalize($salle,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;
    }
    /**
     * @Route("/addSalleMobile", name="addSalleMobile")
     */
    public function addSalleMobile(Request $request, SerializerInterface $serializer): Response
    {

        $salle = new Salle();
        $nom=$request->query->get("nom") ;
        $tel=$request->query->get("tel") ;
        $adresse=$request->query->get("adresse") ;
        $mail=$request->query->get("mail") ;
        $description=$request->query->get("description") ;
        $prix1=$request->query->get("prix1") ;
        $prix2=$request->query->get("prix2") ;
        $prix3=$request->query->get("prix3") ;
        $heureo=$request->query->get("heureo") ;
        $heuref=$request->query->get("heuref") ;

        $salle->setAdresse($adresse) ;
        $salle->setNom($nom) ;
        $salle->setTel($tel) ;
        $salle->setAdresse($adresse) ;
        $salle->setMail($mail) ;
        $salle->setDescription($description) ;
        $salle->setPrix1($prix1) ;
        $salle->setPrix2($prix2) ;
        $salle->setPrix3($prix3) ;
        $salle->setHeureo(new \DateTime()) ;
        $salle->setHeuref(new \DateTime()) ;

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($salle);
        $entityManager->flush();

        $formatted = $serializer->normalize($salle,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;



    }
    /**
     * @Route("/deleteSalleMobile", name="deleteSalleMobile")
     */
    public function deleteSalleMobile(Request $request, SerializerInterface $serializer): Response
    {
        $id=$request->query->get("id") ;
        $entityManager = $this->getDoctrine()->getManager();
        $salle = $entityManager->getRepository(salle::class)->find($id);
        if($salle!=null){
            $entityManager->remove($salle);
            $entityManager->flush();
            $formatted = $serializer->normalize($salle,'json',['groups' => 'post:read']);
            return new Response(json_encode($formatted)) ;

        }


        return new Response("la salle invalide") ;
    }
    /**
     * @Route("/updateSalleMobile", name="updateSalleMobile")
     */
    public function updateSalleMobile(Request $request, SerializerInterface $serializer): Response
    {

        $entityManager = $this->getDoctrine()->getManager() ;

        $salle = $entityManager->getRepository(Salle::class)->find($request->get("id")) ;
        $nom=$request->query->get("nom") ;
        $tel=$request->query->get("tel") ;
        $salle->setNom($nom) ;

        $salle->setTel($tel) ;

        $entityManager = $this->getDoctrine()->getManager() ;
        $entityManager->persist($salle) ;
        $entityManager->flush() ;

        $formatted = $serializer->normalize($salle,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;



    }
}
