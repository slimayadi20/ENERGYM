<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Commande;
use App\Entity\User;
use App\Form\CommandeFormType;
use App\Form\CheckoutFormType;
use App\Form\UserFormType;
use App\Repository\UserRepository;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
    /**
     * @Route("/dashboard/commande", name="commande")
     */
    public function index(): Response
    {
        $commandes = $this->getDoctrine()->getRepository(Commande::class)->findAll();
        return $this->render('commande/afficher.html.twig', [
            'controller_name' => 'CommandeController',
            "commandes" => $commandes,
        ]);
    }
    /**
     * @Route("/dashboard/addCommande", name="addCommande")
     */
    public function addCommande(Request $request): Response
    {
        $commande = new Commande();
        $form = $this->createForm(CommandeFormType::class,$commande);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($commande);
            $entityManager->flush();
            $this->addFlash('success' , 'L"action a été effectué');
            return $this->redirectToRoute("commande");

        }
        return $this->render("commande/ajouter.html.twig", [
            "form_title" => "Ajouter une commande",
            "form_commande" => $form->createView(),
        ]);
    }
    /**
     * @Route("/checkout", name="checkout")
     */
    public function addCheckout(SessionInterface $session, ProduitRepository $produitRepository, Request $request): Response
    {
        $panier = $session->get("panier", []);
        $uid = $this->getUser()->getId();
        $panierUser =$this->getUser()->getPanier($uid);
        // On "fabrique" les données
        $dataPanier = [];
        $total = 0;
        if (!$panierUser)
        {
            //print_r($panier);
            return $this->render('panier/index.html.twig', compact("dataPanier", "total"));
        }
        else
        {
            $entityManager = $this->getDoctrine()->getManager();
            $monPanier = $entityManager->getRepository(Panier::class)->loadPanierByUserId($uid);
            $p= $monPanier->getUserPanier();
            $a=array_column($p,'id');
            foreach($p as $a => $quantite){
                $product = $produitRepository->find($a);
                $dataPanier[] = [
                    "produit" => $product,
                    "quantite" => $quantite
                ];
                $total += $product->getPrix() * $quantite;
                return $this->render('panier/checkout.html.twig', compact("dataPanier", "total","monPanier"));

                //print_r($panier);
            }}
        $commande = new Commande();
        $form = $this->createForm(CheckoutFormType::class,$commande);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($commande);
            $entityManager->flush();
            $this->addFlash('success' , 'L"action a été effectué');
            return $this->redirectToRoute("commande");

        }
        return $this->render("panier/checkout.html.twig", [
            "form_title" => "Ajouter une commande",
            "form_commande" => $form->createView(),
            "monPanier" => $monPanier,
        ]);
    }
    /**
     * @Route("/dashboard/modifyCommande/{id}", name="modifyCommande")
     */
    public function modifyCommande(Request $request, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $cmd = $entityManager->getRepository(Commande::class)->find($id);
        $form = $this->createForm(CommandeFormType::class, $cmd);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager->flush();
            $this->addFlash('success' , 'L"action a été effectué');
            return $this->redirectToRoute("commande");
        }

        return $this->render("commande/modifier.html.twig", [
            "form_title" => "Modifier un user",
            "form_commande" => $form->createView(),
        ]);
    }
    /**
     * @Route("/deleteCommande/{id}", name="deleteCommande")
     */
    public function deleteCommande(int $id): Response
    {

        $entityManager = $this->getDoctrine()->getManager();
        $commande = $entityManager->getRepository(Commande::class)->find($id);
        $entityManager->remove($commande);
        $entityManager->flush();
        $this->addFlash('success' , 'L"action a été effectué');


        return $this->redirectToRoute("commande");
    }
}
