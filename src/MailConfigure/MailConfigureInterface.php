<?php

namespace App\MailConfigure;

use App\Entity\Interfaces\InquiryInterface;
use App\Service\MailService;

interface MailConfigureInterface
{
    public function getMailService(): MailService;
    public function getOption(string $type, ?InquiryInterface $inquiry = null, array $twigAssign = []): ?array;
}