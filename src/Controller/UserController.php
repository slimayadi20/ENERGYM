<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormModifyType;
use App\Form\GerantFormType;
use App\Repository\UserRepository;
use App\Form\UserFormType;
use App\Form\ResetPassType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;


class UserController extends AbstractController
{


    /**
     * @Route("/dashboard/user", name="user")
     */
    public function index( Session $session, PaginatorInterface $paginator,UserRepository $repository , Request $request)
    {

        $utilisateur = $this->getUser();
        $SalleId= $utilisateur->getIdSalle() ;

        if(!$utilisateur)
        {
            $session->set("message", "Merci de vous connecter");
            return $this->redirectToRoute('app_login');
        }

        else if(in_array('ROLE_ADMIN', $utilisateur->getRoles())){
            $users =  $paginator->paginate(
                $repository->findallwithpagination(),
                $request->query->getInt('page' , 1), // nombre de page
                10 //nombre limite
            );
            return $this->render('user/afficher.html.twig', [
                "users" => $users,
            ]);
        }
        else if(in_array('ROLE_GERANT', $utilisateur->getRoles())){
            $users =  $paginator->paginate(
                $repository->findUserGerantwithpagination($SalleId),
                $request->query->getInt('page' , 1), // nombre de page
                10 //nombre limite
            );
            return $this->render('user/afficher.html.twig', [
                "users" => $users,
            ]);
        }

        return $this->redirectToRoute('dashboard');


    }
    /**
     * @Route("/dashboard/verif", name="verif")
     */
    public function verif(Session $session, PaginatorInterface $paginator,UserRepository $repository, Request $request)
    {
        $utilisateur = $this->getUser();


        if (in_array('ROLE_ADMIN', $utilisateur->getRoles())) {

            $users =  $paginator->paginate(
                $repository->findallwithpaginationVerif(),
                $request->query->getInt('page' , 1), // nombre de page
                10 //nombre limite
            );
            return $this->render('user/afficher.html.twig', [
                'controller_name' => 'UserController',
                "users" => $users,

            ]);
        }
        return $this->redirectToRoute('dashboard');
    }

    /**
     * @Route("/dashboard/addUser", name="addUser")
     */
    public function addUser(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $utilisateur = $this->getUser();


        if (in_array('ROLE_ADMIN', $utilisateur->getRoles())) {



        $user = new User();
        $form = $this->createForm(UserFormType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('imageFile')->getData();
            // this condition is needed because the 'image' field is not required

            if ($imageFile) {
                // generate new name to the file image with the function generateUniqueFileName
                $fileName = $this->generateUniqueFileName().'.'.$imageFile->guessExtension();

                // moves the file to the directory where products are stored
                $imageFile->move(
                    $this->getParameter('imagesUser_directory'),
                    $fileName
                );

                // updates the 'product' property to store the image file name
                // instead of its contents
                $user->setImagefile($fileName);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $user->setRoles('ROLE_ADMIN');
            $user->setStatus(1);
            $user->setCreatedAt(new \DateTime()) ;
            $passwordcrypt = $encoder->encodePassword($user,$user->getPassword());
            $user->setPassword($passwordcrypt);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success' , 'L"action a été effectué');
            return $this->redirectToRoute("user");

            }
            return $this->render("user/ajouter.html.twig", [
                "form_title" => "Ajouter un user",
                "form_user" => $form->createView(),
            ]);
        }
        return $this->redirectToRoute('dashboard');
    }
    /**
     * @Route("/dashboard/modifyUser/{id}", name="modifyUser")
     */
    public function modifyUser(Request $request, int $id, Session $session, UserPasswordEncoderInterface $encoder)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        if($user->getId() != $id )
        {
            $this->addFlash('error' , 'You cant edit anotherone');
            $session->set("message", "Vous ne pouvez pas modifier cet utilisateur");
            return $this->redirectToRoute('user');
        }
        $form = $this->createForm(GerantFormType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('imageFile')->getData();
            // this condition is needed because the 'image' field is not required

            if ($imageFile) {
                // generate new name to the file image with the function generateUniqueFileName
                $fileName = $this->generateUniqueFileName().'.'.$imageFile->guessExtension();

                // moves the file to the directory where products are stored
                $imageFile->move(
                    $this->getParameter('imagesUser_directory'),
                    $fileName
                );

                // updates the 'product' property to store the image file name
                // instead of its contents
                $user->setImagefile($fileName);
            }
            $passwordcrypt = $encoder->encodePassword($user,$user->getPassword());
            $user->setPassword($passwordcrypt);
            $entityManager->flush();
            $this->addFlash('success' , 'L"action a été effectué');
            return $this->redirectToRoute("dashboard");
        }

        return $this->render("user/modifier.html.twig", [
            "form_title" => "Modifier un user",
            "f" => $form->createView(),
        ]);
    }

