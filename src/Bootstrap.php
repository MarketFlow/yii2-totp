<?php

namespace MarketFlow\Yii2\TOTP;

use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\helpers\ArrayHelper;

/**
 * Class Bootstrap
 * @package MarketFlow\Yii2\TOTP
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * @param Application $app
     */
    public function bootstrap($app)
    {
        foreach ($app->getModules() as $key => $config) {
            $class = is_string($config) ? $config : ArrayHelper::getValue($config, 'class');
            if (
                $class == Module::class || is_subclass_of($class, Module::class)
            ) {
                $app->bootstrap[] = $key;
            }
        }
    }
}