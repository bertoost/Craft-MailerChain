<?php

namespace bertoost\mailerchain\transports;

use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mailer\Transport\TransportInterface;

class MailerChainTransportDummy extends AbstractTransport
{
    public function __toString(): string
    {
        return 'mailerchain://default';
    }

    protected function doSend(SentMessage $message): void
    {
        $test = true;
    }

    public static function isUsed(TransportInterface $transport): bool
    {
        return $transport instanceof self;
    }
}