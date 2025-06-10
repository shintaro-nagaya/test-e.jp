<?php

namespace App\Form\Admin\News\Entry;

use App\Form\Traits\CmsEntryTypeTrait;
use App\Repository\News\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    use CmsEntryTypeTrait;
    public function __construct(
        private CategoryRepository $categoryRepository
    ) {}
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this
            ->addCmsEntrySearchTypes($builder)
            ->addCmsEntryCategoryType($builder, $this->cmsEntryCategoryConfigurationOptions()->resolve([
                "choices" => $this->categoryRepository->getMasterKeyValueArray(),
                "required" => false,
                "placeholder" => "",
                "constraints" => []
            ]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
