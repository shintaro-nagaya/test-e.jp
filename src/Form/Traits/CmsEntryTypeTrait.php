<?php

namespace App\Form\Traits;

use App\Repository\Interfaces\MasterEntityRepositoryInterface;
use App\Utils\CKEditorConfigUtil;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

trait CmsEntryTypeTrait
{
    /**
     * CMS 基本項目
     * @param FormBuilderInterface $builder
     * @param array $option
     * @return $this
     */
    public function addCmsEntryTypes(FormBuilderInterface $builder, array $option): self
    {
        $builder
            ->add('entryDate', DateType::class, [
                "label" => "記事日付",
                "widget" => "single_text",
                "constraints" => [
                    new NotBlank([
                        "message" => "未入力です"
                    ])
                ]
            ])
            ->add('title', TextType::class, [
                "label" => $option["title_label"],
                "attr" => [
                    "max" => $option["title_length"]
                ],
                "constraints" => [
                    new NotBlank([
                        "message" => "未入力です"
                    ]),
                    new Length([
                        "max" => $option["title_length"],
                        "maxMessage" => "文字数オーバーです"
                    ])
                ]
            ])
            ->add('enable', CheckboxType::class, [
                "label" => "公開",
                "required" => false
            ])
            ->add('content', CKEditorType::class, [
                "label" => $option["content_label"],
                "required" => false,
                "config" => CKEditorConfigUtil::$defaultConfig
            ])
            ->add('description', TextType::class, [
                "label" => "デスクリプション",
                "required" => false,
                "attr" => [
                    "max" => 255
                ],
                "constraints" => [
                    new Length([
                        "max" => 255,
                        "maxMessage" => "文字数オーバーです"
                    ])
                ]
            ])
            ;
        return $this;
    }

    /**
     * addCmsEntryTypes()のoption
     * @return OptionsResolver
     */
    public function cmsEntityConfigureOptions(): OptionsResolver
    {
        return (new OptionsResolver())
            ->setDefaults([
                "title_label" => "タイトル",
                "title_length" => 255,
                "content_label" => "本文"
            ])
            ->setAllowedTypes("title_label", "string")
            ->setAllowedTypes("title_length", "int")
            ->setAllowedTypes("content_label", "string")
            ;
    }

    /**
     * 公開・終了日時項目
     * @param FormBuilderInterface $builder
     * @return $this
     */
    public function addCmsPublishDateTypes(FormBuilderInterface $builder): self {
        $builder
            ->add('publishDate', DateTimeType::class, [
                "label" => "公開時間",
                "required" => false,
                "date_widget" => "single_text"
            ])
            ->add('closeDate', DateTimeType::class, [
                "label" => "公開終了時間",
                "required" => false,
                "date_widget" => "single_text"
            ])
            ;
        return $this;
    }

    /**
     * 画像項目
     * @param FormBuilderInterface $builder
     * @return $this
     */
    public function addCmsImageTypes(FormBuilderInterface $builder): self {
        $builder
            ->add('mainImage', TextType::class, [
                "label" => "画像",
                "required" => false,
                "attr" => [
                    "class" => "d-none"
                ]
            ])
            ->add('mainImageWidth', HiddenType::class, [
                "required" => false
            ])
            ->add('mainImageHeight', HiddenType::class, [
                "required" => false
            ])
            ->add('thumbnail', TextType::class, [
                "label" => "サムネイル",
                "required" => false,
                "attr" => [
                    "class" => "d-none"
                ]
            ])
            ;
        return $this;
    }

    /**
     * 外部リンク項目
     * @param FormBuilderInterface $builder
     * @return $this
     */
    public function addCmsLinkTypes(FormBuilderInterface $builder): self {
        $builder
            ->add('linkUrl', UrlType::class, [
                "label" => "外部リンクURL",
                "required" => false,
                "attr" => [
                    "max" => 255
                ],
                "constraints" => [
                    new Length([
                        "max" => 255,
                        "maxMessage" => "文字数オーバーです"
                    ])
                ]
            ])
            ->add('linkNewTab', CheckboxType::class, [
                "label" => "別タブで開く",
                "required" => false
            ])
            ;
        return $this;
    }

