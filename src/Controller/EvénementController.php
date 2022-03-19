<?php

namespace App\Controller;

use App\Entity\CategoriesEvent;
use App\Entity\Evenement;
use App\Entity\User;
use App\Repository\EvenementRepository;
use App\Form\CategoriesEventType;
use App\Form\EvenementType;
use App\Entity\Participation;
use App\Repository\ParticipationRepository;
use App\Services\QrcodeService;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use http\Message;
use necrox87\NudityDetector\NudityDetector;
use PhpParser\Node\Stmt\Foreach_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\QrCode;

class EvénementController extends AbstractController
{
    /**
     * @Route("/dashboard/evenement", name="evenement")
     */
    public function index(Request $request): Response
    {
        $Evenement = $this->getDoctrine()->getRepository(Evenement::class)->findAll();
        //$Evenement = $this->getDoctrine()->getRepository(Offre::class)->findBy(array(),array('name' => 'ASC'));
        if ($request->isMethod("POST") ) {

            $type= $request->get('sort') ;
            if ($type == "name" ) {
                $Evenement = $this->getDoctrine()->getRepository(Evenement::class)->findBy(array(),array('NomEvent' => 'ASC'));
            }
            elseif ($type == "nbr_exp") {
                $Evenement = $this->getDoctrine()->getRepository(Evenement::class)->findBy(array(),array('Etat' => 'DESC'));
            }
            else {
                $Evenement = $this->getDoctrine()->getRepository(Evenement::class)->findAll();
            }

        }

        return $this->render("evénement/index.html.twig",array("Evenement"=>$Evenement));
    }
    /**
     * @Route("/EvenementFront", name="EvenementFront")
     */
    public function EvenementFront(): Response
    {
        $CategoriesEvent = $this->getDoctrine()->getManager()->getRepository(CategoriesEvent::class)->findAll();
        $Evenement = $this->getDoctrine()->getManager()->getRepository(Evenement::class)->findAll();
        $Post =  $this->getDoctrine()->getManager()->getRepository(Evenement::class)->findRecent();
        return $this->render("evénement/AfficherEventFront.html.twig",array("Evenement"=>$Evenement,"CategoriesEvent"=>$CategoriesEvent, "recent"=>$Post));


    }
    /**
     * @Route("/EvenementDetailFront", name="EvenementDetailFront")
     */
    public function EvenementDetailFront(): Response
    {
        return $this->render('evénement/AfficherEventDetailFront.html.twig', [
        ]);
    }
    /**
     * @Route("/dashboard/AjouterEvent", name="AjouterEvent")
     */
    public function AjouterEvent(Request  $request) {

        $Event = new Evenement(); // construct vide
        $form = $this->createForm(EvenementType::class,$Event);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $uploadFile = $form['image']->getData();
            $NudityChecker = new NudityDetector($uploadFile);
            if($NudityChecker->isPorn()) {
                $this->addFlash('error' , 'elle comprend un contenu sensible');
            }
            else if ($uploadFile) {
                $filename = md5(uniqid()) . '.' . $uploadFile->guessExtension();

                $uploadFile->move($this->getParameter('kernel.project_dir') . '/public/uploads/Event_image', $filename);
                $Event->setImage($filename);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($Event); // Ajouter catégorie
            $em->flush(); // commit
            // Page ely fiha table ta3 affichage

            return $this->redirectToRoute('evenement'); // yhezo lel page ta3 affichage
        }
        return $this->render('evénement/AjouterEvent.html.twig',array('f'=>$form->createView())); // yab9a fi form

    }

