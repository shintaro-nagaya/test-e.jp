<?php

namespace App\Form\Admin\Account;

use App\Entity\Account\Account;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class AccountType extends AbstractType
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
            ->add('role_super_admin', CheckboxType::class, [
                "label" => "アカウント管理権限",
                "mapped" => false,
                "required" => false
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
