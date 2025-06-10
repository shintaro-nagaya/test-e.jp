<?php

namespace App\Form\Admin\News\Category;

use App\Entity\News\Category;
use App\Form\Traits\MasterEntityTypeTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    use MasterEntityTypeTrait;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $resolver = $this->masterEntityTypeConfigureOptions();
        $this->addMasterEntityTypes($builder, $resolver->resolve([]));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
