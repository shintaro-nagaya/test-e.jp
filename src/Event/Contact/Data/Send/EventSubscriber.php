<?php

namespace App\Event\Contact\Data\Send;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            PreMailSendEvent::EVENT_NAME => 'onPreMailSend',
            PostMailSendEvent::EVENT_NAME => 'onPostMailSend',
            PostPersistEvent::EVENT_NAME => 'onPostPersist',
        ];
    }

    public function onPreMailSend(PreMailSendEvent $event)
    {
        // $contact = $event->getContact();
        // $form = $event->getForm();
        // 永続化前に何か行う
    }

    public function onPostPersist(PostPersistEvent $event)
    {
        // $contact = $event->getContact();
        // $form = $event->getForm();
        // 永続化後に何か行う
    }

    public function onPostMailSend(PostMailSendEvent $event)
    {
        // $contact = $event->getContact();
        // $form = $event->getForm();
        // メール送信後に何か行う
    }
}