<?php

namespace MarketFlow\Yii2\TOTP\controllers;

use GAuth\Auth;
use MarketFlow\Yii2\TOTP\actions\TOTPAction;
use MarketFlow\Yii2\TOTP\interfaces\TOTPInterface;
use MarketFlow\Yii2\TOTP\models\SecondFactorForm;
use MarketFlow\Yii2\TOTP\Module;
use yii\validators\BooleanValidator;
use yii\validators\DefaultValueValidator;
use yii\validators\RequiredValidator;
use yii\web\Controller;

/**
 * Class LoginController
 * @package MarketFlow\Yii2\TOTP\controllers
 */
class LoginController extends Controller
{
    public function beforeAction($action)
    {
        return parent::beforeAction($action) && $this->module->totpAction === $action->getUniqueId();
    }

    public function actions()
    {
        return [
            'totp' => function($id, Controller $controller) {
                return new TOTPAction($id, $controller, [
                    'user' => $controller->module->get('user'),
                    'module' => $controller->module,
                    'request' => $controller->module->get('request')
                ]);
            }
        ];
    }
}