<?php

namespace App\Controller\Admin;

use App\Entity\Account\Account;
use App\Exception\CannotExecuteException;
use App\Form\Admin\Account\AccountSearchType;
use App\Form\Admin\Account\SelfType;
use App\Repository\Account\AccountRepository;
use App\Service\Entity\Account\AccountService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use TripleE\Utilities\Controller\IndexListTrait;
use TripleE\Utilities\Controller\ReturnPageTrait;

#[Route(path: "/admin/account")]
class AccountController extends AbstractController
{
    use IndexListTrait;
    use ReturnPageTrait;

    #[Route(
        path: "/{page}",
        name: "admin_account",
        requirements: ["page" => "\d+"],
        methods: ["GET", "POST"]
    )]
    public function index(
        Request $request,
        AccountRepository $repository,
        int $page = 1
    ): Response
    {
        $form = $this->createForm(AccountSearchType::class);
        $paginate = $this->handleResumedList(
            $request,
            $form,
            $repository,
            "admin_account_index",
            $page
        );
        return $this->render("admin/account/index.html.twig", [
            "data" => $paginate->getPaginator()->getIterator(),
            "paginate" => $paginate,
            "form" => $form->createView()
        ]);
    }

    #[Route(
        path: "/add",
        name: "admin_account_add",
        methods: ["GET", "POST"]
    )]
    #[Route(
        path: "/edit/{id}",
        name: "admin_account_edit",
        requirements: ["id" => "\d+"],
        methods: ["GET", "POST"]
    )]
    public function form(
        Request $request,
        AccountService $service,
        Account $account = null
    ): Response {
        $service->setContainer($this->container);
        $this->setReturnPage($request);
        $isSelf = false;
        if($account) {
            $isSelf = $account === $this->getUser();
        } else {
            $account = $service->createNewEntity();
        }

        $form = $service->createForm($account);
        if("POST" === $request->getMethod()) {
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()) {
                $res = $service->persistEntity($account, $form);

                $this->addFlash('success', sprintf(
                    "アカウントを%sしました",
                    $res === AccountService::CREATED ? "作成" : "保存"
                ));
                return $this->redirectToRoute("admin_account", $this->getReturnPageParameters());
            } else {
                $this->addFlash("error", "入力に不備があります");
            }
        }

        return $this->render('admin/account/form.html.twig', [
            "form" => $form->createView(),
            "isCreate" => !$account->getId(),
            "isSelf" => $isSelf,
            "returnParams" => $this->getReturnPageParameters()
        ]);
    }

    #[Route(
        path: "/delete/{id}",
        name: "admin_account_delete",
        requirements: ["id" => "\d+"],
        methods: ["DELETE", "POST"]
    )]
    public function delete(
        Request $request,
        AccountService $service,
        Account $account
    ): Response {
        $this->setReturnPage($request);
        if($this->isCsrfTokenValid(
            "account_delete_". $account->getId(),
            $request->request->get('_token')
        )) {
            try {
                $service->deleteEntity($account);
                $this->addFlash("success", "削除しました");
            } catch (CannotExecuteException $e) {
                $this->addFlash("error", $e->getMessage());
            }
        }
        return $this->redirectToRoute("admin_account", $this->getReturnPageParameters());
    }

    #[Route(
        path: "/password_update",
        name: "admin_account_password_update",
        methods: ["GET", "POST"]
    )]
    public function passwordUpdate(
        Request $request,
        AccountService $service
    ): Response {
        $service->setContainer($this->container);
        $form = $this->createForm(SelfType::class, $this->getUser());
        if("POST" === $request->getMethod()) {
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()) {
                $service->persistEntity($this->getUser(), $form);
                $this->addFlash("success", "アカウント情報を更新しました");
                return $this->redirectToRoute("admin_account_password_update");
            } else {
                $this->addFlash("error", "入力に不備があります");
            }
        }
        return $this->render('admin/account/passwordUpdate.html.twig', [
            "form" => $form->createView(),
        ]);
    }
}