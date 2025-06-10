<?php

namespace App\Form\Admin\Account;

use App\Entity\Account\Account;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SelfType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                "label" => "メールアドレス",
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
            ->add('plain_password', PasswordType::class, [
                "label" => "パスワード",
                "required" => false,
                "help" => "パスワード変更時に入力してください",
                "mapped" => false,
                "attr" => [
                    "placeholder" => "半角英数字記号で6文字以上"
                ],
                "constraints" => [
                    new Length([
                        "min" => 6,
                        "minMessage" => "6文字以上で設定してください"
                    ])
                ]
            ])
            ->add('name', TextType::class, [
                "label" => "名称",
                "attr" => [
                    "maxlength" => 48
                ],
                "constraints" => [
                    new NotBlank([
                        "message" => "未入力です"
                    ]),
                    new Length([
                        "max" => 48,
                        "maxMessage" => "文字数オーバーです"
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Account::class,
        ]);
    }
}
