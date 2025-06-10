<?php

namespace App\Form\Admin\News\Category;

use App\Form\Traits\MasterEntityTypeTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortType extends AbstractType
{
    use MasterEntityTypeTrait;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->addSortType($builder);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            "csrf_protection" => false
        ]);
    }
}
