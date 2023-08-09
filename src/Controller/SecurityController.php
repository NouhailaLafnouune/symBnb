<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\AdRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class SecurityController extends AbstractController
{

    private $userPasswordHasherInterface;

    public function __construct (UserPasswordHasherInterface $userPasswordHasherInterface) 
    {
        $this->userPasswordHasherInterface = $userPasswordHasherInterface;
    }


    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

     /**
     * permet d'afficher le formulaire d'inscriptio
     * 
     * @Route("/register", name="account_register")
     * 
     * @return Response
     */
    public function register(Request $request, AdRepository $adRepository, EntityManagerInterface $manager ){
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $user->setPassword($this->userPasswordHasherInterface->hashPassword($user, $user->getPassword()));

            // Set their role
            $user->setRoles(['ROLE_USER']);
            
            $manager->persist($user);
            $manager->flush();
        }

        return $this->render('security/registration.html.twig',[
            'form' => $form->createView()
        ]);

    }
    /**
     * permet d'afficher et de triter le formulaire de modification de profil
     * 
     * @Route("/security/profile", name="account_profile")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */

    public function profile(Request $request, EntityManagerInterface $manager){
        $user = $this->getUser();
        $form = $this->createForm(AccountType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($user);
            $manager->flush();
            $this->addFlash(
                'success',
                "Les donnees du profil ont ete enregistrer avec succes"
            );
        }

        return $this->render('security/profile.html.twig',[
            'form' => $form->createView()
        ]);

    }

    /**
     * Permet de modifier le mot de passe
     * 
     * @Route("/security/password-update", name="account_password")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response 
     */
    public function updatepassword(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface, EntityManagerInterface $manager ){
        
        $user = $this->getUser();
        $passwordUpdate = new PasswordUpdate();
        
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if(!password_verify($passwordUpdate->getOldPassword(), $user->getPassword())){
            $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tape 
            n'est pas votre mot de passe actuel !"));
            }else{
                $newPassword = $passwordUpdate->getNewPassword();
                $password = $userPasswordHasherInterface->hashPassword($user, $newPassword);
                $user->setPassword($password);
                $manager->persist($user);
                $manager->flush();
                $this->addFlash(
                    'success',
                    'Votre mot de passe a bien ete modifie !'
                );
                return $this->redirectToRoute('homepage');
            }
        }

        return $this->render('security/password.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher le profil de l'utilisteur connecte
     * 
     * @Route("/security", name="account_index")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */

     public function myAccount(){
        return $this->render('user/index.html.twig',[
            'user' => $this->getUser()
        ]);
     }

     /**
      * Permet d'afficher la liste des reservation faites par l'utilisateur
      *@Route("/security/bookings", name="account_bookings")
      *@return Reponse
      */

      public function bookings(){
        return $this->render('security/bookings.html.twig');
      }


}
