<?php

namespace bertoost\mailerchain;

use bertoost\mailerchain\traits\PluginComponentsTrait;
use bertoost\mailerchain\traits\PluginEventsTrait;
use Craft;
use craft\base\Plugin as BasePlugin;
use craft\helpers\UrlHelper;
use craft\i18n\PhpMessageSource;
use yii\web\Response;

class Plugin extends BasePlugin
{
    use PluginEventsTrait,
        PluginComponentsTrait;

    public function init(): void
    {
        Craft::setAlias('@bertoost\mailerchain', $this->getBasePath());

        $this->controllerNamespace = 'bertoost\\mailerchain\\controllers';
        if (Craft::$app->getRequest()->getIsConsoleRequest()) {
            $this->controllerNamespace = 'bertoost\\mailerchain\\console\\controllers';
        }

        parent::init();

        $this->registerTranslations();
        $this->registerComponents();
        $this->registerEvents();
    }

    /**
     * Registers translation definition
     */
    private function registerTranslations()
    {
        Craft::$app->i18n->translations['mailerchain*'] = [
            'class'          => PhpMessageSource::class,
            'sourceLanguage' => 'en',
            'basePath'       => $this->getBasePath().'/translations',
            'allowOverrides' => true,
            'fileMap'        => [
                'mailjet'     => 'site',
                'mailjet-app' => 'app',
            ],
        ];
    }

    public function getSettingsResponse(): Response
    {
        return Craft::$app->controller->redirect(UrlHelper::cpUrl('mailerchain'));
    }
}