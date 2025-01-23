<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PostController extends AbstractController{
    #[Route('/user/post', name: 'app_post', methods: ['GET'])]
    public function index(PostRepository $postRepository): Response
    {

        $posts = $postRepository->findAll();

        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController', 'posts' => $posts
        ]);
    }
}
