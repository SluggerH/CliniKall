<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(UserRepository $userRepository,Request $request): Response
    {
        $users=$userRepository->findBy(
        array(
            'isVerified'=> 'true'
        ),
        array(
            'lastname'=> 'ASC',
            'firstname'=> 'ASC'
        )
        );

        $search=$request->query->get('search');
        if($search){
            $search_users=$userRepository->search($search);
        } else {
            $search_users=$userRepository->findAll();
        }

        return $this->render('admin/admin.html.twig', [
            'users' => $users,
            'search_users'=>$search_users
        ]);
    }

    /**
     * @Route("/admin_delete_user/{id}", name="admin_delete_user")
     */
    public function deleteUser(User $user) 
    {
          $user->getUsername();
          $em=$this->getDoctrine()->getManager();
          $em->remove($user);
          $em->flush();

          $this->addFlash('success',"Le compte a bien été supprimé.");

        return $this->redirectToRoute('admin'); 

    }
}