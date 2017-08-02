<?php

namespace MarketFlow\Yii2\TOTP\controllers;

use GAuth\Auth;
use MarketFlow\Yii2\TOTP\interfaces\TOTPInterface;
use MarketFlow\Yii2\TOTP\Module;
use yii\web\Application;
use yii\web\Controller;

/**
 * Class LoginController
 * @package MarketFlow\Yii2\TOTP\controllers
 */
class LoginController extends Controller
{
    public function actionTotp()
    {
        $request = \Yii::$app->request;
        /** @var TOTPInterface $identity */
        $identity = \Yii::$app->user->identity;
        /** @var Module $module */
        $module = $this->module;

        if (is_null($identity)) {
            return $this->goHome();
        }

        $gAuth = new Auth();
        $gAuth->setInitKey($identity->getTOTPSecret());

        if ($request->isPost && $request->getBodyParam($module->codeParam)) {
            return $this->redirect(\Yii::$app->user->returnUrl);
        }

        return $this->render($module->totpView ?? 'totp', [
            'gAuth' => $gAuth,
            'action' => [$this->module->id . '/' . $this->id, '/totp'],
            'codeParam' => $module->codeParam
        ]);
    }
}