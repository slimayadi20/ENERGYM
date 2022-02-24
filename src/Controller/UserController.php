<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormModifyType;
use App\Repository\UserRepository;
use App\Form\UserFormType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class UserController extends AbstractController
{


    /**
     * @Route("/dashboard/user", name="user")
     */
    public function index( Session $session, PaginatorInterface $paginator,UserRepository $repository , Request $request): Response
    {

        $utilisateur = $this->getUser();

        if(!$utilisateur)
        {
            $session->set("message", "Merci de vous connecter");
            return $this->redirectToRoute('app_login');
        }

        else if(in_array('ROLE_ADMIN', $utilisateur->getRoles())){
            $users =  $paginator->paginate(
                $repository->findallwithpagination(),
                $request->query->getInt('page' , 1), // nombre de page
                3 //nombre limite
            );
            return $this->render('user/afficher.html.twig', [
                "users" => $users,
            ]);
        }
        else if(in_array('ROLE_GERANT', $utilisateur->getRoles())){
            $users =  $paginator->paginate(
                $repository->findUserGerantwithpagination(),
                $request->query->getInt('page' , 1), // nombre de page
                3 //nombre limite
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
    public function verif(Session $session, PaginatorInterface $paginator,UserRepository $repository, Request $request): Response
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
    public function addUser(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $utilisateur = $this->getUser();


        if (in_array('ROLE_ADMIN', $utilisateur->getRoles())) {

        $user = new User();
        $form = $this->createForm(UserFormType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
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
    public function modifyUser(Request $request, int $id, Session $session, UserPasswordEncoderInterface $encoder): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        if($user->getId() != $id )
        {
            $this->addFlash('error' , 'You cant edit anotherone');
            $session->set("message", "Vous ne pouvez pas modifier cet utilisateur");
            return $this->redirectToRoute('user');
        }
        $form = $this->createForm(UserFormModifyType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
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
    public function Profile( Session $session): Response
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
    public function deleteUser(int $id, Session $session): Response
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
    public function modifyStatus(Request $request, int $id): Response
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
}
