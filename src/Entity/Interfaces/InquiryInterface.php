<?php

namespace App\Entity\Interfaces;

interface InquiryInterface
{
    public function getId(): ?int;
    public function getEmail(): ?string;
    public function setEmail(string $email): self;
    public function getIp(): ?string;
    public function setIp(string $ip): self;
}