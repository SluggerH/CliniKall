<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;

class BaseController extends AbstractController
{
    /**
     * @Route("/", name="accueil")
     */
    public function index(): Response
    {
        return $this->render('base/index.html.twig');
    }

    public function header($ROUTE_NAME,UserRepository $userRepository)
    {
        $user=$this->getUser();

        return $this->render('base/header.html.twig', [
            'ROUTE_NAME' => $ROUTE_NAME,
            'user'=>$user
        ]);
    }


    /**
     * @Route("/account-decide", name="account-redirect")
     */
    public function accountRedirect()
    {
        if ($this->isGranted('ROLE_ADMIN'))
        {
            return $this->redirectToRoute('admin');
        } 
        elseif ($this->isGranted('ROLE_USER'))
        {
            return $this->redirectToRoute('user');
        } 
        else {
            return $this->redirectToRoute('accueil');
        }
    }

    public function footer()
    {
        return $this->render('base/footer.html.twig');
         
    }
    
}
