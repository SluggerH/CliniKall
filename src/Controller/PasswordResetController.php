<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\PasswordResetType;
use App\Security\AppAuthenticator;
use App\Service\EmailService;
use App\Repository\UserRepository;
use Nzo\UrlEncryptorBundle\Encryptor\Encryptor;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class PasswordResetController extends AbstractController
{
    const ENCRYPT_PREFIX='password-reset$';

    /**
     * @Route("/password_reset", name="password_reset")
     */
    public function index(Request $request,UserRepository $userRepository,EmailService $emailService,Encryptor $encryptor): Response
    {
        if ($request->isMethod('POST'))
        {
        $email=$request->request->get('reset_email');
        $user=$userRepository->findOneByEmail($email);
        if ($user){

            $token=$encryptor->encrypt(self::ENCRYPT_PREFIX.$user->getEmail());
            $link=$this->generateUrl('reset_mdp',[
                'token'=>$token
            ], UrlGeneratorInterface::ABSOLUTE_URL);

            $emailService->send([
                'to'=>$user->getEmail(),
                'subject'=>"Réinitialiser votre email",
                'template'=>"email/password_reset.email.twig",
                'context'=>[
                    'link'=> $link,
                    'user'=> $user
                ]
            ]);
        }
        $this->addFlash('success',"Vous recevrez un email si votre adresse mail est bien renseignée.");
        return $this->redirectToRoute('password_reset');
        }

        return $this->render('password_reset/index.html.twig', [
        ]);
    }

    /**
     * @Route("/reset_mdp/{token}", name="reset_mdp")
     */
    public function passwordReset($token,Encryptor $encryptor,UserRepository $userRepository,Request $request,UserPasswordEncoderInterface $passwordEncoder,AppAuthenticator $authenticator, GuardAuthenticatorHandler $guardHandler)
    {
        $decrypt=$encryptor->decrypt($token);
        $pos=strpos($decrypt,self::ENCRYPT_PREFIX);
        $email=str_replace(self::ENCRYPT_PREFIX,'',$decrypt);
        $user=$userRepository->findOneByEmail($email);

        if ($pos !==0 || !$user){
            throw new AccessDeniedHttpException();
        }
        $form=$this->createForm(PasswordResetType::class,$user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

           $plainPassword=$form->get('password')->getData();
           $encodedPassword=$passwordEncoder->encodePassword($user,$plainPassword);
            $user->setPassword($encodedPassword);

            $em = $this->getDoctrine()->getManager();
            $em->flush();
                 
            $this->addFlash('success','Votre mot de passe a été réinitialisé.');

            return $this->redirectToRoute('app_login');
        }
        return $this->render('password_reset/reset_mdp.html.twig',[
                      'form'=>$form->createView()
            ]);     
    }
}
