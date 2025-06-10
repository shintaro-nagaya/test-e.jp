<?php

namespace App\Form\Traits;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

trait MasterEntityTypeTrait
{
    public function addMasterEntityTypes(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                "label" => $options['name_label'],
                "attr" => [
                    "maxlength" => $options["name_length"]
                ],
                "constraints" => [
                    new NotBlank([
                        "message" => "未入力です",
                    ]),
                    new Length([
                        "max" => $options["name_length"],
                        "maxMessage" => "文字数オーバーです"
                    ])
                ]
            ])
            ->add('enable', CheckboxType::class, [
                "label" => "有効",
                "required" => false,
            ])
            ;
        $this->addSortType($builder);
    }
    public function addSortType(FormBuilderInterface $builder): void
    {
        $builder
            ->add('sort', IntegerType::class, [
                "label" => "順番",
                "attr" => [
                    "min" => 1,
                    "max" => 9999,
                ],
                "constraints" => [
                    new Range([
                        "min" => 1,
                        "max" => 9999,
                        "notInRangeMessage" => "1〜9999までで入力してください"
                    ])
                ]
            ])
            ;
    }

    public function addMasterEntitySearchTypes(FormBuilderInterface $builder): void
    {
        $builder
            ->add('enable', ChoiceType::class, [
                "label" => "有効",
                "choices" => [
                    "有効のみ" => true,
                    "無効のみ" => false,
                ],
                "required" => false,
                "placeholder" => ""
            ])
            ;
    }
    public function masterEntityTypeConfigureOptions(): OptionsResolver
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            "name_label" => "名称",
            "name_length" => 64
        ]);
        $resolver->setAllowedTypes("name_label", "string");
        $resolver->setAllowedTypes("name_length", "int");

        return $resolver;
    }
}