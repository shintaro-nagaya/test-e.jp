<?php

namespace App\Form\Admin\News\Entry;

use App\Entity\News\Category;
use App\Entity\News\Entry;
use App\Form\Traits\CmsEntryTypeTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntryType extends AbstractType
{
    use CmsEntryTypeTrait;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this
            ->addCmsEntryTypes($builder, $this->cmsEntityConfigureOptions()->resolve([]))
            ->addCmsPublishDateTypes($builder)
            ->addCmsImageTypes($builder)
            ->addCmsLinkTypes($builder)
            ->addCmsEntryCategoryType($builder, $this->cmsEntryCategoryConfigurationOptions()->resolve([
                "class" => Category::class,
            ]))
            ->addChildrenTypes($builder, ChildType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Entry::class,
        ]);
    }
}
