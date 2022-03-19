<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Livraison;
use App\Entity\Commande;
use App\Form\LivraisonFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Lcobucci\JWT\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use App\Entity\User ;
use App\Entity\Reclamation;

class MobileController extends AbstractController
{
    /**
     * @Route("/displaylivraisonMobile", name="displaylivraisonMobile")
     */
    public function displaylivraisonMobile(Request $request, SerializerInterface $serializer): Response
    {
        $livraison = $this->getDoctrine()->getRepository(Livraison::class)->findAll();
        $formatted = $serializer->normalize($livraison, 'json', ['groups' => 'post:read']);
        return new Response(json_encode($formatted));
    }

    /**
     * @Route("/displayCommandesMobile", name="displayCommandesMobile")
     */
    public function displayCommandesMobile(Request $request, SerializerInterface $serializer): Response
    {
        //$uid = $this->getUser()->getId();
        $id = $request->query->get('id');
        $commande = $this->getDoctrine()->getRepository(Commande::class)->findCommandeUser($id);
        $formatted = $serializer->normalize($commande, 'json', ['groups' => 'post:read']);
        return new Response(json_encode($formatted));
    }

    /**
     * @Route("/addlivraisonMobile", name="addlivraisonMobile")
     */
    public function addlivraisonMobile(Request $request, SerializerInterface $serializer): Response
    {

        $livraison = new Livraison();
        $nomLivreur = $request->query->get("nomLivreur");
        $etat = $request->query->get("etat");
        $idCommande = $request->query->get("idCommande");
        $livraison->setIdCommande($idCommande);
        $livraison->setNomLivreur($nomLivreur);
        $livraison->setEtat($etat);
        $livraison->setDateLivraison(new \DateTime());
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($livraison);
        $entityManager->flush();

        $formatted = $serializer->normalize($livraison, 'json', ['groups' => 'post:read']);
        return new Response(json_encode($formatted));


    }

    /**
     * @Route("/deletelivraisonMobile", name="deletelivraisonMobile")
     */
    public function deletelivraisonMobile(Request $request, SerializerInterface $serializer): Response
    {
        $id = $request->query->get("id");
        $entityManager = $this->getDoctrine()->getManager();
        $livraison = $entityManager->getRepository(Livraison::class)->find($id);
        if ($livraison != null) {
            $entityManager->remove($livraison);
            $entityManager->flush();
            $formatted = $serializer->normalize($livraison, 'json', ['groups' => 'post:read']);
            return new Response(json_encode($formatted));

        }


        return new Response("la livraison est invalide");
    }

    /**
     * @Route("/updatelivraisonMobile", name="updatelivraisonMobile")
     */
    public function updatelivraisonMobile(Request $request, SerializerInterface $serializer): Response
    {

        $entityManager = $this->getDoctrine()->getManager();

        $livraison = $entityManager->getRepository(Livraison::class)->find($request->get("id"));
        $nomLivreur = $request->query->get("nomLivreur");
        $etat = $request->query->get("etat");
        $idCommande = $request->query->get("idCommande");
        $livraison->setIdCommande($idCommande);
        $livraison->setNomLivreur($nomLivreur);
        $livraison->setEtat($etat);
        $livraison->setDateLivraison(new \DateTime());
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($livraison);
        $entityManager->flush();

        $formatted = $serializer->normalize($livraison, 'json', ['groups' => 'post:read']);
        return new Response(json_encode($formatted));


    }

    /**
     * @Route("/displayreclamationMobile", name="displayreclamationMobile")
     */
    public function displayreclamationMobile(Request $request, SerializerInterface $serializer): Response
    {
        $reclamation = $this->getDoctrine()->getRepository(Reclamation::class)->findAll();
        $formatted = $serializer->normalize($reclamation, 'json', ['groups' => 'post:read']);
        return new Response(json_encode($formatted));
    }

