<?php

namespace App\MailConfigure;

use App\Entity\Interfaces\InquiryInterface;
use App\Service\MailService;

class ContactConfigure implements MailConfigureInterface
{
    public function __construct(
        private MailService $mailService
    ) {}

    public function getMailService(): MailService
    {
        return $this->mailService;
    }

    public function getOption(string $type, ?InquiryInterface $inquiry = null, array $twigAssign = []): ?array
    {
        if(!$inquiry) {
            throw new \InvalidArgumentException(get_class($this). "::getOption() InquiryInterface required");
        }
        $twigAssign = array_merge($twigAssign, [
            "inquiry" => $inquiry
        ]);
        $resolver = $this->mailService->configureOptions();
        return match ($type) {
            "client" => $resolver->resolve([
                "twigTextTemplate" => "mail/contact/client.txt.twig",
                "twigAssign" => $twigAssign,
                "subject" => "お問い合わせがありました",
            ]),
            "reply" => $resolver->resolve([
                "twigTextTemplate" => "mail/contact/reply.txt.twig",
                "twigAssign" => $twigAssign,
                "subject" => "【自動返信】お問い合わせを承りました",
                "to" => [$inquiry->getEmail()]
            ]),
            "pardotFailure" => $resolver->resolve([
                "twigTextTemplate" => "mail/contact/pardot_error.txt.twig",
                "twigAssign" => $twigAssign,
                "subject" => "【お問い合わせ】パードット送信が失敗しました"
            ]),
            default => null,
        };
    }
}