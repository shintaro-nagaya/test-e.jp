<?php

namespace App\Entity\Interfaces\Cms;

interface PublishDateInterface
{
    public function getPublishDate(): ?\DateTimeInterface;
    public function setPublishDate(?\DateTimeInterface $publishDate): self;
    public function getCloseDate(): ?\DateTimeInterface;
    public function setCloseDate(?\DateTimeInterface $closeDate): self;

    /**
     * 公開時間に現在がいるか判定
     * @return bool
     */
    public function isPublishByTimer(): bool;
}