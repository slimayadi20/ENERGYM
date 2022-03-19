<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Produit;
use App\Entity\Feedback;
use App\Entity\Review;
use App\Repository\FeedbackRepository;
use App\Repository\ReviewRepository;


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