<?php

namespace App\Controller;

use App\Repository\PostsRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/profile')]

final class ProfileController extends AbstractController
{
    #[Route('/', name: 'app_profile')]
    public function profile(UserRepository $user, PostsRepository $posts): Response
    {
        $posts = $postsRepository->findBy(['author' => $author, 'content' => $content, 'created_at' => $created_at]);
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }
}
