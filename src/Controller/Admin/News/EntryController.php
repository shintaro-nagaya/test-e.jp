<?php

namespace App\Controller\Admin\News;

use App\Entity\News\Child;
use App\Entity\News\Entry;
use App\Exception\CannotExecuteException;
use App\Form\Admin\News\Entry\ChildType;
use App\Form\Admin\News\Entry\EntryType;
use App\Form\Admin\News\Entry\SearchType;
use App\Repository\News\EntryRepository;
use App\Service\Entity\News\EntryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use TripleE\Utilities\Controller\AddChildTrait;
use TripleE\Utilities\Controller\IndexListTrait;
use TripleE\Utilities\Controller\ReturnPageTrait;

#[Route(path: "/admin/news/entry")]
class EntryController extends AbstractController
{
    use IndexListTrait;
    use ReturnPageTrait;
    use AddChildTrait;

    #[Route(
        path: "/{page}",
        name: "admin_news_entry",
        requirements: ["page" => "\d+"],
        methods: ["GET", "POST"]
    )]
    public function index(
        Request $request,
        EntryRepository $repository,
        int $page = 1
    ): Response {
        $form = $this->createForm(SearchType::class);
        $paginate = $this->handleResumedList(
            $request,
            $form,
            $repository,
            "admin_news_entry_index",
            $page
        );
        return $this->render('admin/news/entry/index.html.twig', [
            "data" => $paginate->getPaginator()->getIterator(),
            "paginate" => $paginate,
            "form" => $form->createView()
        ]);
    }

    #[Route(
        path: "/add",
        name: "admin_news_entry_add",
        methods: ["GET", "POST"]
    )]
    #[Route(
        path: "/edit/{id}",
        name: "admin_news_entry_edit",
        requirements: ["id" => "\d+"],
        methods: ["GET", "POST"]
    )]
    public function form(
        Request $request,
        EntryService $service,
        Entry $entry = null
    ): Response {
        $service->setContainer($this->container);
        $this->setReturnPage($request);
        if(!$entry) {
            $entry = $service->createNewEntity();
        } else {
            if($request->query->has('clone')) {
                $entry = clone $entry;
            }
        }
        $form = $this->createForm(EntryType::class, $entry);
        if("POST" === $request->getMethod()) {
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()) {
                $res = $service->persistEntity($entry, $form);
                $this->addFlash("success", sprintf(
                    "Newsを%sしました",
                    $res === EntryService::CREATED ? "作成" : "保存"
                ));
                return $this->redirectToRoute("admin_news_entry", $this->getReturnPageParameters());
            } else {
                $this->addFlash("error", "入力に不備があります");
            }
        }
        return $this->render('admin/news/entry/form.html.twig', [
            "form" => $form->createView(),
            "isCreate" => !$entry->getId(),
            "canDelete" => $service->canDelete($entry),
            "returnParams" => $this->getReturnPageParameters()
        ]);
    }

    #[Route(
        path: "/delete/{id}",
        name: "admin_news_entry_delete",
        requirements: ["id" => "\d+"],
        methods: ["DELETE", "POST"]
    )]
    public function delete(
        Request $request,
        EntryService $service,
        Entry $entry
    ): Response {
        $this->setReturnPage($request);
        if($this->isCsrfTokenValid(
            "news_entry_delete_". $entry->getId(),
            $request->request->get('_token')
        )) {
            try {
                $service->deleteEntity($entry);
                $this->addFlash("success", "削除しました");
            } catch (CannotExecuteException $e) {
                $this->addFlash("error", $e->getMessage());
            }
        }
        return $this->redirectToRoute("admin_news_entry", $this->getReturnPageParameters());
    }

    #[Route(
        path: "/child", name: "admin_news_entry_child", methods: ["GET"]
    )]
    public function child(Request $request): Response {
        return $this->getAdminCmsEntryChildHtml(
            $request,
            Child::class,
            ChildType::class,
            "admin/news/entry/_child.html.twig"
        );
    }

    #[Route(
        path: "/preview/{id}",
        name: "admin_news_entry_preview",
        requirements: ["id" => "\d+"],
        methods: ["GET", "POST"]
    )]
    public function preview(
        Request $request,
        EntryRepository $repository,
        EntryService $entryService,
        int $id = null
    ): Response {
        if("POST" === $request->getMethod()) {
            $entry = $entryService->createNewEntity();
            $form = $this->createForm(EntryType::class, $entry);
            $form->handleRequest($request);
            $entry->reSortChild();
        } else {
            $entry = $repository->findOneBy(["id" => $id]);
            if(!$entry) {
                throw new NotFoundHttpException("entry not found");
            }
        }
        return $this->render("admin/news/entry/preview.html.twig", [
            "entry" => $entry
        ]);
    }
}