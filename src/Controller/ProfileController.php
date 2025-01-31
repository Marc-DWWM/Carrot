<?php

namespace App\Controller;


use App\Form\PictureProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

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
    #[Route('/modifier', name: 'modifier')]
    public function uploadPicture(
        Request $request,
        SluggerInterface $slugger,
        #[Autowire('%kernel.project_dir%/public/uploads')] string $picturesDirectory,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();
        $form = $this->createForm(PictureProfilType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $pictureFile */
            $pictureFile = $form->get('picture')->getData();

            if ($pictureFile) {
                  /** @var \App\Entity\User $user */
                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);

                $newFilename = $safeFilename . '-' . uniqid() . '.' . $pictureFile->guessExtension();

                try {
                    $pictureFile->move($picturesDirectory, $newFilename);
                } catch (FileException $e) {
                    echo "Image non valide " . $e->getMessage();
                }
                $user->setPhoto($newFilename);
            }
            $entityManager->persist($user);
            $entityManager->flush();


            return $this->redirectToRoute('profile_index');
        }
        return $this->render('profile/modifier.html.twig', [
            'pictureProfil' => $form->createView(),
            'user' => $user,
        ]);
    }
}
