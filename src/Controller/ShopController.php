<?php

namespace App\Controller;
use App\Entity\Produit;
use App\Entity\Categories;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProduitRepository;

class ShopController extends AbstractController
{
    /**
     * @Route("/shop", name="shop")
     */
    public function listProduit(PaginatorInterface $paginator,ProduitRepository $repository, Request $request): Response
    {
        $Product =  $paginator->paginate(
            $repository->findallwithpagination(),
            $request->query->getInt('page' , 1), // nombre de page
            2 //nombre limite
        );
        $entityManager = $this->getDoctrine()->getManager();
        $Categories = $entityManager->getRepository(Categories::class)->findAll();

        return $this->render('shop/index.html.twig', [
            "listProduit" => $Product,
            'listCategories' => $Categories,
        ]);
    }


}
