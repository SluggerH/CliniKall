<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\RDVType;
use App\Repository\RDVRepository;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Entity\RDV;
use DateTime;

/**
 * @Route("/reservation")
 */
class RDVController extends AbstractController
{
    /**
     * @Route("/", name="reservation_praticiens")
     */
    public function praticiens(UserRepository $userRepository){

        if ($this->getUser()){

           $user=$this->getUser();
           $listeusers=$userRepository->findAll();
           $listepros=[];
            foreach ($listeusers as $list){

                if ($list->hasRole("ROLE_PRO")){
                   $listepros[]=$list;
                }
            }

            return $this->render('rdv/praticiens.html.twig', [
                 'listepros'=>$listepros,
            ]);
        }else{
            $this->addFlash('danger',"Vous devez d'abord vous connecter pour prendre rdv.");
            return $this->render('base/index.html.twig');
        }
    }

    /**
     * $date="Y-m-d"
     * 
     * @Route("/agenda/{id}/{date}", name="reservation_agenda")
     */
    public function agenda(User $praticien,DateTime $date,$id,UserRepository $userRepository){

        //vérifier que $user est un praticien
        $praticien=$userRepository->find($id);
        if ($praticien->hasRole("ROLE_PRO")){


             //vérifier que le patient ne soit pas un pro ou un admin
             $nextday=(clone $date)->modify('+ 1 day');
             $previousday=(clone $date)->modify('- 1 day');


             return $this->render('rdv/agenda.html.twig', [
                'date'=>$date,
                'nextday'=>$nextday,
                'previousday'=>$previousday,
                'praticien'=>$praticien,
               // 'hour'=>$hour,
                'horaires_rdvs'=>RDV::HORAIRES_RDV

            ]);
       }else{
            $this->addFlash('danger',"La personne choisie n'est pas un praticien.");
            return $this->redirectToRoute('reservation_praticiens'); 
        }    
    }

    /**
     * @Route("/agenda/{id}/choix-date/{date}/{hour}", name="reservation_date")
     */
    public function date(RDVType $form,Request $request,UserRepository $userRepository,$id,DateTime $date,$hour){

        $user=$userRepository->find($id);
        $lastname=$user->getLastname();
        $firstname=$user->getFirstname();
        $praticien=$user->__toString($lastname,$firstname);

        $rdv=new RDV();
        $rdv->setDay($date);
        $rdv->setHour($hour);
        $rdv->setPraticien($praticien);
        $rdv->setLastname($this->getUser());

        $form = $this->createForm(RDVType::class,$rdv);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em=$this->getDoctrine()->getManager();
            $em->persist($rdv);
            $em->flush();

            $id=$rdv->getId();

            $this->addFlash('success', "Votre rendez-vous est réservé.");
            return $this-> redirectToRoute ('reservation_confirmation',['id' => $rdv->getId()]);
        }

        return $this->render('rdv/date.html.twig', [
            'RDVForm'=>$form->createView(),
            'praticien'=>$praticien,
        ]);

    }

    /**
     * @Route("/confirmation/{id}", name="reservation_confirmation")
     */
    public function confirmation($id,RDVRepository $RDVRepository){

        $rdv=$RDVRepository->find($id);

        return $this->render('rdv/confirmation.html.twig', [
                'rdv'=>$rdv
        ]);
    }

}
