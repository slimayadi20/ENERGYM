<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormModifyType;
use App\Form\GerantFormType;
use App\Repository\UserRepository;
use App\Form\UserFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        if ($error) {
            $this->addFlash("danger", "Informations incorrectes : soit vous avez commis une erreur d'identifiants, soit votre compte n'est pas valide (adresse mail non validée, compte suspendu ...)");
        }
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
    /**
     * @Route("/signup", name="signup")
     */
    public function signup(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();
        $form = $this->createForm(GerantFormType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager = $this->getDoctrine()->getManager();
            $user->setRoles('ROLE_GERANT');
            $user->setStatus(2);
            $user->setCreatedAt(new \DateTime()) ;
            $passwordcrypt = $encoder->encodePassword($user,$user->getPassword());
            $user->setPassword($passwordcrypt);
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->render("security/Confirm.html.twig");
        }
        return $this->render("security/Register.html.twig", [
            "form_title" => "Ajouter un gerant",
            "form_user" => $form->createView(),
        ]);
    }
    /**
     * @Route("/loginFront", name="loginFront")
     */
    public function loginFront(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/loginFront.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {

        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');

    }
}
