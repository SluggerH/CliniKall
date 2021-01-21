<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;

class BaseController extends AbstractController
{
    /**
     * @Route("/", name="accueil")
     */
    public function index(UserRepository $userRepository,Request $request): Response
    {
        $search=$request->query->get('search');
        if ($search){
            return $this-> redirectToRoute ('recherche',['search' => $search]);
        }
        


        return $this->render('base/index.html.twig',[
            
        ]);
        
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

    /**
     * @Route("/recherche/{search}", name="recherche")
     */
    public function recherche(UserRepository $userRepository,Request $request,$search): Response
    {
        
        $liste_search=$userRepository->search($search);
        $search_users=[];
        foreach ($liste_search as $liste) {
           if ($liste->hasRole("ROLE_PRO")) {
               $search_users[]=$liste;
           }
        }
        if ($search_users){
 
            return $this->render('base/recherche_praticiens.html.twig', [
                  'search_users'=>$search_users
            ]);
        }else{
            $this->addFlash('danger',"Il n'y a pas de praticien lié à votre recherche.");
            return $this->redirectToRoute('accueil');
        }
    }
    
}
