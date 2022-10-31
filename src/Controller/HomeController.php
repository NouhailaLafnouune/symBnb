<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     *  @Route("/bonjour/{prenom}/age/{age}", name="hello")
     * @Route("/salut" , name="hello_base")
     * @Route("/bonjour/{prenom}" , name="hello_prenom")
     * Montre la page qui dit bonjour
     * 
     * @return void
     */
    public function hello($prenom = "anonyme", $age = 0) {
        return new Response("Bonjour " . $prenom . " Vous Avez " . $age . "ans" );
    }




    #[Route('/', name: 'homepage')]


    public function home(){
        $prenoms = ["lion" => 31,"Joseph" => 12,"Anne" => 55];

        return $this->render(
            'home.html.twig',
            [
                 'title'=>"Aurevoir Tout le monde",
                 'age'=>12,
                 'tableau'=>$prenoms
            ]
            );

    }
   
    }
    
    

