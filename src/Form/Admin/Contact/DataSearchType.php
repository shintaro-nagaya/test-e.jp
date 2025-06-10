<?php

namespace App\Form\Admin\Contact;

use App\Form\Traits\InquiryDataSearchTypeTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DataSearchType extends AbstractType
{
    use InquiryDataSearchTypeTrait;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->addSearchTypes($builder);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
