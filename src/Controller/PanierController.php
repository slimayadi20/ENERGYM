<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\User;
use App\Entity\Produit;
use App\Repository\UserRepository;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    /**
     * @Route("/panier", name="panier")
     */
    public function index(SessionInterface $session, ProduitRepository $produitRepository): Response
    {
        $connecte = $this->getUser();
        if ($connecte) {
            $panier = $session->get("panier", []);
            $uid = $this->getUser()->getId();
            $panierUser = $this->getUser()->getPanier($uid);
            // On "fabrique" les données
            $dataPanier = [];
            $total = 0;
            if (!$panierUser) {
                //print_r($panier);
                return $this->render('panier/index.html.twig', compact("dataPanier", "total"));
            } else {
                $entityManager = $this->getDoctrine()->getManager();
                $monPanier = $entityManager->getRepository(Panier::class)->loadPanierByUserId($uid);
                $p = $monPanier->getUserPanier();
                $a = array_column($p, 'id');
                foreach ($p as $a => $quantite) {
                    $product = $produitRepository->find($a);
                    $dataPanier[] = [
                        "produit" => $product,
                        "quantite" => $quantite
                    ];
                    $total += $product->getPrix() * $quantite;

                    //print_r($panier);
                }
            }

            return $this->render('panier/index.html.twig', compact("dataPanier", "total", "monPanier"));
        }
        return $this->redirectToRoute('connectez');
    }

    /**
     * @Route("/frontpanier", name="frontpanier")
     */

    public function checkout(SessionInterface $session, ProduitRepository $produitRepository): Response
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

                //print_r($panier);
            }}

        return $this->render('front_office/navbar.html.twig', compact("dataPanier", "total","monPanier" ));
    }

    /**
     * @Route("/add/{id}", name="add")
     */
    public function add(Produit $produit, SessionInterface $session)
    {
        // On récupère le panier actuel



        $entityManager = $this->getDoctrine()->getManager();
        $panier = $session->get("panier", []);
        $id = $produit->getId();
        $user = $this->getUser();
        $panierUser =$this->getUser()->getPanier();
        if(!empty($panier[$id])){
            $panier[$id]++;
        }else{
            $panier[$id] = 1;
        }

        // On sauvegarde dans la session

        if (!$panierUser)
        {
            $panierUser = new Panier();
            $panierUser->setUser($user);
            $panierUser->setUserPanier($panier);
            $entityManager->persist($panierUser);
            $entityManager->flush();
        }
        else
        {

            $panierUser->setUser($user);
            $panierUser->setUserPanier($panier);
            $entityManager->flush();
            $session->set("panier", []);
        }
        $session->set("panier", $panier);
        return $this->redirectToRoute("panier");
    }
    /**
     * @Route("/remove/{idS}/{idP}/", name="remove")
     * @ParamConverter("produit", options={"mapping": {"idS" : "id"}})
     * @ParamConverter("panierA", options={"mapping": {"idP"   : "id"}})
     * @Template()
     */
    public function remove(Produit $produit,Panier $panierA, SessionInterface $session)
    {
        // On récupère le panier actuel
        $panier = $session->get("panier", []);
        $idP = $panierA->getId();
        $idS = $produit->getId();
        $user = $this->getUser();
        if(!empty($panier[$idS])){
            if($panier[$idS] > 1){
                $panier[$idS]--;
            }else{
                unset($panier[$idS]);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $panierA = $entityManager->getRepository(Panier::class)->find($idP);
            $panierA->setUser($user);
            $panierA->setUserPanier($panier);
            $entityManager->flush();
        }

        // On sauvegarde dans la session
        $session->set("panier", $panier);
        return $this->redirectToRoute("panier");
    }
    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(int $id, SessionInterface $session)
    {


        $entityManager = $this->getDoctrine()->getManager();
        $panier = $entityManager->getRepository(Panier::class)->find($id);
        $entityManager->remove($panier);
        $entityManager->flush();
        $session->set("panier", []);
        $this->addFlash('success' , 'L"action a été effectué');


        return $this->redirectToRoute("panier");
    }
    /**
     * @Route("/connectez", name="connectez")
     */
    public function ParticipationEffectue(): Response
    {
        return $this->render('panier/connectez.html.twig', [
        ]);
    }
}
