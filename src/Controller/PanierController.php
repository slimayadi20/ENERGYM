<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Commande;
use App\Entity\Livraison;
use App\Entity\Promo;
use App\Entity\User;
use App\Entity\Produit;
use App\Repository\UserRepository;
use App\Repository\PromoRepository;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class PanierController extends AbstractController
{
    /**
     * @Route("/panier", name="panier")
     */
    public function index(SessionInterface $session, ProduitRepository $produitRepository,Request $request): Response
    {

        $panier = $session->get("panier", []);
        $uid = $this->getUser()->getId();
        $panierUser =$this->getUser()->getPanier($uid);
        // On "fabrique" les données
        $dataPanier = [];
        $totalOld = 0;
        $totalNew = 0;
        $reduction = 0;
        if (!$panierUser)
        {
            //print_r($panier);
            return $this->render('panier/index.html.twig', compact("dataPanier", "totalOld","reduction","totalNew"));
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
            $request->request->get('coupon-code');
            $totalOld += $product->getPrix() * $quantite;
            $codePromo = $entityManager->getRepository(Promo::class)->findCode($request->request->get('coupon-code'));
            if($request->isMethod('POST') ) {
                if ($codePromo) {
                    $reduction = $codePromo->getReduction();
                    $totalNew = $totalOld -($totalOld * ($reduction / 100));
                }
                else {
                    $reduction = "0";
                    $totalNew = $totalOld;
                }
            }
            else {
                $totalNew = $totalOld;
                $reduction = "0";
            }
            //print_r($codePromo);
        }}

        return $this->render('panier/index.html.twig', compact("dataPanier", "totalOld","monPanier","reduction","totalNew" ));
    }
    /**
     * @Route("/valider/{id}", name="valider")
     */
    public function valider(int $id, SessionInterface $session,MailerInterface $mailer,ProduitRepository $produitRepository)
    {
        $user = $this->getUser();
        $panier = $session->get("panier", []);
        $uid  = $user->getId();
        $nom = $user->getNom();
        $prenom = $user->getPrenom();
        $email1 = $user->getEmail();
        $user = $this->getUser();
        $total = 0;
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
            $total += $product->getPrix() * $quantite;}
        $email = (new TemplatedEmail())
            ->from('projetenergym@gmail.com')
            ->to($email1)
            ->subject('Commande affectuée')

            // path of the Twig template to render
            ->htmlTemplate('panier/templatemail.html.twig')


            // pass variables (name => value) to the template
            ->context([
                'total' => $total,
                'dataPanier' => $dataPanier,
                'date' => new \DateTime(),
                'username' => $nom,
                'prenom' => $prenom,
            ])
        ;
        //on envoi l email
        $mailer->send($email) ;

        foreach($p as $a => $quantite)
        {

            $prod = $this->getDoctrine()->getRepository(Produit::class)->find($a);

            $idS = $prod->getId();
            $prod->addUser($user);
            $qt = $prod->getQuantite();
            if ($qt > $panier[$idS]) {
                $prod->setQuantite($qt - $quantite);
            }
            else {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($prod);
                unset($panier[$idS]);
                $entityManager->flush();
            }

        }


        $entityManager = $this->getDoctrine()->getManager();

        $panier = $entityManager->getRepository(Panier::class)->find($id);
        $entityManager->remove($panier);
        //$entityManager->flush();
        $session->set("panier", []);
        $commande = new Commande();
        $entityManager1 = $this->getDoctrine()->getManager();
        $commande->setUser($user);
        $commande->setNom($nom);
        $commande->setPrenom($prenom);
        $commande->setEmail($email1);
        $entityManager1->persist($commande);
        $entityManager1->flush();
        return $this->redirectToRoute("commande");
    }

    /**
     * @Route("/checkout", name="checkout")
     */

    public function checkout(ProduitRepository $produitRepository): Response
    {

        $email = $this->getUser()->getEmail();
        $entityManager = $this->getDoctrine()->getManager();
        $monPanier = $entityManager->getRepository(Panier::class)->loadPanierByUserId($this->getUser()->getId());
        $p= $monPanier->getUserPanier();
        $total = 0;
        $a=array_column($p,'id');
        foreach($p as $a => $quantite){
            $product = $produitRepository->find($a);
            $total += $product->getPrix() * $quantite;
            $dataPanier[] = [

                'price_data' => [
                    'currency' => 'eur',

                    'product_data' => [
                        'name' => $product->getNom(),


                    ],

                    'unit_amount' => $product->getPrix()*100,

                ],
                'quantity' =>$quantite,

            ]; }

        Stripe::setApiKey('sk_test_51KYFLYBbmA2s99ME3poGVY9Vo57GIPHnNZsL4N0g6mWV78cNVmb6kHbzebbY1TtRjt1gSJRBKti6v7NrLuhdnACD00WbaoXfxe');
        $session1 = Session::create([
            'customer_email' => $email,

            'line_items' => [[

                $dataPanier


            ]
            ],
            'mode' => 'payment',

            'success_url' => $this->generateUrl('valider', ['id'=>$monPanier->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
            return $this->redirect($session1->url, 303);



    }
    /**
     * @Route("/success_url/{id}", name="success_url")
     */
    public function successUrl(int $id, SessionInterface $session): Response
    {
        $user = $this->getUser();
        $panier = $session->get("panier", []);
        $uid  = $user->getId();
        $nom = $user->getNom();
        $prenom = $user->getPrenom();
        $email = $user->getEmail();
        $user = $this->getUser();
        for ($i = 0; $i < count($panier); $i++)
        {
            $p=array_key_first($panier);
            $prod = $this->getDoctrine()->getRepository(Produit::class)->find($p);

            $idS = $prod->getId();
            $prod->addUser($user);
            $qt = $prod->getQuantite();
            if ($qt > $panier[$idS]) {
                $prod->setQuantite($qt - $panier[$idS]);
            }
            else {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($prod);
                unset($panier[$idS]);
                $entityManager->flush();
            }

        }


        $entityManager = $this->getDoctrine()->getManager();

        $panier = $entityManager->getRepository(Panier::class)->find($id);
        $entityManager->remove($panier);
        //$entityManager->flush();
        $session->set("panier", []);
        $commande = new Commande();
        $entityManager1 = $this->getDoctrine()->getManager();
        $commande->setUser($user);
        $commande->setNom($nom);
        $commande->setPrenom($prenom);
        $commande->setEmail($email);
        $entityManager1->persist($commande);
        $entityManager1->flush();
        return $this->render('panier/success.html.twig');
    }
    /**
     * @Route("/cancel_url", name="cancel_url")
     */
    public function cancelUrl(): Response
    {
        return $this->render('panier/cancel.html.twig');
    }
    /**
     * @Route("/panier", name="calculCode")
     */
    public function calculCode(SessionInterface $session): Response
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
                $request->request->get('coupon-code');
                $total += $product->getPrix() * $quantite;
                $codePromo = $entityManager->getRepository(Promo::class)->findCode($request);
                if ($codePromo)
                {
                    $total = $total * $codePromo;
                }


            }}

        return $this->render('panier/index.html.twig', compact("dataPanier", "total","monPanier" ));
    }
    /**
     * @Route("/add/{id}", name="add")
     */
    public function add(Produit $produit, SessionInterface $session)
    {
        // On récupère le panier actuel

        $pname= $produit->getNom();
        $qt= $produit->getQuantite();
        $entityManager = $this->getDoctrine()->getManager();
        $panier = $session->get("panier", []);
        $id = $produit->getId();
        $user = $this->getUser();
        $panierUser =$this->getUser()->getPanier();
        if(!empty($panier[$id])){
            if ($panier[$id]<$qt) {
                $panier[$id]++;
            }
            else {$this->addFlash('error' , 'Stock insuffisant pour le produit '.$pname.' '); }
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
     * @Route("/commande", name="commande")
     */
    public function commande(): Response
    {
        $uid = $this->getUser()->getId();
        $commandes = $this->getDoctrine()->getRepository(Commande::class)->findCommandeUser($uid);

        for($d = 0; $d < count($commandes); ++$d)  {



            $cid = $commandes[$d]->getId();
            //print_r($cid);
            $livraisons[$d] = $this->getDoctrine()->getRepository(Livraison::class)->findCommande($cid);

        }

        $i=0;

        return $this->render('commande/commandeFront.html.twig', [
            'controller_name' => 'CommandeController',
            "commandes" => $commandes,
            "livraisons" => $livraisons,
            "i"=> $i,
        ]);
    }

}
