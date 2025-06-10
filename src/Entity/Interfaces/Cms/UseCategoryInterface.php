<?php

namespace App\Entity\Interfaces\Cms;

interface UseCategoryInterface
{
    public function getCategory(): ?CategoryInterface;
    public function setCategory(?CategoryInterface $Category): self;


}