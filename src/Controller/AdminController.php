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
        $userpros=$userRepository->findBy(
        array(
                   
        ),
        array(
            'lastname'=> 'ASC',
            'firstname'=> 'ASC'
        )
        );
      

        $search=$request->query->get('search');
        if($search){
            $search_users=$userRepository->search($search);
            return $this->render('admin/admin.html.twig', [
                'search_users'=>$search_users,
                'userpros'=>$userpros
            ]);
        } else
        {
            return $this->render('admin/admin.html.twig', [
               'userpros'=>$userpros
            ]);
        }
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

    /**
     * @Route("/admin_validate_user/{id}", name="admin_validate_user")
     */
    public function validateUser(User $user) 
    {
          $user->getUsername();
          $user->setIsVerified(true);
          $em=$this->getDoctrine()->getManager();
          $em->flush();

          $this->addFlash('success',"Le compte a bien été validé.");

        return $this->redirectToRoute('admin'); 

    }
}