<?php

namespace App\Event\Contact\Data\Send;

use App\Entity\Contact\Data;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\EventDispatcher\Event;

class PostMailSendEvent extends Event
{
    public const EVENT_NAME = 'contact_data_send.post_mail_send';
    public function __construct(
        private readonly Data $data,
        private readonly FormInterface $form
    ) {}

    public function getData(): Data
    {
        return $this->data;
    }
    public function getForm(): FormInterface
    {
        return $this->form;
    }
}