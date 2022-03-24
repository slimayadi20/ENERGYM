<?php

namespace App\Controller;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Writer\PngWriter;
use Lcobucci\JWT\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\User ;
use App\Entity\Salle ;
use App\Entity\SalleLike ;
use App\Entity\Cours ;
use App\Entity\Reclamation;
use App\Entity\Reply;
use App\Entity\Evenement;
use App\Entity\Participation;
use App\Entity\CategoriesEvent;
use App\Repository\SalleLikeRepository;
use App\Repository\EvenementRepository;
use App\Services\QrcodeService;
use App\Entity\Categories;
use App\Entity\Produit;

class MobileController extends AbstractController
{

    /**
     * @Route("/displayreclamationMobile", name="displayreclamationMobile")
     */
    public function displayreclamationMobile(Request $request, SerializerInterface $serializer): Response
    {
        $id=$request->get('NomUser');
        $reclamation = $this->getDoctrine()->getRepository(Reclamation::class)->findBy(array('NomUser'=>$id));
        $formatted = $serializer->normalize($reclamation,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;
    }
    /**
     * @Route("/displayreclamationMobileAll", name="displayreclamationMobileAll")
     */
    public function displayreclamationMobileAll(Request $request, SerializerInterface $serializer): Response
    {

        $reclamation = $this->getDoctrine()->getRepository(Reclamation::class)->findAll();
        $formatted = $serializer->normalize($reclamation,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;
    }
    /**
     * @Route("/addReclamationMobile", name="addReclamationMobile")
     */
    public function addReclamationMobile(Request $request, SerializerInterface $serializer): Response
    {

        $reclamation = new Reclamation();
        $titre=$request->query->get("titre") ;
        $contenu=$request->query->get("contenu") ;
        $reclamation->setStatut('Encours') ;
        $idUser = $request->query->get("NomUser");
        $em= $this->getDoctrine()->getManager();
        // hedhi fazet jointure
        $user = $em->getRepository(User::class)->find($idUser);
        $reclamation->setNomUser($user) ;
        // toufa lenna
        $reclamation->setTitre($titre) ;
        $reclamation->setContenu($contenu) ;
        $reclamation->setDateCreation(new \DateTime()) ;
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($reclamation);
        $entityManager->flush();

        $formatted = $serializer->normalize($reclamation,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;



    }
    /**
     * @Route("/deleteReclamationMobile", name="deleteReclamationMobile")
     */
    public function deleteReclamationMobile(Request $request, SerializerInterface $serializer): Response
    {
        $id=$request->query->get("id") ;
        $entityManager = $this->getDoctrine()->getManager();
        $reclamation = $entityManager->getRepository(reclamation::class)->find($id);
        if($reclamation!=null){
            $entityManager->remove($reclamation);
            $entityManager->flush();
            $formatted = $serializer->normalize($reclamation,'json',['groups' => 'post:read']);
            return new Response(json_encode($formatted)) ;

        }


        return new Response("la reclamaton invalide") ;
    }
    /**
     * @Route("/updateReclamationMobile", name="updateReclamationMobile")
     */
    public function updateReclamationMobile(Request $request, SerializerInterface $serializer): Response
    {

        $entityManager = $this->getDoctrine()->getManager();

        $reclamation = $entityManager->getRepository(Reclamation::class)->find($request->get("id"));
        $titre=$request->query->get("titre") ;
        $contenu=$request->query->get("contenu") ;
        $reclamation->setStatut("Encours") ;
        $reclamation->setTitre($titre) ;
        $reclamation->setContenu($contenu) ;
        $reclamation->setDateCreation(new \DateTime()) ;
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($reclamation);
        $entityManager->flush();

        $formatted = $serializer->normalize($reclamation,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;



    }
    /**
     * @Route("replyBack", name="replyBack")
     */
    public function replyBack(Request $request, \Swift_Mailer $mailer): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $Reply = new Reply();
        $idRec=$request->get("id");
        $content=$request->get("contenu");
        $idAdmin=$request->get("idAdmin");

        $reclamation = $entityManager->getRepository(Reclamation::class)->find($idRec);

        $nomProduit=$reclamation->getProduit() ;

        $emailsender=$reclamation->getNomUser()->getEmail() ;

        $user=$entityManager->getRepository(User::class)->find($idAdmin);
        $emailme=$user->getEmail() ;


            $reclamation->setStatut('Repondu');
            $Reply->setEmailReceiver($emailsender);
            $Reply->setEmailSender($emailme);


            $entityManager->persist($Reply);
            $entityManager->flush();

            $this->addFlash('success', 'L"action a été effectué');
            $message = (new \Swift_Message('Reclamation du produit '.$nomProduit))
                //ili bech yeb3ath
                ->setFrom($emailme)
                //ili bech ijih l message
                ->setTo($emailsender) ;

            $img4 = $message->embed(\Swift_Image::fromPath('email/image-4.png'));
            $img5 = $message->embed(\Swift_Image::fromPath('email/image-5.png'));
            $img6 = $message->embed(\Swift_Image::fromPath('email/image-6.png'));
            $img8 = $message->embed(\Swift_Image::fromPath('email/image-8.jpeg'));

            $message->setBody(
                $this->renderView(
                // templates/emails/registration.html.twig
                    'emails/ReclamationEmail.html.twig',
                    [
                        'contenu'=>$content,
                        'img4'=>$img4,
                        'img5'=>$img5,
                        'img6'=>$img6,
                        'img8'=>$img8,
                    ]
                ),
                'text/html'
            )
            ;
            //on envoi l email
            $mailer->send($message) ;

        return new Response("mail envoye check your email") ;

        }
    /**
     * @Route ("/triupMobile", name="triupMobile")
     */
    public function orderStatusASC(Request $request, SerializerInterface $serializer){
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Reclamation::class) ;

        $order=2;
        $reclamations=$repository->triStatusASC();
        $formatted = $serializer->normalize($reclamations,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;

    }
    /**
     * @Route("tridownMobile", name="tridownMobile")
     */
    public function orderStatusDESC(Request $request, SerializerInterface $serializer){
        $entityManager = $this->getDoctrine()->getManager();
        $repository = $entityManager->getRepository(Reclamation::class) ;
        $order=1;
        $reclamations=$repository->triStatusDESC();
        $formatted = $serializer->normalize($reclamations,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;
    }
    // user mobile

    /**
     * @Route("/signupMobile", name="signupMobile")
     */
    public function signup(Request $request, UserPasswordEncoderInterface $encoder, SerializerInterface $serializer): Response
    {

        $email = $request->query->get("email");
        $nom = $request->query->get("nom");
        $prenom = $request->query->get("prenom");
        $password = $request->query->get("password");
        $phone = $request->query->get("phoneNumber");
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new Response("email invalid");
        }
        $user = new User();
        $user->setEmail($email);
        $user->setPhoneNumber($phone);
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setRoles('ROLE_USER');
        $user->setStatus(2);
        $user->setCreatedAt(new \DateTime());
        $user->setPassword($password);
        $user->setImageFile("aa");
        try {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            return new JsonResponse("Account is created", 200);
        } catch (\Exception$ex) {
            return new Response("exception" . $ex->getMessage());
        }


    }
    /**
     * @Route("/signinMobile", name="signinMobile")
     */
    public function signinAction(Request $request,SerializerInterface $serializer)
    {
        $email = $request->query->get("email");
        $password = $request->query->get("password");
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($user) {
            if ($password==$user->getPassword()) {
                $formatted = $serializer->normalize($user, 'json', ['groups' => 'post:read']);
                return new Response(json_encode($formatted));

            }
            else {

                    return new Response("passowrd not found");
                }
            }
        else {
                return new Response("user not found");
            }

    }
    //partie user
    /**
     * @Route("/editUserMobile", name="editUserMobile")
     */
    public function editUserMobile(Request $request,SerializerInterface $serializer, UserPasswordEncoderInterface $encoder)
    {
        $id=$request->query->get("id");
        $email = $request->query->get("email");
        $nom = $request->query->get("nom");
        $prenom = $request->query->get("prenom");
        $password = $request->query->get("password");
        $phone = $request->query->get("phoneNumber");
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($request->files->get("imageFile")==null) {
            $file = $request->files->get("imageFile");
         //   $filename = $file->getClientOriginalName();
         //   $file->move($filename);
          //  $user->setImageFile($filename);
            $user->setEmail($email);
            $user->setPhoneNumber($phone);
            $user->setNom($nom);
            $user->setPrenom($prenom);
            $user->setPassword($password);
        }
        try{
            $em=$this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return new JsonResponse("success",200);
        }catch(\Exception $ex){
            return new Response("fail".$ex->getMessage());
        }



    }
    /**
     * @Route("/passwordMobile", name="passwordMobile")
     */
    public function getPasswordbyPhone(Request $request,SerializerInterface $serializer)
    {
        $phoneNumber = $request->query->get("phoneNumber");
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['phoneNumber' => $phoneNumber]);
        if ($user) {
            $password=$user->getPassword();
            $formatted = $serializer->normalize($password, 'json', ['groups' => 'post:read']);
            return new Response(json_encode($formatted));

        }
        else {
            return new Response("user not found");
        }

    }
    // **** evenement
    /**
     * @Route("/displayEvenementMobileAll", name="displayEvenementMobileAll")
     */
    public function displayEvenementMobileAll(Request $request, SerializerInterface $serializer): Response
    {

        $evenement = $this->getDoctrine()->getRepository(Evenement::class)->findAll();
        $formatted = $serializer->normalize($evenement,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;
    }
    /**
     * @Route("/SupprimerEventMobile", name="SupprimerEventMobile")
     */
    public function  SupprimerEventMobile(Request $request,\Swift_Mailer $mailer) {
        $id=$request->get('id');
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

        return new Response("event deleted and mail was sent");

    }
    /**
     * @Route("/ModifierEventMobile", name="ModifierEventMobile")
     */
    public function ModifierEventMobile(Request $request, SerializerInterface $serializer) {
        $id=$request->get('id');
        $nom=$request->get('nom');
        $date=$request->get('date');
        $lieu=$request->get('lieu');
        $description=$request->get('description');
        $nbr=$request->get('nbr');
        $categorieid=$request->get('categorieid');
        $em= $this->getDoctrine()->getManager();
        $Event = $em->getRepository(Evenement::class)->find($id);
        $categorie=$em->getRepository(CategoriesEvent::class)->find($categorieid);
        $Event->setNomEvent($nom);
        $Event->setDateEvent(new \DateTime($date)); // hedhi fazet el date
        $Event->setDescriptionEvent($description);
        $Event->setLieuEvent($lieu);
        $Event->setNbrParticipantsEvent($nbr);
        $Event->setNomCategorie($categorie);
        if ($nbr >0)
        {
            $Event->setEtat("Incomplet");
        }
        if ($nbr ==0)
        {
            $Event->setEtat("Complet");
        }
            $em->flush();
        $formatted = $serializer->normalize($Event,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;
    }
    /**
     * @Route("/AddEventMobile", name="AddEventMobile")
     */
    public function AddEventMobile(Request $request, SerializerInterface $serializer) {
        $Event=new Evenement();
        $nom=$request->get('nom');
        $date=$request->get('date');
        $lieu=$request->get('lieu');
        $description=$request->get('description');
        $nbr=$request->get('nbr');
        $categorieid=$request->get('categorieid');
        $em= $this->getDoctrine()->getManager();
        $categorie=$em->getRepository(CategoriesEvent::class)->find($categorieid);
        $Event->setNomEvent($nom);
        $Event->setDateEvent(new \DateTime($date)); // hedhi fazet el date
        $Event->setDescriptionEvent($description);
        $Event->setLieuEvent($lieu);
        $Event->setNbrParticipantsEvent($nbr);
        $Event->setNomCategorie($categorie);
        $Event->setImage("untitled.jpg");
        $Event->setEtat("Incomplet");
        $em->persist($Event);
        $em->flush();
        $formatted = $serializer->normalize($Event,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;
    }
    // categorie event
    /**
     * @Route("/displayCategoriesMobile", name="displayCategoriesMobile")
     */
    public function displayCategoriesMobile(Request $request, SerializerInterface $serializer): Response
    {

        $categ = $this->getDoctrine()->getRepository(CategoriesEvent::class)->findAll();
        $formatted = $serializer->normalize($categ,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;
    }
    /**
     * @Route("/displayCategoriesEventMobile", name="displayCategoriesEventMobile")
     */
    public function displayCategoriesEventMobile(Request $request, SerializerInterface $serializer): Response
    {
$id=$request->get('id');
        $categ = $this->getDoctrine()->getRepository(Evenement::class)->findBy(array('NomCategorie'=>$id));
        $formatted = $serializer->normalize($categ,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;
    }
    /**
     * @Route("/ajoutCategoriesMobile", name="ajoutCategoriesMobile")
     */
    public function ajoutCategoriesMobile(Request $request, SerializerInterface $serializer): Response
    {
        $em= $this->getDoctrine()->getManager();
        $nom=$request->get('nom');
        $categ=new CategoriesEvent();
        $categ->setNomCategorie($nom);
        $em->persist($categ);
        $em->flush();
        $formatted = $serializer->normalize($categ,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;
    }
    /**
     * @Route("/ModifierCategoriesMobile", name="ModifierCategoriesMobile")
     */
    public function ModifierCategoriesMobile(Request $request, SerializerInterface $serializer): Response
    {
        $em= $this->getDoctrine()->getManager();
        $id=$request->get('id');
        $categorie=$em->getRepository(CategoriesEvent::class)->find($id);
        $nom=$request->get('nom');
        $categorie->setNomCategorie($nom);
        $em->persist($categorie);
        $em->flush();
        $formatted = $serializer->normalize($categorie,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;
    }
    /**
     * @Route("/SupprimerCategoriesMobile", name="SupprimerCategoriesMobile")
     */
    public function SupprimerCategoriesMobile(Request $request, SerializerInterface $serializer): Response
    {
        $em= $this->getDoctrine()->getManager();
        $id=$request->get('id');
        $categorie=$em->getRepository(CategoriesEvent::class)->find($id);
        $em->remove($categorie);
        $em->flush();
        return new Response("deleted successfuly") ;
    }
// ******* participation
    /**
     * @Route("/AfficherParticipantMobile", name="AfficherParticipantMobile")
     */
    public function AfficherParticipantMobile(Request $request, SerializerInterface $serializer): Response
    {
       // $idEvent=$request->get('id');
        $Participation = $this->getDoctrine()->getManager()->getRepository(Participation::class)->findAll();
 $formatted = $serializer->normalize($Participation,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;
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
     * @Route("/ParticiperEventMobile", name="ParticiperEventMobile")
     */
    public function ParticiperEventMobile( Request $request ,QrcodeService $qrcodeService,EvenementRepository $repository, \Swift_Mailer $mailer) {

        $em= $this->getDoctrine()->getManager();
       $iduser=$request->get('iduser');
       $user=$em->getRepository(User::class)->find($iduser);
        $email= $user->getEmail() ;
        $name= $user->getNom() ;
        $id=$request->get('id');
        $Event = $em->getRepository(Evenement::class)->find($id);
        $eventId=$Event->getId();
        $nameEvent=$Event->getNomEvent();
       // $CategoriesEvent = $this->getDoctrine()->getManager()->getRepository(CategoriesEvent::class)->findAll();
        //$Post =  $this->getDoctrine()->getManager()->getRepository(Evenement::class)->findRecent();

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

            return new Response("participation done") ;


        }

        $this->addFlash('error' , 'Vous avez deja participe');
        $reject="true" ;

        return new Response("error") ;


    }
// ********** salle + cours
    /**
     * @Route("/displaySalleMobile", name="displaysalleMobile")
     */
    public function displaysalleMobile(Request $request, SerializerInterface $serializer): Response
    {
        $salle = $this->getDoctrine()->getRepository(Salle::class)->findAll();
        $formatted = $serializer->normalize($salle,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;
    }
    /**
     * @Route("/SallecoursMobile", name="SallecoursMobile")
     */
    public function SallecoursFront(Request $request, SerializerInterface $serializer): Response
    {
        $id=$request->query->get("id") ;

        $entityManager = $this->getDoctrine()->getManager();
        $salle = $entityManager->getRepository(Cours::class)->findBy(array('salleassocie' => $id));

        $formatted = $serializer->normalize($salle,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;

    }
    /**
     * @route ("/likeMobile",name="likeMobile")
     */
    public function likeMobile(SalleLikeRepository $likeRepo ,Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user= $entityManager->getRepository(User::class)->find($request->get("id"));

        $salle= $entityManager->getRepository(Salle::class)->find($request->get("salleid"));

        if ($salle->isLikedByUser($user)) {
            $like = $likeRepo->findOneBy([
                'salle' => $salle,
                'user' => $user]);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($like);
            $entityManager->flush();

            return new JsonResponse("salle unlikee", 200);

        }
        $like = new SalleLike();
        $like->setSalle($salle)
            ->setUser($user);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($like);
        $entityManager->flush();
        $response = new JsonResponse();
        return new JsonResponse("salle likee", 200);
    }

    /**
     * @Route("/ajoutSalleMobile", name="ajoutSalleMobile")
     */
    public function ajoutSalleMobile(Request $request, SerializerInterface $serializer): Response
    {
        $em= $this->getDoctrine()->getManager();
        $nom=$request->get('nom');
        $adresse=$request->get('adresse');
        $tel=$request->get('tel');
        $mail=$request->get('mail');
        $description=$request->get('description');


        $image=$request->get('image');

        $salle=new salle();
        $salle->setNom($nom);
        $salle->setAdresse($adresse);
        $salle->setTel($tel);
        $salle->setMail($mail);
        $salle->setDescription($description);


        $salle->setImage("untitled.jpg");


        $em->persist($salle);
        $em->flush();
        $formatted = $serializer->normalize($salle,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;
    }
    /**
     * @Route("/ModifierSalleMobile", name="ModifierSalleMobile")
     */
    public function ModifierSalleMobile(Request $request, SerializerInterface $serializer): Response
    {
        $em= $this->getDoctrine()->getManager();
        $id=$request->get('id');
        $salle=$em->getRepository(Salle::class)->find($id);
        $nom=$request->get('nom');
        $adresse=$request->get('adresse');
        $tel=$request->get('tel');
        $mail=$request->get('mail');
        $description=$request->get('description');

     //   $image=$request->get('image');
        $salle->setNom($nom);
        $salle->setAdresse($adresse);
        $salle->setTel($tel);
        $salle->setMail($mail);
        $salle->setDescription($description);


        $salle->setImage("untitled.jpg");
        $em->persist($salle);
        $em->flush();
        $formatted = $serializer->normalize($salle,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;
    }
    /**
     * @Route("/SupprimerSalleMobile", name="SupprimerSalleMobile")
     */
    public function SupprimerSalleMobile(Request $request, SerializerInterface $serializer): Response
    {
        $em= $this->getDoctrine()->getManager();
        $id=$request->get('id');
        $salle=$em->getRepository(Salle::class)->find($id);
        $em->remove($salle);
        $em->flush();
        return new Response("deleted successfuly") ;
    }
    /**
     * @Route("/ajoutCoursMobile", name="ajoutCoursMobile")
     */
    public function ajoutCoursMobile(Request $request, SerializerInterface $serializer): Response
    {
        $em= $this->getDoctrine()->getManager();
        $nom=$request->get('nom');
        $nomCoach=$request->get('nomCoach');
        $description=$request->get('description');
        $nombre=$request->get('nombre');
        $salleassocie=$request->get('salleassocie');
        $jour=$request->get('jour');

        $salle = $em->getRepository(Salle::class)->find($request->get("salleassocie"));
        $cours=new cours();
        $cours->setNom($nom);
        $cours->setNomCoach($nomCoach);
        $cours->setDescription($description);
        $cours->setNombre($nombre);
        $s=$em->getRepository(Salle::class)->find(10);
        $cours->setSalleassocie($salle);
        $cours->setJour($jour);
        $cours->setImage("untitled.jpg");


        $em->persist($cours);
        $em->flush();
        $formatted = $serializer->normalize($cours,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;
    }
    /**
     * @Route("/ModifierCoursMobile", name="ModifierCoursMobile")
     */
    public function ModifierCoursMobile(Request $request, SerializerInterface $serializer): Response
    {
        $em= $this->getDoctrine()->getManager();
        $id=$request->get('id');
        $cours=$em->getRepository(Cours::class)->find($id);

        $nom=$request->get('nom');
        $nomCoach=$request->get('nomCoach');
        $description=$request->get('description');
        $nombre=$request->get('nombre');
        // $salleassocie=$request->get('salleassocie');
        $jour=$request->get('jour');

        //   $image=$request->get('image');

        $cours->setNom($nom);
        $cours->setNomCoach($nomCoach);
        $cours->setDescription($description);
        $cours->setNombre($nombre);
        $s=$em->getRepository(Salle::class)->find(10);
        //  $cours->setSalleassocie($s);
        $cours->setJour($jour);
        $cours->setImage("untitled.jpg");


        $em->persist($cours);
        $em->flush();
        $formatted = $serializer->normalize($cours,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;
    }
    /**
     * @Route("/SupprimerCoursMobile", name="SupprimerCoursMobile")
     */
    public function SupprimerCoursMobile(Request $request, SerializerInterface $serializer): Response
    {
        $em= $this->getDoctrine()->getManager();
        $id=$request->get('id');
        $cours=$em->getRepository(Cours::class)->find($id);
        $em->remove($cours);
        $em->flush();
        return new Response("deleted successfuly") ;
    }
    /**
     * @Route("/SallecoursMobileBack", name="SallecoursMobileBack")
     */
    public function SallecoursMobileBack(Request $request, SerializerInterface $serializer): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $salle = $entityManager->getRepository(Cours::class)->findAll();
        $formatted = $serializer->normalize($salle,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;

    }
    // ******** categorie mobile
    /**
     * @Route("/displaycategorieProduitMobile", name="displaycategorieProduitMobile")
     */
    public function displaycategorieProduitMobile(Request $request, SerializerInterface $serializer): Response
    {
        $categories = $this->getDoctrine()->getRepository(Categories::class)->findAll();
        $formatted = $serializer->normalize($categories,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;
    }
    /**
     * @Route("/addcategoriesMobile", name="addcategoriesMobile")
     */
    public function addcategoriesMobile(Request $request, SerializerInterface $serializer): Response
    {

        $categories = new categories();
        $nom=$request->query->get("nom") ;
        $categories->setNom($nom) ;
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($categories);
        $entityManager->flush();

        $formatted = $serializer->normalize($categories,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;



    }
    /**
     * @Route("/deletecategoriesMobile", name="deletecategoriesMobile")
     */
    public function deletecategoriesMobile(Request $request, SerializerInterface $serializer): Response
    {
        $id=$request->query->get("id") ;
        $entityManager = $this->getDoctrine()->getManager();
        $Categories = $entityManager->getRepository(Categories::class)->find($id);
        if($Categories!=null){
            $entityManager->remove($Categories);
            $entityManager->flush();
            $formatted = $serializer->normalize($Categories,'json',['groups' => 'post:read']);
            return new Response(json_encode($formatted)) ;

        }


        return new Response(" categorie invalide") ;
    }
    /**
     * @Route("/updatecategoriesMobile", name="updatecategoriesMobile")
     */
    public function updatecategoriesMobile(Request $request, SerializerInterface $serializer): Response
    {

        $entityManager = $this->getDoctrine()->getManager();

        $categories = $entityManager->getRepository(Categories::class)->find($request->get("id"));
        $nom=$request->query->get("nom") ;
        $categories->setNom($nom) ;
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($categories);
        $entityManager->flush();

        $formatted = $serializer->normalize($categories,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;
    }
    // ***** produit
    /**
     * @Route("/displayProduitMobile", name="displayProduitMobile")
     */
    public function displayProduitMobile(Request $request, SerializerInterface $serializer): Response
    {
        $prod= $this->getDoctrine()->getRepository(Produit::class)->findAll();
        $formatted = $serializer->normalize($prod,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;
    }
    /**
     * @Route("/displayProduitMobilebyCateg", name="displayProduitMobilebyCateg")
     */
    public function displayProduitMobilebyCateg(Request $request, SerializerInterface $serializer): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
$id=$request->get("id");
        $categories = $entityManager->getRepository(Categories::class)->find($request->get("id"));

        $prod= $this->getDoctrine()->getRepository(Produit::class)->findBy(array('categories'=>$id));
        $formatted = $serializer->normalize($prod,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;
    }
    /**
     * @Route("/addProduitMobile", name="addProduitMobile")
     */
    public function addProduitMobile(Request $request, SerializerInterface $serializer): Response
    {

        $categories = new Produit();
        $nom=$request->query->get("nom") ;
        $description=$request->query->get("description") ;
        $prix=$request->query->get("prix") ;
        $quantite=$request->query->get("quantite") ;
        $image=$request->query->get("image") ;
       // $categorie=$request->query->get("categorie") ;
        $categories->setNom($nom) ;
        $categories->setPrix($prix) ;
        $categories->setQuantite($quantite) ;
        $categories->setDescription($description) ;
        $categories->setImage($image) ;
        $entityManager = $this->getDoctrine()->getManager();
        $categs = $entityManager->getRepository(Categories::class)->find($request->get("categorie"));

        //$categ=$entityManager->getRepository(Categories::class)->find($categorie) ;
        $categories->addCategory($categs) ;

        $entityManager->persist($categories);
        $entityManager->flush();

        $formatted = $serializer->normalize($categories,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;
    }
    /**
     * @Route("/updateProduitMobile", name="updateProduitMobile")
     */
    public function updateProduitMobile(Request $request, SerializerInterface $serializer): Response
    {
        $id=$request->query->get("id") ;

        $entityManager = $this->getDoctrine()->getManager();
        $categories = $entityManager->getRepository(Produit::class)->find($request->get("id"));

        $nom=$request->query->get("nom") ;
        $description=$request->query->get("description") ;
        $prix=$request->query->get("prix") ;
        $quantite=$request->query->get("quantite") ;
        $image=$request->query->get("image") ;
        $categories->setNom($nom) ;
        $categories->setPrix($prix) ;
        $categories->setQuantite($quantite) ;
        $categories->setDescription($description) ;
        $categories->setImage($image) ;
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($categories);
        $entityManager->flush();

        $formatted = $serializer->normalize($categories,'json',['groups' => 'post:read']);
        return new Response(json_encode($formatted)) ;
    }
    /**
     * @Route("/deleteProduitMobile", name="deleteProduitMobile")
     */
    public function deleteProduitMobile(Request $request, SerializerInterface $serializer): Response
    {
        $id=$request->query->get("id") ;
        $entityManager = $this->getDoctrine()->getManager();
        $Categories = $entityManager->getRepository(Produit::class)->find($id);
        if($Categories!=null){
            $entityManager->remove($Categories);
            $entityManager->flush();
            $formatted = $serializer->normalize($Categories,'json',['groups' => 'post:read']);
            return new Response(json_encode($formatted)) ;

        }


        return new Response(" Produit invalide") ;
    }
}
