<?php

namespace App\Form\Traits;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

trait InquiryTypeTrait
{
    public function addRequiredTypes(
        FormBuilderInterface $builder,
        string               $emailLabel = "メールアドレス"
    ): FormBuilderInterface
    {
        $builder
            ->add('email', EmailType::class, [
                "label" => $emailLabel,
                "constraints" => [
                    new NotBlank([
                        "message" => "未入力です"
                    ]),
                    new Email([
                        "mode" => "html5",
                        "message" => "形式違反です"
                    ])
                ]
            ])
            ->add('agreement', CheckboxType::class, [
                "label" => "個人情報の取扱いに同意する",
                "constraints" => [
                    new NotBlank([
                        "message" => "ご同意をお願いします"
                    ])
                ],
                "mapped" => false
            ]);
        return $builder;
    }
}