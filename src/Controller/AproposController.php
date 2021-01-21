<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AproposController extends AbstractController
{
    /**
     * @Route("/a-propos", name="a_propos")
     */
    public function apropos(Request $request): Response
    {
        $search=$request->query->get('search');
        if ($search){
            return $this-> redirectToRoute ('recherche',['search' => $search]);
        }
        return $this->render('apropos/a-propos.html.twig');
    }

}
