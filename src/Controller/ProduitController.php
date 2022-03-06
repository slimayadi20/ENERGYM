<?php

namespace App\Controller;
use App\Entity\Produit;
use App\Entity\Categories;
use App\Entity\User;
use App\Form\ProduitFormType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Repository\ProduitRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
class ProduitController extends AbstractController
{
    /**
     * @Route("/dashboard/produit", name="produit")
     */
    public function index( PaginatorInterface $paginator,ProduitRepository $repository , Request $request): Response
    {
        $utilisateur = $this->getUser();
        $utilisateurid = $utilisateur->getId();
        if(in_array('ROLE_GERANT', $utilisateur->getRoles())){
            $produit =  $paginator->paginate(
                $repository->findGerantProduitwithpagination($utilisateurid),
                $request->query->getInt('page' , 1), // nombre de page
                3//nombre limite
            );
            return $this->render('produit/index.html.twig', [
                'controller_name' => 'ProduitController',
                "produit"=>$produit,
            ]);
        }
        else if(in_array('ROLE_ADMIN', $utilisateur->getRoles())){
            $produit =  $paginator->paginate(
                $repository->findallwithpagination(),
                $request->query->getInt('page' , 1), // nombre de page
                3//nombre limite
            );
            return $this->render('produit/index.html.twig', [
                'controller_name' => 'ProduitController',
                "produit"=>$produit,
            ]);
        }
        return $this->redirectToRoute('dashboard');

    }
    /**
     * @Route("/dashboard/addproduit", name="addproduit")
     */
    public function addproduit(Request $request, \Swift_Mailer $mailer): Response
    {
        $utilisateur = $this->getUser();
        $produit = new produit();
        $form = $this->createForm(ProduitFormType::class,$produit);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $produit->setUser($utilisateur);

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
            $message = (new \Swift_Message('!!!NEW PRODUCT!!!'))
                //ili bech yeb3ath
                ->setFrom('projetenergym@gmail.com')
                //ili bech ijih l message
                ->setTo('fedi.benmansour@esprit.tn')
                ->setBody(
                    "<p>bonjour, </p><p> un nouveau produit est ajoutée go check it on Energym.com</p> veuillez cliquer sur le lien suivant http://127.0.0.1:8000/shop?page=1</a> " ,
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
     * @Route("/dashboard/modifyproduit/{id}/{idU}/", name="modifyproduit")
     * @ParamConverter("Produit", options={"mapping": {"id" : "id"}})
     * @ParamConverter("UserA", options={"mapping": {"idU"   : "id"}})
     * @Template()
     */
    public function modifyproduit(Produit $prod,User $UserA,Request $request,  Session $session): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $id = $prod->getId();
        $idUser = $UserA->getId();
        $user = $this->getUser();

        if($user->getId() != $idUser )
        {
            $this->addFlash('error' , 'You cant edit anotherone');
            $session->set("message", "Vous ne pouvez pas modifier cette salle");
            return $this->redirectToRoute('produit');

        }
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

    /**
     * @Route("/test", name="test")
     */
    public function indexAction(Request $request )
    {
        $data = $this->getDoctrine()->getRepository(Produit::class)->findAll();
        //$dest = [];
        $dest = array();

        foreach ($data as $x)
        {
            //$dest[] = [$x->getDestination()] ;
            array_push($dest,$x->getNom() );
        }
        /*
        $destinations=array(); ;
        foreach ($data as $x)
        {
            $dest = $x->getDestination() ;
            array_push($destinations,$dest) ;
        }
        */

        if ($request->isMethod("POST") ) {

            $nom= $request->get('searchbar') ;
            if ($nom!=NULL) {
                $data =  $this->getDoctrine()->getRepository(Produit::class)->findBy(array('nom'=>$nom));
                if ($data == NULL) {
                    $data = $this->getDoctrine()->getRepository(Produit::class)->findAll();
                }
            }
            else {
                $data = $this->getDoctrine()->getRepository(Produit::class)->findAll();
            }

        }


        $array_dest_occ = array_count_values($dest) ;

        //['sadasd'=>2 , 'sadsad'=>5] ;

        /*foreach ($dest as $x)
        {
            if (array_search($x , $dest)!=-1  ) {
                $array_dest_occ[] = [$x , ]
            }

        }*/
        $final= [
            ['produit ' , 'nom']

        ] ;
        //$array_dest_occ["Germany"];

        foreach($array_dest_occ as $x=>$x_value)
        {
            $final[] = [$x , (int)$x_value] ;
        }


        // charrtt
        $pieChart = new PieChart();
        /*$pieChart->getData()->setArrayToDataTable(
            [
                ['Country', 'Number of offres'],
                ['Work',     11],
                ['Eat',      2],
                ['Commute',  2],
                ['Watch TV', 2],
                ['Sleep',    7]
            ]
        );*/

        $pieChart->getData()->setArrayToDataTable($final) ;

        $pieChart->getOptions()->setTitle('Representation des prix ');
        $pieChart->getOptions()->setHeight(500);
        $pieChart->getOptions()->setWidth(700);
        $pieChart->getOptions()->getTitleTextStyle()->setBold(true);
        $pieChart->getOptions()->getTitleTextStyle()->setColor('#009900');
        $pieChart->getOptions()->getTitleTextStyle()->setItalic(true);
        $pieChart->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $pieChart->getOptions()->getTitleTextStyle()->setFontSize(20);
        //
        /*
            return $this->render('offre/view.html.twig' ,  [
                'list' => $data
                //'list_dest' => $dest

                //'destinationx' =>$destinations
            ]   ) ;
        */
        return $this->render('/test.html.twig' ,
            array(  'piechart' => $pieChart ,
                'list'=>$data ,
                'test'=>$final
            )
        ) ;
     //   return $this->render('produit/test.html.twig', array('piechart' => $pieChart));
    }
}