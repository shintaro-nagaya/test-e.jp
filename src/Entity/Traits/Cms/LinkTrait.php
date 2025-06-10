<?php

namespace App\Entity\Traits\Cms;

use Doctrine\ORM\Mapping as ORM;

trait LinkTrait
{

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $linkUrl = null;

    #[ORM\Column(type: 'boolean')]
    private ?bool $linkNewTab = false;

    public function getLinkUrl(): ?string
    {
        return $this->linkUrl;
    }

    public function setLinkUrl(?string $linkUrl): self
    {
        $this->linkUrl = $linkUrl;

        return $this;
    }

    public function getLinkNewTab(): ?bool
    {
        return $this->linkNewTab;
    }

    public function setLinkNewTab(bool $linkNewTab): self
    {
        $this->linkNewTab = $linkNewTab;

        return $this;
    }

}