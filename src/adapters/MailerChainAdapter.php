<?php

namespace bertoost\mailerchain\adapters;

use bertoost\mailerchain\elements\ChainAdapter;
use bertoost\mailerchain\transports\MailerChainTransportDummy;
use Craft;
use craft\errors\MissingComponentException;
use craft\helpers\App;
use craft\mail\transportadapters\BaseTransportAdapter;
use craft\mail\transportadapters\Sendmail;
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
        return new MailerChainTransportDummy();
    }

    public static function isUsed(): bool
    {
        $settings = App::mailSettings();

        return $settings->transportType === self::class;
    }
}