<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Categories;
use Symfony\Component\Serializer\SerializerInterface;


class MobileController extends AbstractController
{

    /**
     * @Route("/displaycategorieMobile", name="displaycategoriesMobile")
     */
    public function displaycategoriesMobile(Request $request, SerializerInterface $serializer): Response
    {
        $categories = $this->getDoctrine()->getRepository(Categories::class)->findAll();
        $formatted = $serializer->normalize($categories,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;
    }
    /**
     * @Route("/addcategoriesMobile", name="addcategoriesMobile")
     */
    public function addcategoriesMobile(Request $request, SerializerInterface $serializer): Response
    {

        $categories = new categories();
        $nom=$request->query->get("nom") ;
        $categories->setNom($nom) ;
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($categories);
        $entityManager->flush();

        $formatted = $serializer->normalize($categories,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;



    }
    /**
     * @Route("/deletecategoriesMobile", name="deletecategoriesMobile")
     */
    public function deletecategoriesMobile(Request $request, SerializerInterface $serializer): Response
    {
        $id=$request->query->get("id") ;
        $entityManager = $this->getDoctrine()->getManager();
        $Categories = $entityManager->getRepository(Categories::class)->find($id);
        if($Categories!=null){
            $entityManager->remove($Categories);
            $entityManager->flush();
            $formatted = $serializer->normalize($Categories,'json',['groups' => 'post:read']);
            return new Response(json_encode($formatted)) ;

        }


        return new Response(" categorie invalide") ;
    }
    /**
     * @Route("/updatecategoriesMobile", name="updatecategoriesMobile")
     */
    public function updatecategoriesMobile(Request $request, SerializerInterface $serializer): Response
    {

        $entityManager = $this->getDoctrine()->getManager();

        $categories = $entityManager->getRepository(Categories::class)->find($request->get("id"));
        $nom=$request->query->get("nom") ;
        $categories->setNom($nom) ;
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($categories);
        $entityManager->flush();

        $formatted = $serializer->normalize($categories,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;



    }

}
