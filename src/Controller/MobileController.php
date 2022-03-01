<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Livraison;
use App\Form\LivraisonFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class MobileController extends AbstractController
{
    /**
     * @Route("/displaylivraisonMobile", name="displaylivraisonMobile")
     */
    public function displaylivraisonMobile(Request $request, SerializerInterface $serializer): Response
    {
        $livraison = $this->getDoctrine()->getRepository(Livraison::class)->findAll();
        $formatted = $serializer->normalize($livraison,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;
    }
    /**
     * @Route("/addlivraisonMobile", name="addlivraisonMobile")
     */
    public function addlivraisonMobile(Request $request, SerializerInterface $serializer): Response
    {

        $livraison = new Livraison();
        $nomLivreur=$request->query->get("nomLivreur") ;
        $etat=$request->query->get("etat") ;
        $idCommande= $request->query->get("idCommande");
        $livraison->setIdCommande($idCommande);
        $livraison->setNomLivreur($nomLivreur) ;
        $livraison->setEtat($etat) ;
        $livraison->setDateLivraison(new \DateTime()) ;
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($livraison);
        $entityManager->flush();

        $formatted = $serializer->normalize($livraison,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;



    }
    /**
     * @Route("/deletelivraisonMobile", name="deletelivraisonMobile")
     */
    public function deletelivraisonMobile(Request $request, SerializerInterface $serializer): Response
    {
        $id=$request->query->get("id") ;
        $entityManager = $this->getDoctrine()->getManager();
        $livraison = $entityManager->getRepository(Livraison::class)->find($id);
        if($livraison!=null){
            $entityManager->remove($livraison);
            $entityManager->flush();
            $formatted = $serializer->normalize($livraison,'json',['groups' => 'post:read']);
            return new Response(json_encode($formatted)) ;

        }


        return new Response("la livraison est invalide") ;
    }
    /**
     * @Route("/updatelivraisonMobile", name="updatelivraisonMobile")
     */
    public function updatelivraisonMobile(Request $request, SerializerInterface $serializer): Response
    {

        $entityManager = $this->getDoctrine()->getManager();

        $livraison = $entityManager->getRepository(Livraison::class)->find($request->get("id"));
        $nomLivreur=$request->query->get("nomLivreur") ;
        $etat=$request->query->get("etat") ;
        $idCommande= $request->query->get("idCommande");
        $livraison->setIdCommande($idCommande);
        $livraison->setNomLivreur($nomLivreur) ;
        $livraison->setEtat($etat) ;
        $livraison->setDateLivraison(new \DateTime()) ;
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($livraison);
        $entityManager->flush();

        $formatted = $serializer->normalize($livraison,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;



    }
}
