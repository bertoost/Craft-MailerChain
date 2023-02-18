<?php

namespace bertoost\mailerchain\services;

use bertoost\mailerchain\adapters\MailerChainAdapter;
use bertoost\mailerchain\elements\ChainAdapter;
use Craft;
use craft\base\Component;
use craft\helpers\MailerHelper;
use craft\mail\transportadapters\TransportAdapterInterface;
use Symfony\Component\Mailer\Transport\TransportInterface;
use yii\symfonymailer\Mailer;

class ChainAdapterService extends Component
{
    public function getTransportTypeOptions(?ChainAdapter $chainAdapter = null): array
    {
        // Get all the registered transport adapter types
        // And remove our own from the list.
        $allTransportAdapterTypes = MailerHelper::allMailerTransportTypes();
        $allTransportAdapterTypes = array_diff($allTransportAdapterTypes, [MailerChainAdapter::class]);

        // Make sure the selected adapter class is in there
        if (null !== $chainAdapter
            && !in_array(get_class($chainAdapter->getTransportAdapter()), $allTransportAdapterTypes, true)
        ) {
            $allTransportAdapterTypes[] = get_class($chainAdapter->getTransportAdapter());
        }

        $transportTypeOptions = [];

        foreach ($allTransportAdapterTypes as $transportAdapterType) {
            /** @var string|TransportAdapterInterface $transportAdapterType */
            if (
                (null !== $chainAdapter && $transportAdapterType === get_class($chainAdapter->getTransportAdapter()))
                || $transportAdapterType::isSelectable()
            ) {
                $transportTypeOptions[] = [
                    'value' => $transportAdapterType,
                    'label' => $transportAdapterType::displayName(),
                    'adapter' => MailerHelper::createTransportAdapter($transportAdapterType),
                ];
            }
        }

        return $transportTypeOptions;
    }

    /**
     * When the defined transport is a configuration array, there probably is a native support transporter for it
     * try figuring out this, by calling the set/get transporter of the mailer
     */
    public function determineMailerTransportByConfig(array $transportConfig): TransportInterface
    {
        $mailer = new Mailer();
        $mailer->setTransport($transportConfig);

        return $mailer->getTransport();
    }

    public function increaseSentByTransport(string $transportClass): bool
    {
        $chainAdapter = ChainAdapter::find()->transportClass($transportClass)->one();
        if (null !== $chainAdapter) {
            ++$chainAdapter->sent;

            return Craft::$app->getElements()->saveElement($chainAdapter);
        }

        return false;
    }

    public function reorder(array $ids): bool
    {
        foreach ($ids as $i => $id) {
            $chainAdapter = ChainAdapter::findOne(['id' => $id]);

            if (null !== $chainAdapter) {
                $chainAdapter->ranking = ($i + 1);

                return Craft::$app->getElements()->saveElement($chainAdapter);
            }
        }

        return false;
    }

    public function delete(int $id): bool
    {
        $chainAdapter = ChainAdapter::findOne(['id' => $id]);

        if (null !== $chainAdapter) {
            return Craft::$app->getElements()->deleteElement($chainAdapter, true);
        }

        return false;
    }
}