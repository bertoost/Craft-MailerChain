<?php

namespace bertoost\mailerchain\traits;

use bertoost\mailerchain\services\ChainAdapterService;

trait PluginComponentsTrait
{
    public function registerComponents(): void
    {
        $this->setComponents([
            'chainAdapter' => ChainAdapterService::class,
        ]);
    }

    public function getChainAdapter(): ChainAdapterService
    {
        return $this->get('chainAdapter');
    }
}