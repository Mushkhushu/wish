<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;


#[Route(path: 'category/', name: 'category_')]
#[IsGranted('ROLE_ADMIN')]
class CategoryController extends AbstractController
{
    #[Route(path: '', name: 'list', methods: ['GET'])]
    public function list(CategoryRepository $cateRepo): Response
    {
        $categories = $cateRepo->findAll();
        $wishCounts = $cateRepo->howManyWishesByCategory();

        return $this->render('category/list.html.twig', ['categories' => $categories,
            'wishCounts' => $wishCounts]);
    }

    #[Route(path: 'create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $cate = new Category();
        $form = $this->createForm(CategoryType::class, $cate);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($cate);
            $em->flush();
            $this->addFlash('success', 'La catégorie a été créée avec succès !');
            return $this->redirectToRoute('category_list');
        }
        return $this->render('category/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route(path: '{id}', name: 'details', requirements: ["id" => "[1-9]\d*"], methods: ['GET'])]
    public function details(CategoryRepository $CateRepo, Request $request, int $id): Response
    {
        $category = $CateRepo->find($id); // Utiliser $id directement
        $wishes = $CateRepo->getWishesByCategory($id);
        return $this->render('category/details.html.twig', compact('category', 'wishes'));
    }

    #[Route(path: '/update/{id}', name: 'update', requirements: ["id" => "[1-9]\d*"], methods: ['GET', 'POST'])]
    public function update(EntityManagerInterface $em, Request $request, SluggerInterface $slugger, int $id): Response
    {
        $category = $em->getRepository(Category::class)->find($id);
        if (!$category) {
            throw $this->createNotFoundException('La catégorie n\'existe pas pour l\'id ' . $id);
        }
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            if (!empty($formData->getName())) {
                $category->setName($formData->getName());
            }
            $em->flush();
            $this->addFlash('success', 'La catégorie a été modifiée avec succès !');

        }
        return $this->render('category/update.html.twig');

    }

    #[Route(path: "{id}", name: 'delete', requirements: ["id" => "[1-9]\d*"], methods: ['POST'])]
    public function delete(EntityManagerInterface $em, Request $request): Response
    {
        $category = $em->getRepository(Category::class)->find($request->get('id'));
        $em->remove($category);
        $em->flush();
        $this->addFlash('danger', 'La catégorie a été supprimée avec succès !');
        return $this->redirectToRoute('category_list');
    }
}