    /**
     * @Route("/addReclamationMobile", name="addReclamationMobile")
     */
    public function addReclamationMobile(Request $request, SerializerInterface $serializer): Response
    {

        $reclamation = new Reclamation();
        $titre = $request->query->get("titre");
        $contenu = $request->query->get("contenu");
        $reclamation->setStatut('Encours');
        $idUser = $request->query->get("NomUser");
        $em = $this->getDoctrine()->getManager();
        // hedhi fazet jointure
        $user = $em->getRepository(User::class)->find($idUser);
        $reclamation->setNomUser($user);
        // toufa lenna
        $reclamation->setTitre($titre);
        $reclamation->setContenu($contenu);
        $reclamation->setDateCreation(new \DateTime());
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($reclamation);
        $entityManager->flush();

        $formatted = $serializer->normalize($reclamation, 'json', ['groups' => 'post:read']);
        return new Response(json_encode($formatted));


    }

    /**
     * @Route("/deleteReclamationMobile", name="deleteReclamationMobile")
     */
    public function deleteReclamationMobile(Request $request, SerializerInterface $serializer): Response
    {
        $id = $request->query->get("id");
        $entityManager = $this->getDoctrine()->getManager();
        $reclamation = $entityManager->getRepository(reclamation::class)->find($id);
        if ($reclamation != null) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
            $formatted = $serializer->normalize($reclamation, 'json', ['groups' => 'post:read']);
            return new Response(json_encode($formatted));

        }


        return new Response("la reclamaton invalide");
    }

    /**
     * @Route("/updateReclamationMobile", name="updateReclamationMobile")
     */
    public function updateReclamationMobile(Request $request, SerializerInterface $serializer): Response
    {

        $entityManager = $this->getDoctrine()->getManager();

        $reclamation = $entityManager->getRepository(Reclamation::class)->find($request->get("id"));
        $titre = $request->query->get("titre");
        $contenu = $request->query->get("contenu");
        $reclamation->setStatut("hhh");
        $reclamation->setTitre($titre);
        $reclamation->setContenu($contenu);
        $reclamation->setDateCreation(new \DateTime());
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($reclamation);
        $entityManager->flush();

        $formatted = $serializer->normalize($reclamation, 'json', ['groups' => 'post:read']);
        return new Response(json_encode($formatted));


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
    public function signinAction(Request $request, SerializerInterface $serializer)
    {
        $email = $request->query->get("email");
        $password = $request->query->get("password");
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($user) {
            if ($password == $user->getPassword()) {
                $formatted = $serializer->normalize($user, 'json', ['groups' => 'post:read']);
                return new Response(json_encode($formatted));

            } else {

                return new Response("passowrd not found");
            }
        } else {
            return new Response("user not found");
        }

    }

    /**
     * @Route("/editUserMobile", name="editUserMobile")
     */
    public function editUserMobile(Request $request, SerializerInterface $serializer, UserPasswordEncoderInterface $encoder)
    {
        $id = $request->query->get("id");
        $email = $request->query->get("email");
        $nom = $request->query->get("nom");
        $prenom = $request->query->get("prenom");
        $password = $request->query->get("password");
        $phone = $request->query->get("phoneNumber");
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($request->files->get("imageFile") == null) {
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
        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return new JsonResponse("success", 200);
        } catch (\Exception $ex) {
            return new Response("fail" . $ex->getMessage());
        }


    }

    /**
     * @Route("/passwordMobile", name="passwordMobile")
     */
    public function getPasswordbyPhone(Request $request, SerializerInterface $serializer)
    {
        $phoneNumber = $request->query->get("phoneNumber");
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['phoneNumber' => $phoneNumber]);
        if ($user) {
            $password = $user->getPassword();
            $formatted = $serializer->normalize($password, 'json', ['groups' => 'post:read']);
            return new Response(json_encode($formatted));

        } else {
            return new Response("user not found");
        }
    }
}