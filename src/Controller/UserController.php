<?php

namespace App\Controller;

use App\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route(path: 'user/', name: 'user_')]
class UserController extends AbstractController
{
public function getUsersInfos(Security $security) {
    $user = $security->getUser();
    $userName = $user?->getPseudo();

    return $this->render('base.html.twig', [
        'username' => $userName, 'user' => $user
    ]);
}
#[Route(path:'profile', name: 'profile',methods: ['GET', 'POST'])]
public function updateProfile(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
{
    $user = $this->getUser();
    $form = $this->createForm(UserProfileType::class, $user);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {

        $formData=$form->getData();
        if (!empty($formData->getPseudo())) {
            $user->setPseudo($formData->getPseudo());
        }
        if (!empty($formData->getEmail())) {
            $user->setEmail($formData->getEmail());
        }

        if ($form->get('picture_file')->getData() instanceof UploadedFile) {
            $pictureFile = $form->get('picture_file')->getData();
            $fileName = $slugger->slug($user->getPseudo()) . '-' . uniqid() . '.' . $pictureFile->guessExtension();
            $pictureFile->move($this->getParameter('picture_dir'), $fileName);
            if (!empty($user->getPicture())) {
                $picturePath= $this->getParameter('picture_dir') . '/' . $user->getPicture();
                if (file_exists($picturePath)) {
                    unlink($picturePath);
                }
            }
            $user->setPicture($fileName);
        }

        $entityManager->flush();
        $this->addFlash('success', 'Le profil a été mis à jour avec succès !');
        return $this->redirectToRoute('user_profile');
    }

    // Afficher la page de profil avec le formulaire de modification
    return $this->render('user/profile.html.twig', [
        'user' => $user,
        'form' => $form->createView(),
    ]);
}
}