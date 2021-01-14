<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PraticienController extends AbstractController
{
    /**
     * @Route("/nos-praticiens", name="nos_praticiens")
     */
    public function index(): Response
    {
        return $this->render('praticien/nos-praticiens.html.twig');
    }
}
