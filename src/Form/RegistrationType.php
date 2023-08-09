<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class RegistrationType extends ApplicationType
{
    

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           ->add('firstName', TextType::class, $this->getConfiguration("Prenom","Votre prenom ..."))
           ->add('lastName' , TextType::class, $this->getConfiguration("Nom","Votre nom de famille..."))
           ->add('email', EmailType::class, $this->getConfiguration("Email","Votre Adress email"))
           ->add('password' , PasswordType::class, $this->getConfiguration("Mot de passe","choisissez un bon mot de passe !"))
           ->add('passwordConfirm', PasswordType::class, $this->getConfiguration("Confirmation de mot de passe", "Veuillez confirmer votre mot de passe"))
           ->add('picture', UrlType::class, $this->getConfiguration("Photo de profil","URL de votre avatar ..."))
           ->add('introduction' , TextType::class, $this->getConfiguration("Introduction","Presentez vous en quelques mots ..."))
           ->add('description' , TextareaType::class, $this->getConfiguration("Description detaillee","C'est le moment de vous presenter en details !"))
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
