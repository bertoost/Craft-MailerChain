<?php

namespace bertoost\mailerchain\traits;

use bertoost\mailerchain\adapters\MailerChainAdapter;
use bertoost\mailerchain\elements\ChainAdapter;
use bertoost\mailerchain\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\helpers\MailerHelper;
use craft\mail\Mailer;
use craft\services\Elements;
use craft\web\UrlManager;
use yii\base\Event;
use yii\mail\MailEvent;

trait PluginEventsTrait
{
    public function registerEvents(): void
    {
        Event::on(
            MailerHelper::class,
            MailerHelper::EVENT_REGISTER_MAILER_TRANSPORT_TYPES,
            static function (RegisterComponentTypesEvent $event) {
                $event->types[] = MailerChainAdapter::class;
            }
        );

        Event::on(
            Elements::class,
            Elements::EVENT_REGISTER_ELEMENT_TYPES,
            static function (RegisterComponentTypesEvent $event) {
                $event->types[] = ChainAdapter::class;
            }
        );

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            static function (RegisterUrlRulesEvent $event) {
                $event->rules['mailerchain'] = 'mailerchain/adapter/index';
                $event->rules['mailerchain/new'] = 'mailerchain/adapter/new';
                $event->rules['mailerchain/edit/<elementId:\d+>'] = 'mailerchain/adapter/edit';
            }
        );

        Event::on(
            Mailer::class,
            Mailer::EVENT_AFTER_SEND,
            static function (MailEvent $event) {
                if ($event->isSuccessful) {
                    $transporter = $event->message->mailer->getTransport();
                    Plugin::getInstance()->getChainAdapter()->increaseSentByTransport($transporter::class);
                }
            }
        );
    }
}