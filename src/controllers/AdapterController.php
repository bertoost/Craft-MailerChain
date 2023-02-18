<?php

namespace bertoost\mailerchain\controllers;

use bertoost\mailerchain\elements\ChainAdapter;
use bertoost\mailerchain\Plugin;
use Craft;
use craft\helpers\App;
use craft\helpers\Json;
use craft\web\Controller;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class AdapterController extends Controller
{
    public function beforeAction($action): bool
    {
        $this->requireAdmin();

        return parent::beforeAction($action);
    }

    public function actionIndex(): Response
    {
        return $this->renderTemplate('mailerchain/index.twig', [
            'chainAdapters' => ChainAdapter::findAll(),
        ]);
    }

    public function actionNew(): Response
    {
        $transportTypeOptions = Plugin::getInstance()->getChainAdapter()
            ->getTransportTypeOptions();

        return $this->renderTemplate('mailerchain/form.twig', [
            'transportTypeOptions' => $transportTypeOptions,
            'mailSettings' => App::mailSettings(),
        ]);
    }

    public function actionEdit(int $elementId): Response
    {
        /** @var ChainAdapter $chainAdapter */
        $chainAdapter = ChainAdapter::findOne(['id' => $elementId]);

        $transportTypeOptions = Plugin::getInstance()->getChainAdapter()
            ->getTransportTypeOptions($chainAdapter);

        return $this->renderTemplate('mailerchain/form.twig', [
            'chainAdapter' => $chainAdapter,
            'adapter' => $chainAdapter->getTransportAdapter(),
            'transportTypeOptions' => $transportTypeOptions,
            'mailSettings' => App::mailSettings(),
        ]);
    }

    public function actionSave(): ?Response
    {
        $elementId = $this->request->getBodyParam('id');

        if ($elementId) {
            $chainAdapter = ChainAdapter::findOne(['id' => $elementId]);
            if (!$chainAdapter) {
                throw new BadRequestHttpException('Invalid chain adapter ID: ' . $elementId);
            }
        } else {
            $chainAdapter = new ChainAdapter();
        }

        // Populate the event with the form data
        $chainAdapter->title = $this->request->getBodyParam('title');
        $chainAdapter->transportType = $this->request->getRequiredBodyParam('transportType');
        $chainAdapter->transportSettings = $this->request->getBodyParam('transportTypes.' . $chainAdapter->transportType);

        if (null !== ($adapter = $chainAdapter->getTransportAdapter())) {
            $chainAdapter->transportClass = $adapter->defineTransport()::class;
        }

        // Try to save it
        if (!Craft::$app->getElements()->saveElement($chainAdapter)) {
            if ($this->request->getAcceptsJson()) {
                return $this->asJson(['errors' => $chainAdapter->getErrors()]);
            }

            $this->setFailFlash(Craft::t('mailerchain', 'Couldn\'t save chain adapter.'));

            // Send the event back to the edit action
            Craft::$app->urlManager->setRouteParams([
                'chainAdapter' => $chainAdapter,
            ]);

            return null;
        }

        if ($this->request->getAcceptsJson()) {
            return $this->asJson(['success' => true]);
        }

        $this->setSuccessFlash(Craft::t('mailerchain', 'Chain adapter saved.'));

        return $this->redirect('mailerchain/edit/' . $chainAdapter->id);
    }

    public function actionReorder(): Response
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $ids = Json::decode($this->request->getRequiredBodyParam('ids'));
        Plugin::getInstance()->getChainAdapter()->reorder($ids);

        return $this->asSuccess();
    }

    public function actionDelete(): Response
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $id = Json::decode($this->request->getRequiredBodyParam('id'));
        Plugin::getInstance()->getChainAdapter()->delete($id);

        return $this->asSuccess();
    }
}