<?php

namespace App\Entity\Interfaces\Cms;

interface ChildContentInterface
{
    public function getHeadline(): ?string;
    public function setHeadline(?string $headline): self;
    public function getImage(): ?string;
    public function setImage(?string $image): self;
    public function getImageWidth(): ?int;
    public function setImageWidth(?int $image_width): self;
    public function getImageHeight(): ?int;
    public function setImageHeight(?int $image_height): self;
    public function getContent(): ?string;
    public function setContent(?string $content): self;
    public function haveContent(): bool;
    public function getImageUrl(): string;
}