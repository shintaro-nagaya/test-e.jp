<?php

namespace App\Service;

use App\Entity\Interfaces\InquiryInterface;
use App\MailConfigure\MailConfigureInterface;
use App\Repository\Interfaces\InquiryRepositoryInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use ReCaptcha\ReCaptcha;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Email;
use TripleE\Utilities\FormUtil;
use TripleE\Utilities\ParameterBagUtil;

class InquiryControllerService
{
    protected ContainerInterface $container;

    public function __construct(
        private ReCaptcha $reCaptcha,
        private LoggerInterface $logger
    ) {}

    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    /**
     * フォーム入力(indexページ)で使用するフォームを準備する。セッションにデータがあれば割り当てる
     * @param Request $request
     * @param InquiryInterface $inquiry
     * @param string $sessionName
     * @param string $formType
     * @param array $formOptions
     * @return FormInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function prepareForm(
        Request $request,
        InquiryInterface $inquiry,
        string $sessionName,
        string $formType,
        array $formOptions = []
    ): FormInterface {
        $form = $this->getForm($formType, $inquiry, $formOptions);
        if($request->getSession()->has($sessionName)) {
            $form->submit($request->getSession()->get($sessionName));
        }
        return $form;
    }

    /**
     * フォームを作成
     * @param string $formType
     * @param InquiryInterface $inquiry
     * @param array $formOptions
     * @return FormInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getForm(string $formType, InquiryInterface $inquiry, array $formOptions): FormInterface
    {
        return $this->container->get('form.factory')->create($formType, $inquiry, $formOptions);
    }

    /**
     * 確認ページでFormにRequestをアサインして送信済みFormを返す
     * @param Request $request
     * @param InquiryInterface $inquiry
     * @param string $formType
     * @param array $formOptions
     * @return FormInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getSubmittedForm(
        Request $request,
        InquiryInterface $inquiry,
        string $formType,
        array $formOptions = []
    ): FormInterface
    {
        $form = $this->getForm($formType, $inquiry, $formOptions);
        $form->handleRequest($request);
        return $form;
    }

    /**
     * フォームのバリデーションを行う
     * @param Request $request
     * @param InquiryInterface $inquiry
     * @param FormInterface $form
     * @param InquiryRepositoryInterface $repository
     * @param string $thresholdTime
     * @return bool
     */
    public function formValidation(
        Request $request,
        InquiryInterface $inquiry,
        FormInterface $form,
        InquiryRepositoryInterface $repository,
        bool $checkReCaptcha = true,
        string $thresholdTime = "-20 second"
    ): bool {
        $isValid = true;
        if($form->isSubmitted() && $form->isValid()) {
            $inquiry->setIp($request->getClientIp());
            if (
                ParameterBagUtil::$bag->get('kernel.environment') === "dev" &&
                ParameterBagUtil::$bag->get('inquiry.form_validation') === "false"
            ) {
                return true;
            }
            if ($repository->isContinuePost($inquiry, $thresholdTime)) {
                $form->addError(new FormError("連続送信は時間をおいてください"));
                $isValid = false;
            }
            if($checkReCaptcha) {
                $reCaptchaResponse = $this->reCaptcha->verify(
                    $request->request->get('g-recaptcha-response'),
                    $request->getClientIp()
                );
                if (!$reCaptchaResponse->isSuccess()) {
                    $form->addError(new FormError("不正な処理を感知しました"));
                    $this->logger->notice(
                        "reCaptcha invalid. ". print_r($reCaptchaResponse->getErrorCodes(), true)
                    );
                    $isValid = false;
                }
            }
        } else {
            $form->addError(new FormError("入力内容に不備がありました"));
            $isValid = false;
        }
        return $isValid;
    }

    /**
     * 送信前のCSRFトークンチェック
     * @param Request $request
     * @return bool
     */
    public function handleConfirmForm(Request $request): bool
    {
        $form = $this->getConfirmForm();
        $form->handleRequest($request);
        return ($form->isSubmitted() && $form->isValid());
    }

    public function getConfirmForm(): FormInterface
    {
        return $this->container->get('form.factory')->create();
    }

    /**
     * セッションに保存されているデータを持たせたフォームを返す
     * @param Request $request
     * @param InquiryInterface $inquiry
     * @param string $formType
     * @param string $sessionName
     * @param array $formOptions
     * @return FormInterface|null
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function loadAndGetForm(
        Request $request,
        InquiryInterface $inquiry,
        string $formType,
        string $sessionName,
        array $formOptions = []
    ): ?FormInterface
    {
        if(!$request->getSession()->has($sessionName)) {
            return null;
        }
        $form = $this->container->get('form.factory')->create($formType, $inquiry, array_merge(
            $formOptions,
            [
                "csrf_protection" => false
            ]
        ));
        $form->submit($request->getSession()->get($sessionName));

        return $form;
    }

    /**
     * メールを送信する
     * @param MailConfigureInterface $configure
     * @param string $type
     * @param InquiryInterface $inquiry
     * @param FormInterface $form
     * @param array $twigAssign
     * @return Email|null
     */
    public function mailSend(
        MailConfigureInterface $configure,
        string $type,
        InquiryInterface $inquiry,
        FormInterface $form,
        array $twigAssign = []
    ): ?Email {
        $twigAssign["form"] = $form->createView();

        $config = $configure->getOption($type, $inquiry, $twigAssign);

        try {
            return $configure->getMailService()->send($config);
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
            return null;
        }
    }

    /**
     * モックデータの作成
     * @param string $className
     * @return InquiryInterface
     */
    public function createMock(string $className): InquiryInterface
    {
        return (new $className)
            ->setEmail('info@triple-e.inc')
            ;
    }

    /**
     * @param Request $request
     * @param InquiryInterface $inquiry
     * @param string $formType
     * @param string $sessionName
     * @param array $formOptions
     * @return void
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function saveMock(
        Request $request,
        InquiryInterface $inquiry,
        string $formType,
        string $sessionName,
        array $formOptions = []
    ): void {
        $form = $this->getForm($formType, $inquiry, $formOptions);
        $data = FormUtil::getArrayData($form);
        $request->getSession()->set($sessionName, $data);
    }

    /**
     * Formの内容をセッションに保存して確認ページ用のからのフォームを返す
     * @param Request $request
     * @param FormInterface $form
     * @param string $sessionName
     * @return FormInterface
     */
    public function saveAndGetConfirmForm(
        Request $request,
        FormInterface $form,
        string $sessionName
    ): FormInterface {
        $request->getSession()->set($sessionName, FormUtil::getViewData($form));
        return $this->getConfirmForm();
    }

    /**
     * 送信失敗時にリトライ用のフォームを作成する
     * @param string $formType
     * @param InquiryInterface $inquiry
     * @param string|null $errorMessage
     * @param array $formOptions
     * @return FormInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function createRetryForm(
        string $formType,
        InquiryInterface $inquiry,
        string|FormInterface|null $errorMessage = null,
        array $formOptions = array()
    ): FormInterface
    {
        $form = $this->getForm($formType, $inquiry, $formOptions);
        $form->get('agreement')->setData(true);
        if(is_string($errorMessage)) {
            $form->addError(new FormError($errorMessage));
        } elseif($errorMessage) {
            foreach($errorMessage->getErrors() as $error) {
                $form->addError($error);
            }
        }

        return $form;
    }
}