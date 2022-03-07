<?php

namespace App\Controller;
use App\Entity\Salle;
use App\Entity\User;
use App\Form\SalleFormType;
use Knp\Component\Pager\PaginatorInterface;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Repository\SalleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\Entity\SalleLike;
use App\Entity\Inscription;
use App\Entity\Notification;
use App\Repository\SalleLikeRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class SalleController extends AbstractController
{
    /**
     * @Route("/dashboard/salle", name="salle")
     */
    public function index( Session $session, PaginatorInterface $paginator,SalleRepository $repository , Request $request): Response
    {
        $utilisateur = $this->getUser();
        $utilisateurid = $utilisateur->getId();
        if(in_array('ROLE_GERANT', $utilisateur->getRoles())){
            $salle =  $paginator->paginate(
                $repository->findGerantSallewithpagination($utilisateurid),
                $request->query->getInt('page' , 1), // nombre de page
                3//nombre limite
            );
            return $this->render('salle/index.html.twig', [
                "salle" => $salle,
            ]);
        }
        else if(in_array('ROLE_ADMIN', $utilisateur->getRoles())){
            $salle =  $paginator->paginate(
                $repository->findallwithpagination(),
                $request->query->getInt('page' , 1), // nombre de page
                3 //nombre limite
            );
            return $this->render('salle/index.html.twig', [
                "salle" => $salle,
            ]);
        }
        return $this->redirectToRoute('dashboard');

    }
    /**
     * @Route("/map", name="map")
     */
    public function mewmap(): Response
    {

        $salle = $this->getDoctrine()->getRepository(salle::class)->findAll();

        return $this->render('salle/newMap.html.twig', [
            'controller_name' => 'SalleController',
            "salle" => $salle,

        ]);
    }
    /**
     * @Route("/pdf2/{id}", name="pdf2")
     */
    public function pdf2($id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $salle = $entityManager->getRepository(Salle::class)->find($id);


        return $this->render('cours/pdf.html.twig', [
            "salle" => $salle,
        ]);
    }
    /**
     * @route ("/salleFront/{id}/like",name="Salle_like")
     * @param SalleLikeRepository $likeRepo
     */
    public function like(Salle $salle,SalleLikeRepository $likeRepo,SalleRepository $repo): Response
    {
        $user = $this->getUser();
        if (!$user)
            return $this->redirectToRoute("salleFront");
        if ($salle->isLikedByUser($user)) {
            $like = $likeRepo->findOneBy([
                'salle' => $salle,
                'user' => $user]);
            $entityManager = $this->getDoctrine()->getManager();
            $salle->setLikeCount($salle->getLikeCount()-1) ;
            $entityManager->remove($like);
            $entityManager->flush();

            return $this->redirectToRoute("salleFront");

        }
        $like = new SalleLike();
        $like->setSalle($salle)
            ->setUser($user);
        $salle->setLikeCount($salle->getLikeCount()+1) ;
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($like);
        $entityManager->flush();
        $response = new JsonResponse();
        return $this->redirectToRoute("salleFront");
    }
    /**
     * @Route("/salleFront", name="salleFront")
     */
    public function salleFront(): Response
    {

        $salle = $this->getDoctrine()->getRepository(salle::class)->findAll();
        return $this->render('salle/afficherFront.html.twig', [
            'controller_name' => 'SalleController',
            "salle" => $salle,
        ]);
    }
    /**
     * @Route("/salleTries", name="salleTries")
     */
    public function salleTries(): Response
    {

        $salle = $this->getDoctrine()->getRepository(salle::class)->tripluslike();
        return $this->render('salle/afficherFront.html.twig', [
            'controller_name' => 'SalleController',
            "salle" => $salle,
        ]);
    }
    /**
     * @Route("/detailFront/{id}", name="detailFront")
     */
    public function detailFront(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $salle = $entityManager->getRepository(salle::class)->find($id);
        return $this->render('salle/detailFront.html.twig', [
            'controller_name' => 'SalleController',
            "salle" => $salle,
        ]);
    }

    /**
     * @Route("/dashboard/addSalle", name="addSalle")
     */
    public function addSalle(Request $request): Response
    {
        $utilisateur = $this->getUser();
        $salle= new salle();
        $form = $this->createForm(SalleFormType::class,$salle);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();
            // this condition is needed because the 'image' field is not required

            if ($imageFile) {
                // generate new name to the file image with the function generateUniqueFileName
                $fileName = $this->generateUniqueFileName().'.'.$imageFile->guessExtension();

                // moves the file to the directory where products are stored
                $imageFile->move(
                    $this->getParameter('imagesSalle_directory'),
                    $fileName
                );

                // updates the 'product' property to store the image file name
                // instead of its contents
                $salle->setImage($fileName);
                $salle->addUser($utilisateur);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($salle);
            $entityManager->flush();
            return $this->redirectToRoute("salle");


        }
        return $this->render("salle/ajouter.html.twig", [
            "form_title" => "Ajouter une salle",
            "form_salle" => $form->createView(),
        ]);
    }
    /**
     * @Route("/dashboard/modifySalle/{id}/{idU}/", name="modifySalle")
     * @ParamConverter("Salle", options={"mapping": {"id" : "id"}})
     * @ParamConverter("UserA", options={"mapping": {"idU"   : "id"}})
     * @Template()
     */
    public function modifySalle(Salle $salle,User $UserA,Request $request,  Session $session): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $id = $salle->getId();
        $idUser = $UserA->getId();
        $user = $this->getUser();

        if($user->getId() != $idUser )
        {
            $this->addFlash('error' , 'You cant edit anotherone');
            $session->set("message", "Vous ne pouvez pas modifier cette salle");
            return $this->redirectToRoute('salle');

        }
        $salle = $entityManager->getRepository(salle::class)->find($id);
        $form = $this->createForm(SalleFormType::class, $salle);
        $form->handleRequest($request);
        /* print_r("***********************");
         print_r($id);
         print_r("***********************");
         print_r($idUser);
         print_r("***********************");
         print_r($user->getId());*/
        if($form->isSubmitted() && $form->isValid())
        {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();
            // this condition is needed because the 'image' field is not required

            if ($imageFile) {
                // generate new name to the file image with the function generateUniqueFileName
                $fileName = $this->generateUniqueFileName().'.'.$imageFile->guessExtension();

                // moves the file to the directory where products are stored
                $imageFile->move(
                    $this->getParameter('imagesSalle_directory'),
                    $fileName
                );

                // updates the 'product' property to store the image file name
                // instead of its contents
                $salle->setImage($fileName);
            }
            $entityManager->flush();
            $this->addFlash('success' , 'L"action a été effectué');
            return $this->redirectToRoute("salle");
        }

        return $this->render("salle/modifier.html.twig", [
            "form_title" => "Modifier une salle",
            "form_salle" => $form->createView(),
        ]);

    }
    /**
     * @Route("/dashboard/deleteSalle/{id}", name="deleteSalle")
     */
    public function deleteSalle(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $salle = $entityManager->getRepository(Salle::class)->find($id);
        $entityManager->remove($salle);
        $entityManager->flush();
        $this->addFlash('success' , 'L"action a été effectué');


        return $this->redirectToRoute("salle");
    }
    /**
     * @Route("/dashboard/detail_salle/{id}", name="detailsalle")
     */
    public function detailSalle(Request $req, $id) {
        $em= $this->getDoctrine()->getManager();
        $salle = $em->getRepository(Salle::class)->find($id);
        return $this->render('salle/DetailSalle.html.twig',array(
            'nom'=>$salle->getNom(),
            'adresse'=>$salle->getAdresse(),
            'tel'=>$salle->getTel(),
            'mail'=>$salle->getMail(),
            'description'=>$salle->getDescription(),
            'prix1'=>$salle->getPrix1(),
            'prix2'=>$salle->getPrix2(),
            'prix3'=>$salle->getPrix3(),
            'heureo'=>$salle->getHeureo(),
            'heuref'=>$salle->getHeuref(),
            'image'=>$salle->getImage()
        ));
    }
    /**
     * @Route("/SallecoursFront/{id}", name="SallecoursFront")
     */
    public function SallecoursFront($id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $salle = $entityManager->getRepository(Salle::class)->find($id);


        return $this->render('cours/afficherFront.html.twig', [
            "salle" => $salle,
        ]);
    }
