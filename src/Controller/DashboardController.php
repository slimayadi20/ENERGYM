<?php

namespace App\Controller;

use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Repository\UserRepository;
use App\Entity\Categories;
use App\Entity\Produit;
use App\Entity\Notification;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(UserRepository $repository,Request $request ,Session $session): Response
    {
        $utilisateur = $this->getUser();
        $SalleId = $utilisateur->getIdSalle();
        $pieChart = new PieChart();

        $data = $this->getDoctrine()->getRepository(Produit::class)->findAll();
        $typeAlimentaire = 0;
        $typeMaterielMusculation = 0;
        $materieldeboxe = 0;
        $typeMateriel=0;
        foreach ($data as $x) {
            foreach ($x->getCategories() as $c)
            {

            if($c->getNom()=="produit alimentaire")
            {
                $typeAlimentaire++ ;
            }
            else if($c->getNom()=="materiel de musculation")
            {
                $typeMaterielMusculation++ ;
            }
            else if($c->getNom()=="materiel de boxe")
            {
                $materieldeboxe++ ;
            }
            else{
                $typeMateriel++ ;
            }
            }
        }

        $pieChart = new PieChart();
        //ALL STATS HERE
        //Livraison summary chart
        $pieChart->getData()->setArrayToDataTable(
            [['categories', 'nbr de produits'],
                ["type alimentaire",     $typeAlimentaire],
                ["materiel de musculation",     $typeMaterielMusculation],
                ["materiel de boxe",     $materieldeboxe],
                ["autre materiel",     $typeMateriel],
            ]
        );
        $pieChart->getOptions()->setTitle('Nombre de Produit selon les categories');
        $pieChart->getOptions()->setHeight(500);
        $pieChart->getOptions()->setWidth(800);
        $pieChart->getOptions()->getTitleTextStyle()->setBold(true);
        $pieChart->getOptions()->getTitleTextStyle()->setColor('#009900');
        $pieChart->getOptions()->getTitleTextStyle()->setItalic(true);
        $pieChart->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $pieChart->getOptions()->getTitleTextStyle()->setFontSize(20);



        if (!$utilisateur) {
            $session->set("message", "Merci de vous connecter");
            return $this->redirectToRoute('app_login');
        } else if (in_array('ROLE_ADMIN', $utilisateur->getRoles())) {
            $entityManager = $this->getDoctrine()->getManager();
            $notification = $entityManager->getRepository(Notification::class)->findAll();
            return $this->render('dashboard/index.html.twig', [
                "notification" => $notification,
                'list' => $data,
                'piechart' => $pieChart,
            ]);
        } else if (in_array('ROLE_GERANT', $utilisateur->getRoles())) {
            $entityManager = $this->getDoctrine()->getManager();
            $notification = $entityManager->getRepository(Notification::class)->findAll($SalleId);
            return $this->render('dashboard/index.html.twig', [
                "notification" => $notification,
                'list' => $data,
                'piechart' => $pieChart,
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