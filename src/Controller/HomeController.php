<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route(name: 'home_')]
class HomeController extends AbstractController
{

    #[Route(path: '')]
    #[Route(path: 'home', name: 'home', methods: ['GET'])]
    public function home(): Response
    {
        return $this->render('home/home.html.twig');
    }

    #[Route(path: 'about', name: 'about', methods: ['GET'])]
    public function about(KernelInterface $kernel): Response
    {
        $jsonFilePath = $kernel->getProjectDir() . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'team.json';
        $jsonData = file_get_contents($jsonFilePath);
        $creatorsData = json_decode($jsonData, true);

        return $this->render('home/about.html.twig', compact('creatorsData'));
    }


    #[Route(path: 'contact', name: 'contact', methods: ['GET'])]
    public function contact(): Response
    {
        return $this->render('home/contact.html.twig');
    }

    public function list(): Response
    {
        return $this->redirectToRoute('wish_list');
    }
}