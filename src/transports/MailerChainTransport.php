<?php

namespace bertoost\mailerchain\transports;

use bertoost\mailerchain\elements\ChainAdapter;
use craft\errors\MissingComponentException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mailer\Transport\TransportInterface;

class MailerChainTransport extends AbstractTransport
{
    private TransportInterface $usedTransport;

    public function __toString(): string
    {
        return 'mailerchain://default';
    }

    protected function doSend(SentMessage $message): void
    {
        /** @var null|ChainAdapter $chainAdapter */
        $chainAdapter = ChainAdapter::find()->testSuccess()->orderBySent()->one();

        if (null === $chainAdapter) {
            throw new MissingComponentException('There is no configured chain adapter found.');
        }

        $adapter = $chainAdapter->getTransportAdapter();

        $this->usedTransport = $adapter->defineTransport();
        $this->usedTransport->doSend($message);
    }

    public function getUsedTransport(): TransportInterface
    {
        return $this->usedTransport;
    }
}