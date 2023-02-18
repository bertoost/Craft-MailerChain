<?php

namespace bertoost\mailerchain\elements;

use bertoost\mailerchain\elements\db\ChainAdapterQuery;
use bertoost\mailerchain\Plugin;
use Craft;
use craft\base\Element;
use craft\helpers\MailerHelper;
use craft\mail\transportadapters\TransportAdapterInterface;
use Symfony\Component\Mailer\Transport\AbstractTransport;

class ChainAdapter extends Element
{
    public string $transportType;

    public ?array $transportSettings = null;

    public ?string $transportClass = null;

    public int $sent = 0;

    public int $ranking = 0;

    private ?TransportAdapterInterface $transportAdapter = null;

    public function getTransportAdapter(): TransportAdapterInterface
    {
        if (null === $this->transportAdapter) {
            $this->transportAdapter = MailerHelper::createTransportAdapter(
                $this->transportType,
                $this->transportSettings
            );
        }

        return $this->transportAdapter;
    }

    public static function displayName(): string
    {
        return Craft::t('mailerchain', 'Chain Adapter');
    }

    public static function lowerDisplayName(): string
    {
        return Craft::t('mailerchain', 'chain adapter');
    }

    public static function pluralDisplayName(): string
    {
        return Craft::t('mailerchain', 'Chain Adapters');
    }

    public static function pluralLowerDisplayName(): string
    {
        return Craft::t('mailerchain', 'chain adapters');
    }

    public static function hasTitles(): bool
    {
        return true;
    }

    public static function hasContent(): bool
    {
        return true;
    }

    public static function find(): ChainAdapterQuery
    {
        return new ChainAdapterQuery(static::class);
    }

    public function beforeSave(bool $isNew): bool
    {
        if (empty($this->title)) {
            $this->title = $this->getTransportAdapter()::displayName();
        }

        // figure out transport class
        if (null === $this->transportClass
            && null !== ($adapter = $this->getTransportAdapter())
            && null !== ($transport = $adapter->defineTransport())
        ) {
            if (!$transport instanceof AbstractTransport && is_array($transport)) {
                $transport = Plugin::getInstance()->getChainAdapter()->determineMailerTransportByConfig($transport);
            }

            $this->transportClass = $transport::class;
        }

        return parent::beforeSave($isNew); // TODO: Change the autogenerated stub
    }

    public function afterSave(bool $isNew): void
    {
        $data = [
            'transportType' => $this->transportType,
            'transportSettings' => $this->transportSettings,
            'transportClass' => $this->transportClass,
            'ranking' => $this->ranking,
            'sent' => $this->sent,
        ];

        if ($isNew) {
            Craft::$app->db->createCommand()
                ->insert('{{%mailerchain}}', array_merge($data, ['id' => $this->id]))
                ->execute();
        } else {
            Craft::$app->db->createCommand()
                ->update('{{%mailerchain}}', $data, ['id' => $this->id])
                ->execute();
        }

        parent::afterSave($isNew);
    }
}