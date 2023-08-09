<?php

namespace App\Controller;
use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Form\BookingType;
use App\Form\CommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class BookingController extends AbstractController
{
    #[Route('/ads/{id}/book', name: 'app_booking')]
    /**
     * @IsGranted("ROLE_USER")
     */
    public function book(Ad $ad, Request $request, EntityManagerInterface $manager)
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user = $this->getUser();

            $booking->setBooker($user)
                    ->setAd($ad);

                    if(empty($booking->getCreatedAt())){
                        $booking->setCreatedAt(new \DateTimeImmutable());
                    }

                    $diff = $booking->getEndDate()->diff($booking->getStartDate());
                    $duration = $diff->days;
                    if(empty($booking->getAmount())){
                        //Prix de l'annonce * nombre de jour
                        $booking->setAmount($ad->getPrice() * $duration);
                    }
                if(!$booking->isBookableDates()){
                    $this->addFlash(
                        'warning',
                        "Les dates que vous choisi ne peuvent entre reservee : elles sont deja prises."
                    );
                } else {
                    $manager->persist($booking);
                    $manager->flush();

                    return $this->redirectToRoute('booking_show', ['id' =>$booking->getId(),
                    'withAlert' => true]);

                }

                    
                   
                    
        }

        return $this->render('booking/book.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);

    }

    /**
     * Permet d'afficher la page d'une reservation
     * 
     * @Route("/booking/{id}", name="booking_show")
     * 
     * @param Booking $booking
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
     public function show(Booking $booking, Request $request, EntityManagerInterface $manager){
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            $comment->setAd($booking->getAd())
                    ->setAuthor($this->getUser());

            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre commentaire a bien ete pris en compte !"
            );
        }
        $diff = $booking->getEndDate()->diff($booking->getStartDate());
        $duration = $diff->days;
         
        
        return $this->render('booking/show.html.twig',[
            'booking'  => $booking,
            'duration' => $duration,
            'form'     => $form->createView()
        ]);
     }
}