    /**
     * @Route("/dashboard/SupprimerEvent/{id}", name="SupprimerEvent")
     */
    public function  SupprimerEvent($id,\Swift_Mailer $mailer) {
        $em= $this->getDoctrine()->getManager();
        $i = $em->getRepository(Evenement::class)->find($id);
        $participant=$em->getRepository(Participation::class)->findBy(array('idEvent' => $id),array('idEvent' => 'ASC'),null ,null) ;

        foreach ($participant as $s) {

            $email=$s->getIdUser()->getEmail();
            $message = (new \Swift_Message('Evenement annulé :( ' . $i->getNomEvent()))
                ->setFrom('projetenergym@gmail.com')
                ->setTo(array($email => 'hello '));


            $img1 = $message->embed(\Swift_Image::fromPath('email/image-10.png'));


            $message->setBody(
                $this->renderView(
                // templates/emails/registration.html.twig
                    'emails/annulerEvent.html.twig',
                    [
                        'img1'=>$img1,
                    ]
                ),
                'text/html'
            )
            ;
            $mailer->send($message);
            print_r($email);

        }

        $em->remove($i);
        $em->flush();

        return $this->redirectToRoute("evenement");

    }
    /**
     * @Route("/dashboard/ModifierEvent/{id}", name="ModifierEvent")
     */
    public function ModifierEvent(Request $req, $id) {
        $em= $this->getDoctrine()->getManager();
        $Event = $em->getRepository(Evenement::class)->find($id);
        $form = $this->createForm(EvenementType::class,$Event);
        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid()) {

            $uploadFile = $form['image']->getData();
            $filename = md5(uniqid()) . '.' .$uploadFile->guessExtension();

            $uploadFile->move($this->getParameter('kernel.project_dir').'/public/uploads/Event_image',$filename);
            $Event->setImage($filename);



            $em->flush();

            return $this->redirectToRoute('evenement');

        }

