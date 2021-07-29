<?php

namespace App\Form;

use App\Entity\User;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\LessThan;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('login')
            ->add('firstname', null, [
                'label'=>'Prénom',
            ])
            ->add('lastname', null, [
                'label'=>'Nom'
            ])
            ->add('birthdate', DateType::class, [
                'widget'=>'single_text',
                'label'=>'Date de naissance'
            ])
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent être identiques',
                'required' => true,
                'first_options'  => ['label' => false, 'attr'=> ['placeholder'=>'Mot de passe']],
                'second_options' => ['label' => false, 'attr'=>['placeholder'=>'Répétez votre mot de passe']],
                'attr' => ['autocomplete' => 'new-password'], 
                'constraints'=> [
                   new Length(['min'=>6, 'max'=>50, 'minMessage'=>"Mot de passe trop court, minimum {{ limit }} caractères" ]),
                   new NotBlank(['message'=>"Le mot de passe doit être renseigné"]),
                   new Regex(['value'=>'/[a-z]+/','message'=>  'Le mot de passe doit contenir une lettre minuscule']),
                   new Regex('/[A-Z]+/',  'Le mot de passe doit contenir une lettre majuscule'),
                   new Regex('/\d+/',  'Le mot de passe doit contenir un chiffre'),
                   new Regex('/[?!%_-]+/',  'Le mot de passe doit contenir un caractère spécial parmi ?!%-_'),
                ]
            ])
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
