<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomePageController extends AbstractController
{
    #[Route('/', name: 'app_home_page')]
    public function index(): Response
    {
        $roleadmin = ['ROLE_ADMIN', 'ROLE_USER'];
        $roleuser = ['ROLE_USER'];
        // if ($this->getUser()) {
            
        // }




        return $this->render('main/index.html.twig', [
            
        ]);
    }

    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    public function adminDashboard(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    
    #[Route('/dashboard', name: 'app_user_dashboard')]
    public function userDashboard(): Response
    {
        return $this->render('user/dashboard.html.twig');
    }
}
