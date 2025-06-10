<?php

namespace App\Entity\Interfaces;

interface MasterEntityInterface
{
    public function getName(): ?string;
    public function setName(string $name): self;
    public function getEnable(): ?bool;
    public function setEnable(bool $enable): self;
    public function getSort(): ?int;
    public function setSort(int $sort): self;
}