<?php

namespace App\Controller\Mvc;

use App\Entity\Contact\Data;
use App\Event\Contact\Data\Send\PostMailSendEvent;
use App\Event\Contact\Data\Send\PostPersistEvent;
use App\Event\Contact\Data\Send\PreMailSendEvent;
use App\Form\Contact\DataType;
use App\MailConfigure\ContactConfigure;
use App\Repository\Contact\DataRepository;
use App\Service\InquiryControllerService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: "/contact")]
class ContactController extends AbstractController
{
    private const SESSION_NAME = "inquiry_contact";

    private const INDEX_TEMPLATE = "pages/contact/index.html.twig";
    private const INDEX_ROUTE = "contact_index";

    #[Route(
        path: "/",
        name: "contact_index",
        methods: ["GET"]
    )]
    public function index(
        Request $request,
        InquiryControllerService $service
    ): Response {
        $service->setContainer($this->container);
        $form = $service->prepareForm(
            $request,
            new Data(),
            self::SESSION_NAME,
            DataType::class
        );
        return $this->render(self::INDEX_TEMPLATE, [
            "form" => $form->createView(),
            "retry" => false
        ]);
    }

    #[Route(
        path: "/confirm",
        name: "contact_confirm",
        methods: ["POST"]
    )]
    public function confirm(
        Request $request,
        InquiryControllerService $service,
        DataRepository $repository
    ): Response {
        $service->setContainer($this->container);

        $contact = new Data();
        $form = $service->getSubmittedForm(
            $request,
            $contact,
            DataType::class
        );
        if(false === $service->formValidation($request, $contact, $form, $repository, false)) {
            return $this->render(self::INDEX_TEMPLATE, [
                "form" => $form->createView(),
                "retry" => true
            ]);
        }
        return $this->render('pages/contact/confirm.html.twig', [
            "inquiry" => $contact,
            "form" => $form->createView(),
            "confirmForm" => $service
                ->saveAndGetConfirmForm($request, $form, self::SESSION_NAME)
                ->createView()
        ]);
    }

    #[Route(
        path: "/send",
        name: "contact_send",
        methods: ["POST"]
    )]
    public function send(
        Request                  $request,
        InquiryControllerService $service,
        DataRepository           $repository,
        ContactConfigure         $configure,
        EventDispatcherInterface $eventDispatcher
    ): Response {
        $service->setContainer($this->container);
        if(!$service->handleConfirmForm($request)) {
            return $this->redirectToRoute(self::INDEX_ROUTE);
        }
        $contact = new Data();
        if(!$form = $service->loadAndGetForm(
            $request,
            $contact,
            DataType::class,
            self::SESSION_NAME
        )) {
            return $this->redirectToRoute(self::INDEX_ROUTE);
        }

        if(!$service->formValidation($request, $contact, $form, $repository)) {
            return $this->render(self::INDEX_TEMPLATE, [
                "form" => $service->createRetryForm(
                    DataType::class,
                    $contact,
                    $form
                )->createView(),
                "retry" => true
            ]);
        }
        $preEvent = new PreMailSendEvent($contact, $form);
        $eventDispatcher->dispatch($preEvent, PreMailSendEvent::EVENT_NAME);

        $sendSuccess = true;
        if(!$service->mailSend($configure, "client", $contact, $form)) {
            $sendSuccess = false;
        }
        if(!$service->mailSend($configure, "reply", $contact, $form)) {
            $sendSuccess = false;
        }
        if(false === $sendSuccess) {
            return $this->render(self::INDEX_TEMPLATE, [
                "form" => $service->createRetryForm(
                    DataType::class,
                    $contact,
                    "送信処理に失敗しました"
                )->createView(),
                "retry" => true
            ]);
        }
        $postSendEvent = new PostMailSendEvent($contact, $form);
        $eventDispatcher->dispatch($postSendEvent, PostMailSendEvent::EVENT_NAME);

        $repository->add($contact);

        $postEvent = new PostPersistEvent($contact, $form);
        $eventDispatcher->dispatch($postEvent, PostPersistEvent::EVENT_NAME);

        // pardot送信などの場合ここでHTMLレンダリング
//        return $this->render('pages/contact/send.html.twig', [
//            "inquiry" => $contact
//        ]);

        return $this->redirectToRoute("contact_complete");
    }

    #[Route(
        path: "/complete",
        name: "contact_complete",
        methods: ["GET"]
    )]
    public function complete(
        Request $request
    ): Response {
        $request->getSession()->remove(self::SESSION_NAME);
        return $this->render('pages/contact/complete.html.twig', []);
    }

    #[Route(
        path: "/failure",
        name: "contact_failure",
        methods: ["GET"]
    )]
    public function failure(
        Request $request,
        InquiryControllerService $service,
        ContactConfigure $configure
    ): Response {
        $service->setContainer($this->container);
        $contact = new Data();
        if(!$form = $service->loadAndGetForm(
            $request,
            $contact,
            DataType::class,
            self::SESSION_NAME
        )) {
            return $this->redirectToRoute("contact_complete");
        }
        $service->mailSend($configure, "pardotFailure", $contact, $form);

        return $this->redirectToRoute("contact_complete");
    }

    #[Route(
        path: "/pardot_mock",
        name: "contact_pardot_mock",
        methods: ["POST"]
    )]
    public function pardotMock(
        Request $request,
        LoggerInterface $logger
    ): Response {
        $logger->info('Contact pardot send: '. print_r($request->request->all(), true));
        return $this->redirectToRoute("contact_failure");
    }

    #[Route(
        path: "/mock",
        name: "contact_mock",
        methods: ["GET"]
    )]
    public function mock(
        Request $request,
        InquiryControllerService $service
    ): Response {
        $service->setContainer($this->container);
        $contact = $service->createMock(Data::class)
            ->setName('鈴木一郎')
            ->setMessage('お問い合わせのローカルの動作テストです')
            ;
        $service->saveMock($request, $contact, DataType::class, self::SESSION_NAME);
        return $this->redirectToRoute(self::INDEX_ROUTE);
    }
}