<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Produit;


class ProductController extends AbstractController
{


    /**
     * @Route("/product/{id}", name="product")
     */
    public function detailFront(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $Product = $entityManager->getRepository(Produit::class)->find($id);
        return $this->render('product/index.html.twig', [
            'controller_name' => 'ProductController',
            "Product" => $Product,
        ]);
    }
}