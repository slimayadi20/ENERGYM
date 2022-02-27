<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SalleLikeController extends AbstractController
{
    /**
     * @Route("/salle/like", name="salle_like")
     */
    public function index(): Response
    {
        return $this->render('salle_like/index.html.twig', [
            'controller_name' => 'SalleLikeController',
        ]);
    }

}
