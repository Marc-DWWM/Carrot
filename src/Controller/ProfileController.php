<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Entity\User;
use App\Form\PictureProfilType;
use App\Repository\UserRepository;
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
    public function modifierProfil(Request $request, SluggerInterface $slugger,  #[Autowire('%kernel.project_dir%/public/uploads/brochures')] string $picturesDirectory): Response
    {
        $picture = new Picture();
        $form = $this->createForm(PictureProfilType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $pictureFile */
            $pictureFile = $form->get('picture')->getData();
            if ($pictureFile) {
                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $pictureFile->guessExtension();

                try {
                    $pictureFile->move($picturesDirectory, $newFilename);
                } catch (FileException $e) {
                }
                $picture->setPhoto($newFilename);
            }
            return $this->redirectToRoute('app_profil_index');
        }
        return $this->render('profile/modifier.html.twig', [
            'form' => $form,
        ]);
    }
}
