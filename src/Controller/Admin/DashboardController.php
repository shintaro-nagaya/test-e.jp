<?php

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/admin')]
class DashboardController extends AbstractController
{
    #[Route(path: '/', name: 'admin')]
    public function index(): Response
    {
        return $this->redirectToRoute("admin_dashboard");
    }
    #[Route(path: '/dashboard', name: 'admin_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('admin/dashboard/index.html.twig', []);
    }
    #[Route(path: '/theme-change', name: 'admin_theme_change', methods: ["POST"])]
    public function themeChange(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $account = $this->getUser();
        $account->setAdminLightMode((bool)$request->request->get('theme'));
        $entityManager->persist($account);
        $entityManager->flush();

        return $this->json("done");
    }
}