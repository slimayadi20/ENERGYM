<?php

namespace App\Controller;
use App\Entity\Produit;
use App\Entity\Categories;
use App\Data\SearchData;
use App\Form\SearchFormType;
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
    public function listProduit(PaginatorInterface $paginator, ProduitRepository $repository, Request $request): Response
    {

        $entityManager = $this->getDoctrine()->getManager();
        $Categories = $entityManager->getRepository(Categories::class)->findAll();


            $search = new SearchData();
            $search->page = $request->get('page', 1);
            $form = $this->createForm(SearchFormType::class, $search);
            $form->handleRequest($request);

            $Product = $repository->findSearch($search);
            return $this->render('shop/index.html.twig', [
                'listProduit' => $Product,
                'listCategories' => $Categories,
                'form' => $form->createView()
            ]);

    }

}

