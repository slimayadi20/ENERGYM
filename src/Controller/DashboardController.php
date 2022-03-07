<?php

namespace App\Controller;

use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Repository\UserRepository;
use App\Entity\Produit;
use App\Entity\Notification;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(UserRepository $repository,Request $request): Response
    {






        // stats
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
        //   return $this->render('produit/test.html.twig', array('piechart' => $pieChart));
        $entityManager = $this->getDoctrine()->getManager();
        $notification = $entityManager->getRepository(Notification::class)->findAll();
        return $this->render('dashboard/index.html.twig',  array(  'piechart' => $pieChart ,
            'list'=>$data ,
            'test'=>$final,
            "notification" => $notification,
        ));
    }
    /**
     * @Route("/notification", name="notification")
     */
    public function notification(Session $session): Response
    {
        $utilisateur = $this->getUser();
        $SalleId= $utilisateur->getIdSalle() ;

        if(!$utilisateur)
        {
            $session->set("message", "Merci de vous connecter");
            return $this->redirectToRoute('app_login');
        }

        else if(in_array('ROLE_ADMIN', $utilisateur->getRoles())){
            $entityManager = $this->getDoctrine()->getManager();
            $notification = $entityManager->getRepository(Notification::class)->findAll();
            return $this->render('dashboard/navbar.html.twig', [
                "notification" => $notification,
            ]);
        }
        else if(in_array('ROLE_GERANT', $utilisateur->getRoles())){
            $entityManager = $this->getDoctrine()->getManager();
                $notification = $entityManager->getRepository(Notification::class)->findbysalle($SalleId);
                return $this->render('dashboard/navbar.html.twig', [
                    "notification" => $notification,
                ]);


        }

        return $this->redirectToRoute('dashboard');


    }
    /**
     * @Route("/", name="utilisateur_index", methods={"GET"})
     */
    public function home( Session $session): Response
    {
        //besoin de droits admin
        $utilisateur = $this->getUser();
        if(!$utilisateur)
        {
            $session->set("message", "Merci de vous connecter");
            return $this->redirectToRoute('front_office');
        }
        else {
            return $this->render('front_office/home.html.twig');
        }

    }

}