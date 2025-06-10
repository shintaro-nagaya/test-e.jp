<?php

namespace App\Form\Admin\News\Category;

use App\Form\Traits\MasterEntityTypeTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    use MasterEntityTypeTrait;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->addMasterEntitySearchTypes($builder);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
