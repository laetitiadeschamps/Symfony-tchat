<?php

namespace App\Controller;

use App\Form\UserType;
use App\Service\FileUpload;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
* @Route("/user", name="user-")
*/
class UserController extends AbstractController
{
    /** 
     * @Route("/profile", name="profile")
     */
    public function profile(Request $request, Security $security, EntityManagerInterface $em, FileUpload $fileUpload): Response
    {
        /** @var User */
        $user = $security->getUser();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
         
            /** @var UploadedFile $pictureFile */
            $picture = $form->get('picture')->getData();
            $avatar = $form->get('avatar')->getData();
            
            if ($picture) {         
                $pictureFileName = $fileUpload->upload($picture, $this->getParameter('images_directory'));  
                $user->setPicture($pictureFileName);
            }
            else if($avatar){   
                $user->setPicture($avatar);
            }
            $em->flush();
            $this->addFlash(
                'info',
                'Le profil a été mis à jour'
            );
            return $this->redirectToRoute('user-profile');
        }
        return $this->render('user/profile.html.twig', [
            'form'=> $form->createView()
        ]);
    }
}