// fonction qui generer un identifiant unique pour chaque image
    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }
    // fonction qui generer un identifiant unique pour chaque image
    /**
     * @Route("/InscriptionSalle/{id}", name="InscriptionSalle")
     */
    public function InscriptionSalle( SalleRepository $repository,Request $req, $id,\Swift_Mailer $mailer) {

        $user= $this->getUser() ;
        $iduser= $user->getId() ;

        $email= $user->getEmail() ;
        $name= $user->getNom() ;
        $em= $this->getDoctrine()->getManager();
        $salle = $em->getRepository(Salle::class)->find($id);
        $salleId=$salle->getId();

        //TEST PARTICIPATION:
        $userr =$em->getRepository(Inscription::class)->findBy(["idUser"=>$iduser]);
        //$userr =$em->getRepository(Inscription::class)->findUserinsalle($iduser,$salleId);



        if (  !$userr)
        {
            $inscription= new Inscription();
            $em= $this->getDoctrine()->getManager();
            // begin notification
            $Notification= new Notification();
            $Notification->setTitre( $user->getNom() ."has joined your gym");
            $Notification->setType("New Signup");
            $Notification->setIdSalle($salle);
            $Notification->setCreatedAt(new \DateTime()) ;

            // fin notification
            $inscription->setIdUser($user);
            $inscription->setIdSalle($salle);
            $user->addIdSalle($salle);
            $em->persist($Notification);
            $em->persist($inscription);
            $em->flush();
            Stripe::setApiKey('sk_test_51KYFLYBbmA2s99ME3poGVY9Vo57GIPHnNZsL4N0g6mWV78cNVmb6kHbzebbY1TtRjt1gSJRBKti6v7NrLuhdnACD00WbaoXfxe');
            $session1 = \Stripe\Checkout\Session::create([
                'customer_email' => $email,

                'line_items' => [[

                    'price_data' => [
                        'currency' => 'eur',

                        'product_data' => [
                            'name' => 'abonnement',


                        ],

                        'unit_amount' => 1000,

                    ],
                    'quantity' =>1,


                ]
                ],
                'mode' => 'payment',

                'success_url' => $this->generateUrl('detailFront', ['id'=>$salle->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
                'cancel_url' => $this->generateUrl('cancel_url', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ]);
            return $this->redirect($session1->url, 303);






        }
        $this->addFlash('error' , 'Vous avez deja participe');

        $salle = $em->getRepository(salle::class)->find($id);
        return $this->render('salle/detailFront.html.twig', [
            "salle" => $salle,
        ]);

    }

}