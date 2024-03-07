<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class UserController extends AbstractController
{
public function getUsersInfos(Security $security) {
    $user = $security->getUser();
    $userName = $user?->getPseudo();

    return $this->render('base.html.twig', [
        'username' => $userName, 'user' => $user
    ]);
}
}