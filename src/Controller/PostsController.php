<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Posts;
use App\Form\PostsType;
use App\Form\CommentsType;
use App\Repository\PostsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/posts')]
final class PostsController extends AbstractController
{
    #[Route(name: 'app_posts_index', methods: ['GET'])]
    public function index(PostsRepository $postsRepository): Response
    {
        $posts = $postsRepository->findBy(['originalPost' => null]);

        return $this->render('posts/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/new', name: 'app_posts_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $post = new Posts($user);
        $form = $this->createForm(PostsType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('app_posts_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('posts/new.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_posts_show', methods: ['GET', 'POST'])]
    public function show(Posts $post, Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comments();
        $commentsForm = $this->createForm(CommentsType::class, $comment);
        $commentsForm->handleRequest($request);

        if ($commentsForm->isSubmitted() && $commentsForm->isValid()) {
            $comment->setPost($post);
            $comment->setUser($this->getUser());
            $comment->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_posts_show', ['id' => $post->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('posts/show.html.twig', [
            'post' => $post,
            'commentsForm' => $commentsForm->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_posts_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Posts $post, EntityManagerInterface $entityManager): Response
    {
        if ($post->getAuthor() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Vous n'êtes pas l'auteur de ce post");
        }

        $form = $this->createForm(PostsType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_posts_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('posts/edit.html.twig', [
            'post' => $post,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_posts_delete', methods: ['POST'])]
    public function delete(Request $request, Posts $post, EntityManagerInterface $entityManager): Response
{
    if ($post->getAuthor() !== $this->getUser()) {
        throw $this->createAccessDeniedException("Vous n'êtes pas l'auteur de ce post");
    }
    if (!$this->isCsrfTokenValid('delete' . $post->getId(), $request->request->get('_token'))) {
        throw $this->createAccessDeniedException('Token CSRF invalide');
    }

    $entityManager->remove($post);
    $entityManager->flush();



    return $this->redirectToRoute('app_posts_index', [], Response::HTTP_SEE_OTHER);
}

    #[Route('/{id}/repost', name: 'app_posts_repost', methods: ['GET'])]
    public function repost(EntityManagerInterface $entityManager, Posts $post): Response
    {
        $user = $this->getUser();
        $repost = new Posts($user);
        $repost->setContent($post->getContent());
        $repost->setOriginalPost($post);

        $entityManager->persist($repost);
        $entityManager->flush();

        return $this->redirectToRoute('app_posts_index', [], Response::HTTP_SEE_OTHER);
    }




}
