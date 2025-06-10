<?php

namespace App\Entity\Traits\Cms;

use Doctrine\ORM\Mapping as ORM;

trait PublishDateTrait
{
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $publishDate = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $closeDate = null;

    public function getPublishDate(): ?\DateTimeInterface
    {
        return $this->publishDate;
    }

    public function setPublishDate(?\DateTimeInterface $publishDate): self
    {
        $this->publishDate = $publishDate;

        return $this;
    }

    public function getCloseDate(): ?\DateTimeInterface
    {
        return $this->closeDate;
    }

    public function setCloseDate(?\DateTimeInterface $closeDate): self
    {
        $this->closeDate = $closeDate;

        return $this;
    }

    /**
     * 公開時間に達しているか
     * @return bool
     */
    public function isPublishByTimer(): bool
    {
        if($this->publishDate || $this->closeDate) {
            $now = new \DateTime();
            if($this->closeDate && $this->closeDate < $now) {
                return false;
            }
            if($this->publishDate && $this->publishDate > $now) {
                return false;
            }
        }
        return true;
    }
}