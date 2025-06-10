<?php
/**
 * TOPページ
 */
namespace App\Controller\Mvc;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    #[Route(path: '/', name: 'top')]
    public function index(): Response
    {
        return $this->render('pages/index/index.html.twig', []);
    }
}