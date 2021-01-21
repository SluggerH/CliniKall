<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\RDVType;
use App\Repository\RDVRepository;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\User\UserInterface;
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
    public function agenda(User $praticien,DateTime $date,$id,UserRepository $userRepository,RDVRepository $rdvRepository){

        //vérifier que $user est un praticien
        $praticien=$userRepository->find($id);
        $liste_reserves=$rdvRepository->findDateRdvPraticien($praticien,$date);
        $horaire_reserves=[];
        foreach ($liste_reserves as $rdv) {
           $horaire_reserves[]=$rdv->getHour();
        }

        if ($praticien->hasRole("ROLE_PRO")){


             //vérifier que le patient ne soit pas un pro ou un admin
             $nextday=(clone $date)->modify('+ 1 day');
             $previousday=(clone $date)->modify('- 1 day');


             return $this->render('rdv/agenda.html.twig', [
                'date'=>$date,
                'nextday'=>$nextday,
                'previousday'=>$previousday,
                'praticien'=>$praticien,
                'horaires_rdvs'=>RDV::HORAIRES_RDV,
                'horaire_reserves'=>$horaire_reserves

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

        $praticien=$userRepository->find($id);

        $rdv=new RDV();
        $rdv->setDay($date);
        $rdv->setHour($hour);
        $rdv->setPraticien($praticien);
        $rdv->setPatient($this->getUser());

        $form = $this->createForm(RDVType::class,$rdv);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em=$this->getDoctrine()->getManager();
            $em->persist($rdv);
            $em->flush();


            return $this-> redirectToRoute ('reservation_confirmation',['id' => $rdv->getId()]);
        }

        return $this->render('rdv/date.html.twig', [
            'RDVForm'=>$form->createView(),
            'praticien'=>$praticien,
            'date'=>$date,
            'hour'=>$hour,
            'rdv'=>$rdv
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

        /**
     * @Route("/rdv_delete/{id}", name="rdv_delete")
     */
    public function deleteRDV(UserInterface $user,RDV $rdv,$id,RDVRepository $rdvRepository) 
    {
          $user=$this->getUser();
          $rdv=$rdvRepository->find($id);

          if ($user->hasRole("ROLE_PRO")){

            $user->removeRdvPraticien($rdv);
          }
          else{
            $user->removeRDV($rdv);
          }

          $em=$this->getDoctrine()->getManager();
          $em->flush();

          $this->addFlash('success',"Le rendez-vous a bien été supprimé.");

        return $this->redirectToRoute('user'); 

    }

}
