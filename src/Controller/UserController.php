<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormModifyType;
use App\Repository\UserRepository;
use App\Form\UserFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index(): Response
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            "users" => $users,

        ]);
    }
    /**
     * @Route("/addUser", name="addUser")
     */
    public function addUser(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserFormType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $user->setRoles('ROLE_USER');
            $user->setStatus(true);
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
    /**
     * @Route("/modifyUser/{id}", name="modifyUser")
     */
    public function modifyUser(Request $request, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $user = $entityManager->getRepository(User::class)->find($id);
        $form = $this->createForm(UserFormModifyType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager->flush();
            $this->addFlash('success' , 'L"action a été effectué');
            return $this->redirectToRoute("user");
        }

        return $this->render("user/modifier.html.twig", [
            "form_title" => "Modifier un user",
            "form_user" => $form->createView(),
        ]);
    }
    /**
     * @Route("/deleteUser/{id}", name="deleteUser")
     */
    public function deleteUser(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);
        $entityManager->remove($user);
        $entityManager->flush();
        $this->addFlash('success' , 'L"action a été effectué');


        return $this->redirectToRoute("user");
    }

    /**
     * @Route("/modifyStatus/{id}", name="modifyStatus")
     */
    public function modifyStatus(Request $request, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($id);
        if($user->getStatus()==true)
        {
            $user->setStatus(false);
        }
        else
        {
            $user->setStatus(true);
        }
        $entityManager->persist($user);
        $entityManager->flush();
        $this->addFlash('success', 'L action a été effectué');
        return $this->redirectToRoute("user");
    }
}
