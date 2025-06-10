<?php

namespace App\Form\Contact;

use App\Entity\Contact\Data;
use App\Form\Traits\InquiryTypeTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class DataType extends AbstractType
{
    use InquiryTypeTrait;
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this
            ->addRequiredTypes($builder)
            ->add('name', TextType::class, [
                "label" => "氏名",
                "attr" => [
                    "maxlength" => 64
                ],
                "constraints" => [
                    new NotBlank([
                        "message" => "未入力です"
                    ]),
                    new Length([
                        "max" => 64,
                        "maxMessage" => "文字数オーバーです"
                    ])
                ]
            ])
            ->add('message', TextareaType::class, [
                "label" => "お問い合わせ内容",
                "constraints" => [
                    new NotBlank([
                        "message" => "未入力です"
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Data::class,
        ]);
    }
}
