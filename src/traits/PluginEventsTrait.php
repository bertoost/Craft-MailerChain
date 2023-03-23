<?php

namespace bertoost\mailerchain\traits;

use bertoost\mailerchain\adapters\MailerChainAdapter;
use bertoost\mailerchain\elements\ChainAdapter;
use bertoost\mailerchain\Plugin;
use bertoost\mailerchain\transports\MailerChainTransportDummy;
use craft\errors\MissingComponentException;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\helpers\MailerHelper;
use craft\mail\Mailer;
use craft\services\Elements;
use craft\web\UrlManager;
use yii\base\Event;
use yii\mail\BaseMailer;
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
            Mailer::EVENT_BEFORE_PREP,
            static function (MailEvent $event) {
                /** @var Mailer $mailer */
                $mailer = $event->sender;

                if (!MailerChainAdapter::isUsed()
                    || !MailerChainTransportDummy::isUsed($mailer->getTransport())
                ) {
                    return;
                }

                try {
                    /** @var null|ChainAdapter $chainAdapter */
                    $chainAdapter = ChainAdapter::find()->testSuccess()->orderBySent()->one();

                    if (null === $chainAdapter) {
                        throw new MissingComponentException('There is no configured chain adapter found.');
                    }

                    $adapter = $chainAdapter->getTransportAdapter();
                }  catch (\Exception $e) {
                    throw new MissingComponentException();
                }

                $mailer->setTransport($adapter->defineTransport());
            }
        );

        Event::on(
            Mailer::class,
            BaseMailer::EVENT_AFTER_SEND,
            static function (MailEvent $event) {
                if (!MailerChainAdapter::isUsed()) {
                    return;
                }

                if ($event->message->key === 'test_email' || $event->isSuccessful) {
                    /** @var Mailer $mailer */
                    $mailer = $event->sender;

                    $service = Plugin::getInstance()->getChainAdapter();
                    $transport = $mailer->getTransport();
                    $chainAdapter = $service->getByTransportClass($transport::class);

                    if (null === $chainAdapter) {
                        return;
                    }

                    if ($event->isSuccessful) {
                        $service->increaseSent($chainAdapter);
                    }

                    if ($event->message->key === 'test_email') {
                        $service->registerTestStatus($chainAdapter, $event->isSuccessful);
                    }
                }
            }
        );
    }
}