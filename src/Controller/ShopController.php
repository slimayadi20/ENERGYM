<?php

namespace App\Controller;
use App\Entity\Produit;
use App\Entity\Categories;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShopController extends AbstractController
{
    /**
     * @Route("/shop", name="shop")
     */
    public function listProduit(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $produit = $entityManager->getRepository(produit::class)->findAll();
        $Categories = $entityManager->getRepository(Categories::class)->findAll();

        return $this->render('shop/index.html.twig', [
            'controller_name' => 'ShopController',
            'listProduit' => $produit,
            'controller_name' => 'ShopController',
            'listCategories' => $Categories,
        ]);
    }


}
