<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use App\Security\EmailVerifier;
use App\Service\EmailService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Nzo\UrlEncryptorBundle\Encryptor\Encryptor;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class RegistrationController extends AbstractController
{
    private $encryptor;

    public function __construct(Encryptor $encryptor)
    {
        $this->encryptor = $encryptor;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder,EmailService $emailService): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $token=$this->encryptor->encrypt($user->getEmail());

            // generate a signed url and email it to the user
            $emailService->send([
                'to'=>$user->getEmail(),
                'subject'=>"Inscription sur ClinKall",
                'template'=>'email/confirmation_email.email.twig',
                'context'=>[
                    'link' => $this->generateUrl('app_verify_email', [ 'token' => $token ], UrlGeneratorInterface::ABSOLUTE_URL)
                ]
            ]);
            // do anything else you need here, like send an email
            $this->addFlash('success',"Votre inscription est prise en compte.Merci de valider votre email.");

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request,UserRepository $userRepository,AppAuthenticator $authenticator): Response
    {
        $token = $request->query->get('token');
        $email = $this->encryptor->decrypt($token);
        $user = $userRepository->findOneBy([ 'email' => $email ]);
        if (!$user) {
            $this->createAccessDeniedException();
        }

        $user->setIsVerified(true);
    
        $entityManager=$this->getDoctrine()->getManager();
        $entityManager->flush();

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', "Votre compte est bien validé.");

        return $this->redirectToRoute('app_login');
    }
}