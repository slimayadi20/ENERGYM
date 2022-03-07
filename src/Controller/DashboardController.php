<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Repository\UserRepository;

class DashboardController extends AbstractController
{
    /**
     * @Route("/{_locale/dashboard", name="dashboard")
     * requiremenets={
     * "_locale" : "en|fr",
     * }
     * }
     */
    public function index(UserRepository $repository): Response
    {
        return $this->render('dashboard/index.html.twig', [

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
            return $this->render('front_office/home.html.twig');
        }

    }
}