    /**
     * @Route("/dashboard/Profile", name="Profile")
     */
    public function Profile( Session $session)
    {
        // $users = $this->getDoctrine()->getRepository(User::class)->find();
        $user = $this->getUser();

        return $this->render('user/profile.html.twig', [
            'controller_name' => 'UserController',
            'user'=>$user

        ]);
    }
    /**
     * @Route("/dashboard/deleteUser/{id}", name="deleteUser")
     */
    public function deleteUser(int $id, Session $session)
    {
        $user = $this->getUser();
        if($user->getId() == $id )
        {
            $this->addFlash('error' , 'You cant delete yourself');
            $session->set("message", "Vous ne pouvez pas supprimer votre compte");
            return $this->redirectToRoute('user');
        }
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);
        $entityManager->remove($user);
        $entityManager->flush();
        $this->addFlash('success' , 'L"action a été effectué');


        return $this->redirectToRoute("user");
    }

    /**
     * @Route("/dashboard/modifyStatus/{id}", name="modifyStatus")
     */
    public function modifyStatus(Request $request, int $id)
    {
        $utilisateur = $this->getUser();


        if (in_array('ROLE_ADMIN', $utilisateur->getRoles())) {
            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->find($id);
            if($user->getStatus()==0)
            {
                $user->setStatus(1);

            }
            else if ($user->getStatus()==1)
            {
                $user->setStatus(2);

            }
            else if ($user->getStatus()==2){
                $user->setStatus(0);

            }
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'L action a été effectué');
            return $this->redirectToRoute("user");
        }
        return $this->redirectToRoute('dashboard');
    }

    /**
     * @Route("/oubli-pass", name="app-forgotten-password")
     */
    public function forgottenPass(Request $request, UserRepository $repository, \Swift_Mailer $mailer, TokenGeneratorInterface $tokenGenerator)
    {
        $form = $this->createForm(ResetPassType::class) ;
        $form->handleRequest($request) ;
        if($form->isSubmitted() && $form->isValid()){
            //recupere les donnes
            $donnees = $form->getData() ;
            //on cherche si lutilisateur a cet email
            $user = $repository->findOneByEmail($donnees['email']) ;

            if(!$user){
                $this->addFlash('danger','cette adresse nexiste pas') ;
                $this->redirectToRoute('app_login') ;
            }

            //on genere un token
            $token = $tokenGenerator->generateToken() ;
            try{
                $user->setResetToken($token) ;
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
            }catch (\Exception $exception) {
                $this->addFlash('warning', 'une erreure est survenue : ' . $exception->getMessage());
                $this->redirectToRoute('app_login') ;
            }

            //on genere lurl du mot de passe
            $url= $this->generateURL('app_reset_password',['token'=>$token],
                UrlGeneratorInterface::ABSOLUTE_URL) ;

            //on envoie le message
            $message = (new \Swift_Message('Mot de passe oublie'))
                //ili bech yeb3ath
                ->setFrom('slim.ayadi@esprit.tn')
                //ili bech ijih l message
                ->setTo($user->getEmail())
                ->setBody(
                    "<p>bonjour, </p><p> une demande de reinitialisation de mot de passe a ete effectue</p> <a href='$url '> veuillez cliquer sur le lien suivant</a> " ,
                    'text/html'
                )
            ;
            //on envoi l email
            $mailer->send($message) ;
            $this->addFlash('success','un email de reinitialisation de mot de passe vous a ete envoye') ;
            return $this->redirectToRoute('app_login') ;
        }
        return $this->render('security/forgotten_password.html.twig', ['emailForm'=>$form->createView()]) ;
    }
    /**
     * @Route("/reset_pass/{token}", name="app_reset_password")
     */
    public function resetPassword($token, Request $request, UserPasswordEncoderInterface $encoder){
        //on chercher lutilisateur avec le token
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['reset_token'=>$token]) ;
        if(!$user){
            $this->addFlash('danger','token inconnu') ;
            return $this->redirectToRoute('app_login') ;
        }
        if($request->isMethod('POST') and ($request->request->get('confirmPass')==$request->request->get('password'))) {
            $user->setResetToken(null);
            $user->setPassword($encoder->encodePassword($user,$request->request->get('password')));
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success','mot de passe modifiee avec succes') ;

            return $this->redirectToRoute('app_login') ;
        }else{
            return $this->render('user/reset_password.html.twig',['token'=>$token]) ;
        }
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

}
