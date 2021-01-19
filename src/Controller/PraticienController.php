<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;

class PraticienController extends AbstractController
{
    /**
     * @Route("/nos-praticiens", name="nos_praticiens")
     */
    public function index(UserRepository $userRepository): Response
    {
        $userproMeds=$userRepository->findBy(
            array(
                 'descriptionPatient' => 'médecin'    
            ),
            array(
                'lastname'=> 'ASC',
                'firstname'=> 'ASC'
            ),
            3
            );

            $userproKins=$userRepository->findBy(
                array(
                     'descriptionPatient' => 'kiné'    
                ),
                array(
                    'lastname'=> 'ASC',
                    'firstname'=> 'ASC'
                ),
                3
                ); 

                $userproDens=$userRepository->findBy(
                    array(
                         'descriptionPatient' => 'dentiste'    
                    ),
                    array(
                        'lastname'=> 'ASC',
                        'firstname'=> 'ASC'
                    ),
                    3
                    );
        return $this->render('praticien/nos-praticiens.html.twig',[
            'userproMeds'=> $userproMeds,
            'userproKins'=>$userproKins,
            'userproDens'=>$userproDens
        ]);
    }

    /**
     * @Route("/praticien/{id}", name="praticien")
     */
    public function pagePraticien(UserRepository $userRepository,$id,User $user): Response
    {
        $user=$userRepository->find($id);

        $em=$this->getDoctrine()->getManager();
        $em->flush();

        return $this->render('praticien/praticien.html.twig',[
                 'id'=>$id,
                 'user'=>$user
        ]);
    }   
}
