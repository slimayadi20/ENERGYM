<?php

namespace App\Controller;
use App\Entity\Salle;
use App\Form\SalleFormType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Repository\SalleRepository;


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
            1 //nombre limite
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
     * @Route("/dashboard/modifySalle/{id}", name="modifySalle")
     */
    public function modifySalle(Request $request, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $salle = $entityManager->getRepository(salle::class)->find($id);
        $form = $this->createForm(SalleFormType::class, $salle);
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


}
