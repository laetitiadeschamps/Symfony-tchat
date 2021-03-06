<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            
            ->add('firstname', null, [
                'label'=>'Prénom',
                'label_attr'=> [
                    'class'=>'label'
                ]
            ])
            ->add('lastname', null, [
                'label'=>'Nom',
                'label_attr'=> [
                    'class'=>'label'
                ]
            ])
            ->add('avatar', HiddenType::class, [
                'mapped'=>false,
                'attr'=> [
                    'class'=>'avatar-input'
                ]
            ])
            ->add('picture', FileType::class, [
                'label' => '<i class="fas fa-folder-open"></i>',
                'label_attr'=>  [
                    'class'=> 'photoUpload tooltip-outer'
                ],
                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image',
                    ])
                ],
            ])
            ->add('birthdate', DateType::class, [
                'widget'=>'single_text',
                'label_attr'=> [
                    'class'=>'label'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label'=>'Description',
                'label_attr'=> [
                    'class'=>'label'
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
