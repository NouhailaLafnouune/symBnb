<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Ad;
use App\Form\AdType;
use App\Entity\Image;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AdRepository;
use App\Repository\ImageRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;




class AdController extends AbstractController
{
    /**
     *  @Route("/ads", name= "ads_index")
     */
   
    public function index(AdRepository $repo): Response
    {

        //$repo = $this->getDoctrine()->getRepository(Ad::class);
        $ads = $repo->findAll();
        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }

     /**
     * permet de creer une annonce
     * 
     * @Route("ads/new", name="ads_create")
     *
     * @IsGranted("ROLE_USER")
     * @return Response
     */

    public function create(Request $request,  AdRepository $adRepository, EntityManagerInterface $manager){


        $ad = new Ad();

        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
              foreach($ad->getImages() as $image){
                $image->setAd($ad);
                $manager->persist($image);
            }

            $ad->setAuthor($this->getUser());

            $manager->persist($ad);
            $manager->flush();
           $this->addFlash(
            'success',
            "L'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrées !"
        );

          
           return $this->redirectToRoute('ads_show',[
            'id' => $ad->getid()
           ]);
        }

        return $this->render('ad/new.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher le furmulaire d'edition
     * 
     * @Route("/ads/{id}/edit", name="ads_edit")
     * @Security("is_granted('ROLE_USER') and user== ad.getAuthor()", message="Cette annonce ne vous appartient pas, vous ne pouvez pas la modifier")
     * @return Response
     */

    public function edit(Ad $ad, Request $request, AdRepository $adRepository, EntityManagerInterface $manager){

        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            foreach($ad->getImages() as $image){
              $image->setAd($ad);
              $manager->persist($image);
          }
          $manager->persist($ad);
          $manager->flush();

         $this->addFlash(
          'success',
          "Les Modifications de l'annonce <strong>{$ad->getTitle()}</strong> ont bien été enregistrées !"
      );

        
         return $this->redirectToRoute('ads_show',[
          'id' => $ad->getid()
         ]);
      }

       return $this->render('ad/edit.html.twig', [
        'form' => $form->createView(),
        'ad' => $ad
       ]);
        
       
    }




    /**
     * permet d'afficher une seule annonce
     * 
     * @Route("/ads/{id}", name="ads_show")
     * 
     * @return Response
     */
    public function show(Ad $ad){
        return $this->render('ad/show.html.twig' ,[
             'ad' => $ad
        ]);

    }

    /**
     * Permet de suuprimer une annonce
     * 
     * @Route("/ads/{id}/delete", name="ads_delete")
     * @Security("is_granted('ROLE_USER') and user == ad.getAuthor()", message="Vous n'avez pas 
     * le droit d'acceder a cette resource")
     * @param Ad $ad
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Ad $ad, EntityManagerInterface $manager){
        $manager->remove($ad);
        $manager->flush();

        $this->addflash(
            'success',
            "L'annonce <strong>{$ad->getTitle()}</strong> a bien ete supprimee !"
        );
        return $this->redirectToRoute('ads_index');

    }

   
    
}
