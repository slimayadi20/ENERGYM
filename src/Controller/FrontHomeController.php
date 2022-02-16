<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontHomeController extends AbstractController
{
    /**
     * @Route("/front/home", name="front_home")
     */
    public function number(): Response
    {
        return $this->render('front_home/index.html.twig');
    }
}
