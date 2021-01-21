<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
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

        $em=$this->getDoctrine()->getManager();
        $em->flush();

        $rdvs=$rdvRepository->findAll($user);


        return $this->render('user/user.html.twig', [
            'user' => $user,
            'rdvs'=>$rdvs
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
