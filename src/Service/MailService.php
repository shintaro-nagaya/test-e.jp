<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;
use Twig\Error\Error as TwigError;

class MailService
{
    private array $delivery = [];

    public function __construct(
        private MailerInterface $mailer,
        private ParameterBagInterface $parameterBag,
        private Environment $twig,
        private LoggerInterface $logger
    ) {
    }

    public function configureOptions(): OptionsResolver {
        return (new OptionsResolver())
            ->setDefaults([
                "from" => $this->parameterBag->get('mail.from'),
                "fromName" => $this->parameterBag->get('mail.from_name'),
                "replyTo" => $this->parameterBag->get('mail.reply_to'),
                "returnPath" => $this->parameterBag->get('mail.return_path'),
                "to" => $this->parameterBag->get('mail.to'),
                "subject" => null,
                "cc" => null,
                "bcc" => null,
                "twigAssign" => [],
                "twigTextTemplate" => null,
                "twigHtmlTemplate" => null,
                "message" => null,
//          "attach" => [
//            [
//                [
//                    path: "path/to/file.jpg", required
//                    name: "custom file name", optional
//                    mime: "mime type", optional
//                ], or
//                "path/to/file.jpg"
//            ]
//          ]
                "attach" => []
            ])
            ->setRequired([
                "from",
                "fromName",
                "to",
                "subject"
            ])
            ->setAllowedTypes("from", "string")
            ->setAllowedTypes("fromName", "string")
            ->setAllowedTypes("replyTo", ["null", "string"])
            ->setAllowedTypes("returnPath", ["null","string"])
            ->setAllowedTypes("to", "string[]")
            ->setAllowedTypes("subject", "string")
            ->setAllowedTypes("cc", ["null", "string[]"])
            ->setAllowedTypes("bcc", ["null", "string[]"])
            ->setAllowedTypes("twigAssign", ["null", "array"])
            ->setAllowedTypes("twigTextTemplate", ["null","string"])
            ->setAllowedTypes("twigHtmlTemplate", ["null","string"])
            ->setAllowedTypes("message", ["null","string"])
            ->setAllowedTypes("attach", ["array"])
            ;

    }
    /**
     * @param array $config
     * @param bool $sendNow
     * @return Email
     * @throws TransportExceptionInterface
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function send(array $config, bool $sendNow = true): Email
    {
        $this->delivery = [];

        if(null === $config['subject']) {
            throw new \InvalidArgumentException('Mail subject config required');
        }
        if(
            null === $config['twigTextTemplate'] &&
            null === $config['twigHtmlTemplate'] &&
            null === $config['message']
        ) {
            throw new \InvalidArgumentException('Mail twigTextTemplate or twigHtmlTemplate or message required');
        }

        $email = (new Email())
            ->from(new Address($config['from'], $config['fromName']))
            ->replyTo($config['replyTo'])
            ->returnPath($config['returnPath'])
            ->subject($config['subject'])
            ;
        $this->addAddress($email, "addTo", $config['to']);
        if(null !== $config['cc']) {
            $this->addAddress($email, "addCc", $config['cc']);
        }
        if(null !== $config['bcc']) {
            $this->addAddress($email, "addBcc", $config['bcc']);
        }
        if($config['twigHtmlTemplate']) {
            try {
                $email->html(
                    $this->twig->render($config['twigHtmlTemplate'], $config['twigAssign'])
                );
            } catch (TwigError $e) {
                $this->logger->error('Twig error. '. $e->__toString());
                throw $e;
            }
        }
        if($config['twigTextTemplate']) {
            try {
                $email->text(
                    $this->twig->render($config['twigTextTemplate'], $config['twigAssign'])
                );
            } catch (TwigError $e) {
                $this->logger->error('Twig error. '. $e->__toString());
                throw $e;
            }
        } else {
            $email->text($config['message']);
        }
        foreach($config['attach'] as $file) {
            if(is_array($file) && isset($file['path'])) {
                $path = $file['path'];
                $name = $file['name']?? null;
                $mime = $file['mime']?? null;
                $email->attachFromPath($path, $name, $mime);
            } else {
                if(null !== $file) {
                    throw new \InvalidArgumentException('Mail attachment file path unset.');
                }
                $email->attachFromPath($file);
            }
        }
        if(false === $sendNow) {
            return $email;
        }
        $this->sendMail($email);
        return $email;
    }

    /**
     * 送信先を登録
     *
     * @param Email $email
     * @param string $method    "addTo"|"addCc"|"addBcc"
     * @param $addresses string | array
     *  "address@example.com" or [
     *      0 => "address@example.com",
     *      "Name" => "name@example.com
     * ]
     */
    private function addAddress(Email $email, string $method, string|array $addresses): void
    {
        if(is_string($addresses)) {
            $email->$method($addresses);
            $this->delivery[] = $addresses;
        } elseif(is_array($addresses)) {
            foreach($addresses as $k => $address) {
                if(is_numeric($k)) {
                    $email->$method($address);
                } else {
                    $email->$method(new Address($address, $k));
                }
                $this->delivery[] = $address;
            }
        }
    }

    /**
     * メール送信実行、送信件名と送信先をログに書き込み
     *
     * @param Email $email
     * @throws TransportExceptionInterface
     */
    public function sendMail(Email $email): void
    {
        try {
            $this->mailer->send($email);
            $this->logger->info(
                "Mail send. ". $email->getSubject(). " Delivery To: ". implode(",", $this->delivery)
            );
        } catch (TransportExceptionInterface $e) {
            $this->logger->warning('Mail transport failure. '. $e->__toString());
            throw $e;
        }
    }
}