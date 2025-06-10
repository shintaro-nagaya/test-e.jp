<?php

namespace App\Controller\Admin\News;

use App\Entity\News\Category;
use App\Exception\CannotExecuteException;
use App\Form\Admin\News\Category\CategoryType;
use App\Form\Admin\News\Category\SearchType;
use App\Form\Admin\News\Category\SortType;
use App\Repository\News\CategoryRepository;
use App\Service\Entity\News\CategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use TripleE\Utilities\Controller\IndexListTrait;
use TripleE\Utilities\Controller\ReturnPageTrait;

#[Route(path: "/admin/news/category")]
class CategoryController extends AbstractController
{
    use IndexListTrait;
    use ReturnPageTrait;

    #[Route(
        path: "/{page}",
        name: "admin_news_category",
        requirements: ["page" => "\d+"],
        methods: ["GET", "POST"]
    )]
    public function index(
        Request $request,
        CategoryRepository $repository,
        int $page = 1
    ): Response {
        $form = $this->createForm(SearchType::class);
        $paginate = $this->handleResumedList(
            $request,
            $form,
            $repository,
            "admin_news_category_index",
            $page
        );
        return $this->render('admin/news/category/index.html.twig', [
            "data" => $paginate->getPaginator()->getIterator(),
            "paginate" => $paginate,
            "form" => $form->createView()
        ]);
    }

    #[Route(
        path: "/add",
        name: "admin_news_category_add",
        methods: ["GET", "POST"]
    )]
    #[Route(
        path: "/edit/{id}",
        name: "admin_news_category_edit",
        requirements: ["id" => "\d+"],
        methods: ["GET", "POST"]
    )]
    public function form(
        Request $request,
        CategoryService $service,
        ?Category $category = null
    ): Response {
        $service->setContainer($this->container);
        $this->setReturnPage($request);
        if(!$category) {
            $category = $service->createNewEntity();
        }
        $form = $this->createForm(CategoryType::class, $category);
        if("POST" === $request->getMethod()) {
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()) {
                $res = $service->persistEntity($category);
                $this->addFlash("success", sprintf(
                    "カテゴリーを%sしました",
                    $res === CategoryService::CREATED ? "作成" : "保存"
                ));
                return $this->redirectToRoute("admin_news_category", $this->getReturnPageParameters());
            } else {
                $this->addFlash("error", "入力に不備があります");
            }
        }

        return $this->render('admin/news/category/form.html.twig', [
            "form" => $form->createView(),
            "isCreate" => !$category->getId(),
            "canDelete" => $service->canDelete($category),
            "returnParams" => $this->getReturnPageParameters()
        ]);
    }

    #[Route(
        path: "/sort/{id}",
        name: "admin_news_category_sort",
        requirements: ["id" => "\d+"],
        methods: ["POST"]
    )]
    public function updateSort(
        Request $request,
        CategoryService $service,
        Category $category
    ): Response {
        $this->setReturnPage($request);
        if(!$this->isCsrfTokenValid(
            "news_category_sort_". $category->getId(),
            $request->request->get('_token')
        )) {
            $this->addFlash('error', '実行できません');
            return $this->redirectToRoute("admin_news_category", $this->getReturnPageParameters());
        }
        $service->setContainer($this->container);
        $form = $this->createForm(SortType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $category
                ->setSort($form->get('sort')->getData())
                ;
            $service->persistEntity($category);
            $this->addFlash('success', '順番を保存しました');
        } else {
            $errors = [];
            foreach($form->get('sort')->getErrors() as $error) {
                $errors[] = $error->getMessage();
            }

            $this->addFlash("error",
                implode(",", $errors)
            );
        }
        return $this->redirectToRoute("admin_news_category", $this->getReturnPageParameters());
    }


    #[Route(
        path: "/delete/{id}",
        name: "admin_news_category_delete",
        requirements: ["id" => "\d+"],
        methods: ["DELETE", "POST"]
    )]
    public function delete(
        Request $request,
        CategoryService $service,
        Category $category
    ): Response {
        $this->setReturnPage($request);
        if($this->isCsrfTokenValid(
            "news_category_delete_". $category->getId(),
            $request->request->get('_token')
        )) {
            try {
                $service->deleteEntity($category);
                $this->addFlash("success", "削除しました");
            } catch (CannotExecuteException $e) {
                $this->addFlash("error", $e->getMessage());
            }
        }
        return $this->redirectToRoute("admin_news_category", $this->getReturnPageParameters());
    }
}