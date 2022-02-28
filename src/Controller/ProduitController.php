<?php

namespace App\Controller;
use App\Entity\Produit;
use App\Form\ProduitFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProduitController extends AbstractController
{
    /**
     * @Route("/dashboard/produit", name="produit")
     */
    public function index(): Response
    {
        $produit = $this->getDoctrine()->getRepository(Produit::class)->findAll();
        return $this->render('produit/index.html.twig', [
            'controller_name' => 'ProduitController',
            "produit"=>$produit,
        ]);
    }
    /**
     * @Route("/dashboard/addproduit", name="addproduit")
     */
    public function addproduit(Request $request, \Swift_Mailer $mailer): Response
    {
        $produit = new produit();
        $form = $this->createForm(ProduitFormType::class,$produit);
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
                    $this->getParameter('imagesProduct_directory'),
                    $fileName
                );

                // updates the 'product' property to store the image file name
                // instead of its contents
                $produit->setImage($fileName);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($produit);
            $entityManager->flush();
            $user=$this->getUser();
            $message = (new \Swift_Message('Mot de passe oublie'))
                //ili bech yeb3ath
                ->setFrom('fedi.benmansour@esprit.tn')
                //ili bech ijih l message
                ->setTo('fedi.benmansour@esprit.tn')
                ->setBody(
                    "<p>bonjour, </p><p> une demande de reinitialisation de mot de passe a ete effectue</p> veuillez cliquer sur le lien suivant</a> " ,
                    'text/html'
                )
            ;
            //on envoi l email
            $mailer->send($message) ;
            $this->addFlash('success' , 'L"action a été effectué');
            return $this->redirectToRoute("produit");

        }
        return $this->render("produit/ajouter.html.twig", [
            "form_title" => "Ajouter un produit",
            "form_produit" => $form->createView(),
        ]);
    }
    /**
     * @Route("/dashboard/modifyproduit/{id}", name="modifyproduit")
     */
    public function modifyproduit(Request $request, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $produit = $entityManager->getRepository(produit::class)->find($id);
        $form = $this->createForm(produitFormType::class, $produit);
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
                    $this->getParameter('imagesProduct_directory'),
                    $fileName
                );

                // updates the 'product' property to store the image file name
                // instead of its contents
                $produit->setImage($fileName);
            }
            $entityManager->flush();
            $this->addFlash('success' , 'L"action a été effectué');
            return $this->redirectToRoute("produit");
        }

        return $this->render("produit/modifier.html.twig", [
            "form_title" => "Modifier un produit",
            "form_produit" => $form->createView(),
        ]);
    }
    /**
     * @Route("/dashboard/deleteproduit/{id}", name="deleteproduit")
     */
    public function deleteproduit(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $produit = $entityManager->getRepository(produit::class)->find($id);
        $entityManager->remove($produit);
        $entityManager->flush();
        $this->addFlash('success' , 'L"action a été effectué');


        return $this->redirectToRoute("produit");
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