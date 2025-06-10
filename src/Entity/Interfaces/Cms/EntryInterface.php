<?php

namespace App\Entity\Interfaces\Cms;

interface EntryInterface
{
    public function getId(): ?int;
    public function getEntryDate(): ?\DateTimeInterface;
    public function setEntryDate(\DateTimeInterface $entryDate): self;
    public function getTitle(): ?string;
    public function setTitle(string $title): self;
    public function getEnable(): ?bool;
    public function setEnable(bool $enable): self;
    public function getDescription(): ?string;
    public function setDescription(?string $description): self;
    public function getContent(): ?string;
    public function setContent(?string $content): self;

    /**
     * descriptionを返す、未設定の場合titleを返す様な実装をする
     * @return string
     */
    public function descriptionForHtml(): string;
}