        return $this->render('evénement/ModifierEvent.html.twig',array("f"=>$form->createView()));


    }


    /**
     * @Route("/dashboard/detail/{id}", name="detail")
     */
    public function detail(Request $req, $id) {
        $em= $this->getDoctrine()->getManager();
        $prod = $em->getRepository(Evenement::class)->find($id);


        return $this->render('evénement/DetailEvent.html.twig',array(
            'id'=>$prod->getId(),
            'NomEvent'=>$prod->getNomEvent(),
            'DescriptionEvent'=>$prod->getDescriptionEvent(),
            'LieuEvent'=>$prod->getLieuEvent(),
            'DateEvent'=>$prod->getDateEvent(),
            'NbrParticipantsEvent'=>$prod->getNbrParticipantsEvent(),
            'image'=>$prod->getImage(),
        ));


    }
    /**
     * @Route("/detailEventFront/{id}", name="detailFrontEvent")
     */
    public function detailEventFront(Request $req, $id) {
        $em= $this->getDoctrine()->getManager();
        $prod = $em->getRepository(Evenement::class)->find($id);
        $location = $prod->getLieuEvent();
        $CategoriesEvent = $this->getDoctrine()->getManager()->getRepository(CategoriesEvent::class)->findAll();
        $Post =  $this->getDoctrine()->getManager()->getRepository(Evenement::class)->findRecent();
       //  for btn reject
        $user= $this->getUser() ;
        $iduser= $user->getId() ;
        $Event = $em->getRepository(Evenement::class)->find($id);
        $eventId=$Event->getId();
        $userr =$em->getRepository(Participation::class)->findUserinEvent($iduser,$eventId);
        if($userr){
            $reject="true" ;
        }
        else{
            $reject="false";
        }
        // end



        return $this->render('evénement/AfficherEventDetailFront.html.twig',array(
            'id'=>$prod->getId(),
            'NomEvent'=>$prod->getNomEvent(),
            'DescriptionEvent'=>$prod->getDescriptionEvent(),
            'LieuEvent'=>$prod->getLieuEvent(),
            'DateEvent'=>$prod->getDateEvent(),
            'NbrParticipantsEvent'=>$prod->getNbrParticipantsEvent(),
            'image'=>$prod->getImage(),
            'Etat'=>$prod->getEtat(),
            'CategoriesEvent'=>$CategoriesEvent,
            'recent'=>$Post,
            'reject'=>$reject,



        ));


    }





    /**
     * @Route("/dashboard/AfficherParticipant", name="AfficherParticipant")
     */
    public function AfficherParticipant(): Response
    {
        $Participation = $this->getDoctrine()->getManager()->getRepository(Participation::class)->findAll();

        return $this->render("evénement/AfficherParticipation.html.twig",array("Participation"=>$Participation));

    }



    /**
     * @Route("/ParticipationEffectue", name="ParticipationEffectue")
     */
    public function ParticipationEffectue(): Response
    {
        return $this->render('evénement/ParticipationEffectue.html.twig', [
        ]);
    }
    /**
     * @Route("/RecentsPost", name="RecentsPost")
     */
    public function RecentsPost(): Response
    {
        $em= $this->getDoctrine()->getManager();
        $Post = $em->getRepository(Evenement::class)->findRecent();
        return $this->render('evénement/AfficherEventFront.html.twig', [
            "recent"=>$Post
        ]);
    }
    public function QR( $id,EvenementRepository $repository){
        $event=$repository->find($id);// nom event , lieu , date , nom user
        $user=$this->getUser()->getId();
        $name=$event->getNomEvent();
        $lieu=$event->getLieuEvent();
        $date=$event->getDateEvent();

        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($name.$lieu)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(200)
            ->margin(10)
            ->build();
        header('Content-Type: '.$result->getMimeType());
        $result->saveToFile('QRcode/'.'client'.$user.'nomEvent'.$name.'.png');
    }
    public function email($nameUser,$nameEvent,$email, \Swift_Mailer $mailer)
    {

        $message = (new \Swift_Message('confirmation de reservation pour evenement '))
            ->setFrom('projetenergym@gmail.com')
            ->setTo($email);
        $img = $message->embed(\Swift_Image::fromPath('QRcode/client'.$nameUser.'nomEvent'.$nameEvent.'.png'));
        $img8 = $message->embed(\Swift_Image::fromPath('email/image-8.jpeg'));

        $message
            ->setBody(
                $this->renderView(
                // templates/emails/registration.html.twig
                    'emails/registration.html.twig',
                    ['name' => $nameUser,
                        'img'  =>$img,
                        'nomEvent'=> $nameEvent,
                        'img8'=>$img8,
                    ]
                ),
                'text/html'
            )
        ;

        $mailer->send($message);

    }
    /**
     * @Route("/ParticiperEvent/{id}", name="ParticiperEvent")
     */
    public function ParticiperEvent( QrcodeService $qrcodeService,EvenementRepository $repository,Request $req, $id,\Swift_Mailer $mailer) {

        $user= $this->getUser() ;
        $iduser= $user->getId() ;

        $email= $user->getEmail() ;
        $name= $user->getNom() ;
        $em= $this->getDoctrine()->getManager();
        $Event = $em->getRepository(Evenement::class)->find($id);
        $eventId=$Event->getId();
        $nameEvent=$Event->getNomEvent();
        $CategoriesEvent = $this->getDoctrine()->getManager()->getRepository(CategoriesEvent::class)->findAll();
        $Post =  $this->getDoctrine()->getManager()->getRepository(Evenement::class)->findRecent();

        //TEST PARTICIPATION:
      //  $currentEvt =$em->getRepository(Participation::class)->findBy(["idEvent"=>$id]);
        $userr =$em->getRepository(Participation::class)->findUserinEvent($iduser,$eventId);

        if ($Event->getNbrParticipantsEvent()==1){
            $Event->setEtat("Complet");
        }

        if (  !$userr)
        {
            $Participation= new Participation();
            $em= $this->getDoctrine()->getManager();
            $Event = $em->getRepository(Evenement::class)->find($id);
            $nbr=$Event->getNbrParticipantsEvent();
            $Event->setNbrParticipantsEvent($nbr-1);
            $Participation->setIdUser($user);
            $Participation->setIdEvent($Event);
            $random = random_int(1000, 9000);
            $Participation->setVerificationCode($random);
            $qrCode = $qrcodeService->qrcode($name,$nameEvent,$random);
            $this->email($name,$nameEvent,$email,$mailer);
            $em->persist($Participation);
            $em->flush();
            $reject="true" ;


            return $this->render('evénement/AfficherEventDetailFront.html.twig',array(
                'id'=>$Event->getId(),
                'NomEvent'=>$Event->getNomEvent(),
                'DescriptionEvent'=>$Event->getDescriptionEvent(),
                'LieuEvent'=>$Event->getLieuEvent(),
                'DateEvent'=>$Event->getDateEvent(),
                'NbrParticipantsEvent'=>$Event->getNbrParticipantsEvent(),
                'image'=>$Event->getImage(),
                'Etat'=>$Event->getEtat(),
                'CategoriesEvent'=>$CategoriesEvent,
                'recent'=>$Post,
                 'reject'=>$reject,
            ));
        }

        $this->addFlash('error' , 'Vous avez deja participe');
        $reject="true" ;

        return $this->render('evénement/AfficherEventDetailFront.html.twig',array(
            'id'=>$Event->getId(),
            'NomEvent'=>$Event->getNomEvent(),
            'DescriptionEvent'=>$Event->getDescriptionEvent(),
            'LieuEvent'=>$Event->getLieuEvent(),
            'DateEvent'=>$Event->getDateEvent(),
            'NbrParticipantsEvent'=>$Event->getNbrParticipantsEvent(),
            'image'=>$Event->getImage(),
            'Etat'=>$Event->getEtat(),
            'CategoriesEvent'=>$CategoriesEvent,
            'recent'=>$Post,
            'reject'=>$reject,



        ));

    }
    /**
     * @Route("/AnnulerParticiperEvent/{id}", name="AnnulerParticiperEvent")
     */
    public function AnnulerParticiperEvent(EvenementRepository $repository,Request $req, $id,\Swift_Mailer $mailer) {

        $user= $this->getUser() ;
        $iduser= $user->getId() ;
        $email= $user->getEmail() ;
        $name= $user->getNom() ;
        $em= $this->getDoctrine()->getManager();
        $Event = $em->getRepository(Evenement::class)->find($id);
        $eventId=$Event->getId();
        $nameEvent=$Event->getNomEvent();
        $CategoriesEvent = $this->getDoctrine()->getManager()->getRepository(CategoriesEvent::class)->findAll();
        $Post =  $this->getDoctrine()->getManager()->getRepository(Evenement::class)->findRecent();

        //TEST PARTICIPATION:
        $userr =$em->getRepository(Participation::class)->findUserinEvent($iduser,$eventId);
        foreach ($userr as $c)
        {
        $participation=$em->getRepository(Participation::class)->find($c->getId());
            if ($userr)
            {
                $Event->setNbrParticipantsEvent($Event->getNbrParticipantsEvent()+1) ;
                $em->remove($participation);
                $em->flush();
                if ($Event->getNbrParticipantsEvent()==0){
                    $Event->setEtat("incomplet");
                    $Event->setNbrParticipantsEvent($Event->getNbrParticipantsEvent()+1) ;
                }
                $reject="true" ;

                return $this->render('evénement/AfficherEventDetailFront.html.twig',array(
                    'id'=>$Event->getId(),
                    'NomEvent'=>$Event->getNomEvent(),
                    'DescriptionEvent'=>$Event->getDescriptionEvent(),
                    'LieuEvent'=>$Event->getLieuEvent(),
                    'DateEvent'=>$Event->getDateEvent(),
                    'NbrParticipantsEvent'=>$Event->getNbrParticipantsEvent(),
                    'image'=>$Event->getImage(),
                    'Etat'=>$Event->getEtat(),
                    'CategoriesEvent'=>$CategoriesEvent,
                    'recent'=>$Post,
                    'reject'=>$reject,


                ));
            }
        }




        $this->addFlash('error' , 'Vous avez deja annulé');
        $reject="false" ;

        return $this->render('evénement/AfficherEventDetailFront.html.twig',array(
            'id'=>$Event->getId(),
            'NomEvent'=>$Event->getNomEvent(),
            'DescriptionEvent'=>$Event->getDescriptionEvent(),
            'LieuEvent'=>$Event->getLieuEvent(),
            'DateEvent'=>$Event->getDateEvent(),
            'NbrParticipantsEvent'=>$Event->getNbrParticipantsEvent(),
            'image'=>$Event->getImage(),
            'Etat'=>$Event->getEtat(),
            'CategoriesEvent'=>$CategoriesEvent,
            'recent'=>$Post,
            'reject'=>$reject,


        ));

    }

//SEARCH


    /**
     * @Route("/ajax_search/", name="ajax_search")
     */
    public function chercherProduit(\Symfony\Component\HttpFoundation\Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $requestString = $request->get('q');
        $evenementss =  $em->getRepository(Evenement::class)->rechercheAvance($requestString);
        if(!$evenementss) {
            $result['evenementss']['error'] = "Product non trouvé 🙁 ";
        } else {
            $result['evenementss'] = $this->getRealEntities($evenementss);
        }
        return new Response(json_encode($result));
    }
    public function getRealEntities($evenementss){

        foreach ($evenementss as $evenementss){
            $realEntities[$evenementss->getId()] = [$evenementss->getImage(),$evenementss->getNomEvent()];

        }
        return $realEntities;
    }


}