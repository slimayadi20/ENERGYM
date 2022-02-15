<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\User;
use App\Form\CommandeFormType;
use App\Form\UserFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
    /**
     * @Route("/commande", name="commande")
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
     * @Route("/addCommande", name="addCommande")
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
     * @Route("/modifyCommande/{id}", name="modifyCommande")
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
