<?php

namespace App\Controller\Admin;

use App\Form\Admin\Account\SelfType;
use App\Service\Entity\Account\AccountService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: "/admin/edit_self")]
class EditSelfController extends AbstractController
{
    #[Route(
        path: "/",
        name: "admin_edit_self",
        methods: ["GET", "POST"]
    )]
    public function form(
        Request $request,
        AccountService $service
    ): Response {
        $service->setContainer($this->container);
        $account = $this->getUser();
        $form = $this->createForm(SelfType::class, $account);
        if("POST" === $request->getMethod()) {
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()) {
                $service->persistEntity($account, $form);

                $this->addFlash("success", "保存しました");
                return $this->redirectToRoute("admin_edit_self");
            } else {
                $this->addFlash("error", "入力に不備があります");
            }
        }
        return $this->render("admin/edit_self/form.html.twig", [
            "form" => $form->createView()
        ]);
    }
}