    /**
     * 一覧検索条件項目
     * @param FormBuilderInterface $builder
     * @return $this
     */
    public function addCmsEntrySearchTypes(FormBuilderInterface $builder): self
    {
        $builder
            ->add('entryDateFrom', DateType::class, [
                "label" => "記事日付",
                "widget" => "single_text",
                "required" => false
            ])
            ->add('entryDateTo', DateType::class, [
                "label" => "記事日付",
                "widget" => "single_text",
                "required" => false
            ])
            ->add('enable', ChoiceType::class, [
                "label" => "公開",
                "choices" => [
                    "公開のみ" => 1,
                    "非公開のみ" => 2,
                ],
                "required" => false,
            ])
            ->add('limit', ChoiceType::class, [
                "choices" => [
                    20 => 20,
                    40 => 40,
                    60 => 60
                ],
                "label" => "表示件数"
            ])
            ;
        return $this;
    }
    public function addCmsEntryCategoryType(FormBuilderInterface $builder, array $option, string $name = "Category"): self
    {
        if(is_array($option['choices'])) {
            $builder
                ->add($name, ChoiceType::class, [
                    "label" => $option['label'],
                    "choices" => $option['choices'],
                    "required" => $option["required"],
                    "placeholder" => $option["placeholder"],
                    "constraints" => $option["constraints"]
                ]);
        } else {
            $builder
                ->add($name, EntityType::class, [
                    "label" => $option['label'],
                    "class" => $option['class'],
                    "query_builder" => function (MasterEntityRepositoryInterface $repository) {
                        return $repository->getMasterData();
                    },
                    "required" => $option["required"],
                    "placeholder" => $option["placeholder"],
                    "constraints" => $option["constraints"]
                ])
            ;
        }
        return $this;
    }
    public function cmsEntryCategoryConfigurationOptions(): OptionsResolver
    {
        return (new OptionsResolver())
            ->setDefaults([
                "label" => "カテゴリー",
                "choices" => null,
                "class" => null,
                "required" => true,
                "placeholder" => false,
                "constraints" => [
                    new NotBlank([
                        "message" => "選択されていません"
                    ])
                ]
            ])
            ->setAllowedTypes("label" , "string")
            ;
    }
    public function addChildrenTypes(FormBuilderInterface $builder, string $entryType, string $name = "children"): self {
        $builder->add($name, CollectionType::class, [
            "entry_type" => $entryType,
            "entry_options" => [
                "label" => false
            ],
            "allow_add" => true,
            "allow_delete" => true,
            "label" => false,
            "by_reference" => false
        ]);
        return $this;
    }

    /**
     * CMS段落の項目
     * @param FormBuilderInterface $builder
     * @return $this
     */
    public function addCmsChildTypes(FormBuilderInterface $builder): self
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
            ->add('delete', CheckboxType::class, [
                "label" => "段落削除",
                "mapped" => false,
                "required" => false
            ])
            ;
        return $this;
    }

    /**
     * Cms段落のコンテンツ項目
     * @param FormBuilderInterface $builder
     * @return $this
     */
    public function addCmsChildContentTypes(FormBuilderInterface $builder): self
    {
        $builder
            ->add('headline', TextType::class, [
                "label" => "見出し",
                "required" => false,
                "attr" => [
                    "max" => 128
                ],
                "constraints" => [
                    new Length([
                        "max" => 128,
                        "maxMessage" => "文字数オーバーです"
                    ])
                ]
            ])
            ->add('image', HiddenType::class, [
                "label" => "画像",
                "required" => false
            ])
            ->add('imageWidth', HiddenType::class, [
                "required" => false
            ])
            ->add('imageHeight', HiddenType::class, [
                "required" => false
            ])
            ->add('content', CKEditorType::class, [
                "label" => "本文",
                "required" => false,
                "config" => CKEditorConfigUtil::$defaultConfig
            ])
            ->add('youtubeId', TextType::class, [
                "label" => "YoutubeID",
                "required" => false
            ])
            ;
        return $this;
    }

    /**
     * CMSの記事一覧での基礎項目
     * @param FormBuilderInterface $builder
     * @return $this
     */
    public function addListTypes(FormBuilderInterface $builder): self
    {
        $builder
            ->add('page', IntegerType::class, [
                "label" => false,
                "required" => false
            ])
            ->add('limit', IntegerType::class, [
                "label" => false,
                "required" => false
            ])
            ;
        return $this;
    }

    public function addListCategoryType(FormBuilderInterface $builder): self
    {
        $builder
            ->add('Category', IntegerType::class, [
                "label" => false,
                "required" => false
            ])
            ;
        return $this;
    }

}