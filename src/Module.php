<?php

namespace MarketFlow\Yii2\TOTP;

use yii\base\Event;
use yii\web\Application;

/**
 * Class Module
 * @package MarketFlow\Yii2\TOTP
 */
class Module extends \yii\base\Module
{
    public $controllerNamespace = 'MarketFlow\Yii2\TOTP\controllers';

    public $sesionPrefix = 'totp';

    public $totpView = '@vendor/marketflow/yii2-totp/views/totp';

    public function init()
    {
        parent::init();

        Event::on(Application::class, Application::EVENT_BEFORE_ACTION, function(Event $event) {
            vdd($event);

        });
    }

}