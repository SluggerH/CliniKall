<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    /**
     * @Route("/", name="accueil")
     */
    public function index(): Response
    {
        return $this->render('base/index.html.twig');
    }

    public function header($ROUTE_NAME)
    {
        return $this->render('base/header.html.twig', [
            'ROUTE_NAME' => $ROUTE_NAME,
        ]);
    }
    
}
