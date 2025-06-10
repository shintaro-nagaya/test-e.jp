<?php

namespace App\Entity\Interfaces\Cms;

interface LinkInterface
{
    public function getLinkUrl(): ?string;
    public function setLinkUrl(?string $linkUrl): self;
    public function getLinkNewTab(): ?bool;
    public function setLinkNewTab(bool $linkNewTab): self;
}