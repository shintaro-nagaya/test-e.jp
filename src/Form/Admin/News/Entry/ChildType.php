<?php

namespace App\Form\Admin\News\Entry;

use App\Entity\News\Child;
use App\Form\Traits\CmsEntryTypeTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChildType extends AbstractType
{
    use CmsEntryTypeTrait;
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this
            ->addCmsChildTypes($builder)
            ->addCmsChildContentTypes($builder)
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Child::class,
        ]);
    }
}
