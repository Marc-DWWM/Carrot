<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Entity\Like;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class LikeController extends AbstractController
{
    #[Route('/posts/{id}/like', name: 'posts_like', methods: ['POST'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function like(Posts $posts, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $like = $em->getRepository(Like::class)->findOneBy(['posts' => $posts, 'user' => $user]);

        if ($like) {
            $em->remove($like); // Supprimer le like existant
        } else {
            $newLike = new Like();
            $newLike->setPosts($posts);
            $newLike->setUser($user);
            $em->persist($newLike); // Ajouter un nouveau like
        }

        $em->flush();

        // Redirection vers la page de l'article
        //return $this->redirectToRoute('app_posts_show', ['id' => $posts->getId()]);

         // Retourner une réponse JSON avec l'état du like et le nouveau nombre de likes
        return $this->json([
            'status' => 'success',
            'liked' => $like ? false : true,
            'likeCount' => count($posts->getLikes())
        ]);
    }
}

