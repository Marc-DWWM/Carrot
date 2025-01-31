<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Entity\Like;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
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
            $em->remove($like);
        } else {
            $newLike = new Like();
            $newLike->setPosts($posts);
            $newLike->setUser($user);
            $em->persist($newLike);
        }

        $em->flush();

        // Redirection vers la page de l'article
        //return $this->redirectToRoute('app_posts_show', ['id' => $posts->getId()]);

         // Retourner une réponse JSON 
        return $this->json([
            'status' => 'success',
            'liked' => $like ? false : true,
            'likeCount' => count($posts->getLikes())
        ]);
    }

    // public function delete(Request $request, Posts $post, EntityManagerInterface $entityManager): Response
       // {
    
         //   $likes = $post->getLikes(); 
         //   foreach ($likes as $like) {
         //       $entityManager->remove($like); 
          //  }

         //   $entityManager->remove($post); 
          //  $entityManager->flush(); 
//
         //   return $this->redirectToRoute('app_posts');
     //   }
}

