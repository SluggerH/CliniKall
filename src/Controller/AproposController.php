<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AproposController extends AbstractController
{
    /**
     * @Route("/a-propos", name="a_propos")
     */
    public function apropos(): Response
    {
        return $this->render('apropos/a-propos.html.twig');
    }

}
