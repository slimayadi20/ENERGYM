<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormModifyType;
use App\Form\ConfirmCodeVerifType;
use App\Form\GerantFormType;
use App\Repository\UserRepository;
use App\Form\UserFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Routing\RouterInterface;

class SecurityController extends AbstractController
{
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {



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
            $user->setRoles('ROLE_GERANT');
            $user->setStatus(2);
            $user->setCreatedAt(new \DateTime()) ;
            $passwordcrypt = $encoder->encodePassword($user,$user->getPassword());
            $user->setPassword($passwordcrypt);
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->render("security/Email.html.twig");
        }
        return $this->render("security/Register.html.twig", [
            "form_title" => "Ajouter un gerant",
            "form_user" => $form->createView(),
        ]);
    }
    /**
     * @Route("/signupUser", name="signupUser")
     */
    public function signupUser(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();
        $form = $this->createForm(GerantFormType::class,$user);
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
            $user->setRoles('ROLE_USER');
            $user->setStatus(2);
            $user->setCreatedAt(new \DateTime()) ;
            $passwordcrypt = $encoder->encodePassword($user,$user->getPassword());
            $random = random_int(1000, 9000);
            $user->setVerificationCode($random);
            $user->setPassword($passwordcrypt);
            $entityManager->persist($user);
            $entityManager->flush();
            $basic  = new \Vonage\Client\Credentials\Basic("e75f3672", "PU1UcHA3ydMKUvvf");
            $client = new \Vonage\Client($basic);
            $response = $client->sms()->send(
                new \Vonage\SMS\Message\SMS("21695590010", "energym", $random)
            );

            $message = $response->current();

            if ($message->getStatus() == 0) {
                echo "The message was sent successfully\n";
            } else {
                echo "The message failed with status: " . $message->getStatus() . "\n";
            }
            return new RedirectResponse($this->router->generate('verificationCode'));


        }
        return $this->render("security/Register.html.twig", [
            "form_title" => "Ajouter un user",
            "form_user" => $form->createView(),
        ]);
    }
    /**
     * @Route("/verificationCode", name="verificationCode")
     */
    public function VerificationCode(Request $request,UserRepository $repository): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $form = $this->createForm(ConfirmCodeVerifType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //recupere les donnes
            $donnees = $form->getData();
            //on cherche si lutilisateur a cet email
            $user = $repository->findOneByEmail($donnees['email']);

            if (!$user) {
                $this->addFlash('danger', 'cette adresse nexiste pas');
                $this->redirectToRoute('app_login');
            }
            if ($user->getVerificationCode()==$donnees['VerificationCode'])
            {
                $user->setStatus(1);
                $user->setVerificationCode(NULL);
                $entityManager->flush();
                return new RedirectResponse($this->router->generate('app_login'));

            }

        }
        return $this->render("security/Confirm.html.twig", [
            "form_title" => "Ajouter un user",
            "form" => $form->createView(),
        ]);


    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {

        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');

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
