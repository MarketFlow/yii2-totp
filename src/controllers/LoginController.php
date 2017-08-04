<?php

namespace MarketFlow\Yii2\TOTP\controllers;

use GAuth\Auth;
use MarketFlow\Yii2\TOTP\interfaces\TOTPInterface;
use MarketFlow\Yii2\TOTP\Module;
use yii\validators\RequiredValidator;
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

        $model = new \yii\base\DynamicModel([
            $module->codeParam => null
        ]);
        $model->addRule([$module->codeParam], RequiredValidator::class);
        $model->addRule([$module->codeParam], function($attribute, $params, $validator) use ($model, $gAuth) {
            if (!$gAuth->validateCode($model->{$attribute})) {
                $model->addError($attribute, \Yii::t('yii2-totp', 'Invalid code'));
            }
        });

        if ($request->isPost && $model->load($request->getBodyParams()) && $model->validate()) {
            $module->setTotpChecked();
            return $this->redirect(\Yii::$app->user->returnUrl);
        }

        return $this->render($module->totpView ?? 'totp', [
            'gAuth' => $gAuth,
            'action' => [$this->module->id . '/' . $this->id, '/totp'],
            'codeParam' => $module->codeParam,
            'model' => $model
        ]);
    }
}