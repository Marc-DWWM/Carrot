<?php

namespace App\Controller;

Use App\Entity\Posts;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/profil', name: 'profile_')]

final class ProfileController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {


        return $this->render('profile/index.html.twig', [
            'controller_name' => 'Profil de l\'utilisateur',
        ]);
    }

    #[Route('/posts', name: 'posts')]
    public function posts(): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $posts = $user->getPosts();    
        return $this->render('profile/posts.html.twig', [
            'posts' => $posts,
        ]);
    }
}
