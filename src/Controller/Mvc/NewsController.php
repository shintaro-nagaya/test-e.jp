<?php

namespace App\Controller\Mvc;

use App\Entity\Interfaces\Cms\LinkInterface;
use App\Repository\News\CategoryRepository;
use App\Service\Entity\News\EntryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: "/news")]
class NewsController extends AbstractController
{
    #[Route(path: "/", name: "news_index", methods: ["GET"])]
    public function index(
        Request $request,
        EntryService $service,
        CategoryRepository $categoryRepository
    ): Response
    {
        $service->setContainer($this->container);
        $paginate = $service->getPagination($request);
        return $this->render('pages/news/index.html.twig', [
            "paginate" => $paginate,
            "categories" => $categoryRepository->getCategoriesHaveEntry()
        ]);
    }

    #[Route(path: "/{id}", name: "news_detail", requirements: ["id" => "\d+"], methods: ["GET"])]
    public function detail(
        EntryService $entryService,
        int          $id
    ): Response
    {
        $entry = $entryService->getEntryById($id);
        if (!$entry) {
            throw $this->createNotFoundException();
        }
        if (
            in_array(LinkInterface::class, class_implements($entry), true) &&
            $entry->getLinkUrl()
        ) {
            return $this->redirect($entry->getLinkUrl());
        }
        $beforeAfter = $entryService->getBeforeAfter($entry);
        return $this->render('pages/news/detail.html.twig', [
            "entry" => $entry,
            "before" => $beforeAfter['before'],
            "after" => $beforeAfter['after']
        ]);
    }

    #[Route(path: '/headline', name: "news_headline", methods: ["GET"])]
    public function headline(
        Request $request,
        EntryService $service
    ): Response
    {
        $service->setContainer($this->container);
        $paginate = $service->getPagination($request, 4);
        return $this->render('pages/news/headline.html.twig', [
            "paginate" => $paginate
        ]);
    }
}