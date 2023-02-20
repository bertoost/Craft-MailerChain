<?php

namespace bertoost\mailerchain\adapters;

use bertoost\mailerchain\elements\ChainAdapter;
use Craft;
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
        try {
            /** @var null|ChainAdapter $chainAdapter */
            $chainAdapter = ChainAdapter::find()->testSuccess()->random()->one();

            if (null === $chainAdapter) {
                throw new \RuntimeException('There is no configured chain adapter found.');
            }

            $adapter = $chainAdapter->getTransportAdapter();
        }  catch (\Exception $e) {
            // Fallback to the PHP mailer
            $adapter = new Sendmail();
        }

        return $adapter->defineTransport();
    }

    public static function isUsed(): bool
    {
        $settings = App::mailSettings();

        return $settings->transportType === self::class;
    }
}