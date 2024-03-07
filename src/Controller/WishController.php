<?php

namespace App\Controller;

use App\Censurator\Censurator;
use App\Entity\Wish;
use App\Form\WishType;
use App\Repository\WishRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route(path: 'wish/', name: 'wish_')]
class WishController extends AbstractController
{
    #[Route(path: '', name: 'list', methods: ['GET'])]
    public function list(WishRepository $wishRepo, Request $request): Response
    {
        $elementsByPage = 10;
        if (($page = $request->get('p', 1)) < 1) {
            return $this->redirectToRoute('wish_list');
        }
        $total = count($wishRepo->getAllPublishedWishes());
        $wishes = $wishRepo->getWishesWithCategoryByPage($page, $elementsByPage);

        if ($page !== 1 && empty($wishes)) {
            return $this->redirectToRoute('wish_list');
        }
        return $this->render('wish/list.html.twig', compact('wishes', 'total', 'elementsByPage'));
    }
// $elementsByPage, $elementsByPage*($page-1)
    //ex : http://127.0.0.1:8001/detail/12
    /*    #[Route(path: '{id}', name: 'details', requirements: ["id" => "[1-9]\d*"], methods: ['GET'])]
        public function details(?Wish $wish): Response
        {
            if (!$wish || !$wish->isPublished()) {
                throw $this->createNotFoundException('no published wish found for id ' . $wish->getId());
            }
            return $this->render('wish/details.html.twig', compact('wish'));
        }*/
    #[Route(path: '{id}', name: 'details', requirements: ["id" => "[1-9]\d*"], methods: ['GET'])]
    public function details(WishRepository $wishRepo, Request $request): Response
    {
        $wish = $wishRepo->getWishById($request->get('id'));
        if (!$wish || !$wish->isPublished()) {
            throw $this->createNotFoundException('no published wish found for id ' . $wish->getId());
        }
        return $this->render('wish/details.html.twig', compact('wish'));
    }

    #[Route(path: "create", name: 'create', methods: ['GET', 'POST'])]
    public function form(Censurator $censurator,EntityManagerInterface $entMana, Request $request, SluggerInterface $slugger): Response
    {
        $wish = new Wish();
        $wishForm = $this->createForm(WishType::class, $wish);
        $wishForm->handleRequest($request);
        if ($wishForm->isSubmitted() && $wishForm->isValid()) {
            $wish->setisPublished(true);
            $censoredTitle=$censurator->purify($wish->getTitle());
            $censoredDescription=$censurator->purify($wish->getDescription());
            $wish->setTitle($censoredTitle);
            $wish->setDescription($censoredDescription);
            if ($wishForm->get('picture_file')->getData() instanceof UploadedFile) {
                $pictureFile = $wishForm->get('picture_file')->getData();
                $fileName = $slugger->slug($wish->getTitle()) . '-' . uniqid() . '.' . $pictureFile->guessExtension();
                $pictureFile->move($this->getParameter('picture_dir'), $fileName);
                $wish->setPicture($fileName);
            }

            $entMana->persist($wish);
            $entMana->flush();
            $this->addFlash('success', 'Le wish a été créé avec succès !');
            return $this->redirectToRoute('wish_details', ['id' => $wish->getId()]);
        }
        return $this->render('wish/create.html.twig', ['wishForm' => $wishForm->createView()]);
    }

    #[Route(path: "/update/{id}", name: 'update', methods: ['GET', 'POST'])]
    public function update(Censurator $censurator, EntityManagerInterface $entityManager, SluggerInterface $slugger, Request $request, int $id): Response
    {
        $wish = $entityManager->getRepository(Wish::class)->find($id);

        if (!$wish) {
            throw $this->createNotFoundException('No published wish found for id ' . $id);
        }

        $wishForm = $this->createForm(WishType::class, $wish);
        $wishForm->handleRequest($request);

        if ($wishForm->isSubmitted() && $wishForm->isValid()) {
            // Récupérer les données soumises par le formulaire
            $formData = $wishForm->getData();

            // Modifier les attributs du souhait seulement si les données sont définies et non vides
            if (!empty($formData->getTitle())) {
                $censoredTitle = $censurator->purify($formData->getTitle());
                $wish->setTitle($censoredTitle);
            }
            if (!empty($formData->getDescription())) {
                $censoredDescription=$censurator->purify($formData->getDescription());
                $wish->setDescription($censoredDescription);
            }
            if (!empty($formData->getAuthor())) {
                $wish->setAuthor($formData->getAuthor());
            }

            if ($wishForm->get('picture_file')->getData() instanceof UploadedFile) {
                $pictureFile = $wishForm->get('picture_file')->getData();
                $fileName = $slugger->slug($wish->getTitle()) . '-' . uniqid() . '.' . $pictureFile->guessExtension();
                $pictureFile->move($this->getParameter('picture_dir'), $fileName);
                if (!empty($wish->getPicture())) {
                    $picturePath = $this->getParameter('picture_dir') . '/' . $wish->getPicture();
                    if(file_exists($picturePath)) {
                        unlink($picturePath);
                    }
                }
                $wish->setPicture($fileName);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Le wish a été mis à jour avec succès !');
            return $this->redirectToRoute('wish_list');
        }

        return $this->render('wish/update.html.twig', [
            'wish' => $wish,
            'wishForm' => $wishForm->createView()
        ]);
    }


    #[Route(path: "{id}", name: 'delete', requirements: ["id" => "[1-9]\d*"], methods: ['POST'])]
    public function delete(EntityManagerInterface $entMana, Request $request): Response
    {
        $wish = $entMana->getRepository(Wish::class)->find($request->get('id'));
        $entMana->remove($wish);
        $entMana->flush();
        $this->addFlash('danger', 'Le wish a été supprimé avec succès !');
        return $this->redirectToRoute('wish_list');
    }
}
