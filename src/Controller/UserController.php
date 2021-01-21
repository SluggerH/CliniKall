<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Entity\RDV;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Form\UserType;
use App\Repository\RDVRepository;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index(UserRepository $userRepository,UserInterface $user,RDVRepository $rdvRepository): Response
    {
        $user=$this->getUser();

        $listeusers=$userRepository->findAll();
        $listepros=[];
         foreach ($listeusers as $list){

             if ($list->hasRole("ROLE_PRO")){
                $listepros[]=$list;
             }
         }

        $em=$this->getDoctrine()->getManager();
        $em->flush();

        if ( $user->hasRole("ROLE_PRO")){

           $rdvs=$user->getRdvPraticien();
        }
        else{
            $rdvs=$user->getRDVs();
        }

        return $this->render('user/user.html.twig', [
            'user' => $user,
            'rdvs'=>$rdvs,
            'listepros'=>$listepros
        ]);
    }

    /**
     * @Route("/user_modify/{id}", name="user_modify")
     */
    public function userModify(Request $request,UserRepository $userRepository,UserInterface $user,UserType $form): Response
    {
        $user=$this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

                $em=$this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $this->addFlash('success', "Votre compte a été mofifié.");
                return $this->redirectToRoute('user');
        }


        return $this->render('user/user_modify.html.twig', [
            'user' => $user,
            'userForm'=>$form->createView()
        ]);
    }


}
