<?php

namespace App\Form\Traits;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

trait InquiryDataSearchTypeTrait
{
    public function addSearchTypes(FormBuilderInterface $builder): FormBuilderInterface
    {
        $builder
            ->add('send_from', DateType::class, [
                "label" => "送信日",
                "required" => false,
                "widget" => "single_text"
            ])
            ->add('send_to', DateType::class, [
                "label" => "送信日",
                "required" => false,
                "widget" => "single_text"
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
        return $builder;
    }
}