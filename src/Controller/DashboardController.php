<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;

class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
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
            return $this->render('dashboard/home.html.twig');
        }

    }
}
