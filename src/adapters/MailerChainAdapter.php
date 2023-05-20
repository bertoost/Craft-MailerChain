<?php

namespace bertoost\mailerchain\adapters;

use bertoost\mailerchain\elements\ChainAdapter;
use bertoost\mailerchain\transports\MailerChainTransport;
use Craft;
use craft\mail\transportadapters\BaseTransportAdapter;
use Symfony\Component\Mailer\Transport\AbstractTransport;

class MailerChainAdapter extends BaseTransportAdapter
{
    public static function displayName(): string
    {
        return 'Mailer Chain';
    }

    public function getSettingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate(
            'mailerchain/adapter/settings', [
                'adapter' => $this,
                'valid' => ChainAdapter::find()->testSuccess()->exists(),
            ]
        );
    }

    public function defineTransport(): array|AbstractTransport
    {
        return new MailerChainTransport();
    }
}