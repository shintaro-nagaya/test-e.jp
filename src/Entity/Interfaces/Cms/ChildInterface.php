<?php

namespace App\Entity\Interfaces\Cms;

interface ChildInterface
{
    public function getId(): ?int;
    public function getSort(): ?int;
    public function setSort(int $sort): self;
    public function getEntry(): ?EntryInterface;
    public function setEntry(?EntryInterface $Entry): self;
}