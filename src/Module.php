<?php

namespace MarketFlow\Yii2\TOTP;

use Exception;
use MarketFlow\Yii2\TOTP\interfaces\TOTPInterface;
use yii\base\Application;
use yii\base\ActionEvent;
use yii\base\Event;
use yii\web\Response;
use yii\web\Session;
use yii\web\User;

/**
 * Class Module
 * @package MarketFlow\Yii2\TOTP
 */
class Module extends \yii\base\Module
{
    const EVENT_TOTP_CONFIGURED = 'eventTotpConfigured';

    public $codeParam = 'totpCode';

    public $sessionPrefix = 'totp';
    public $sessionTOTPDonekey = 'totpDone';

    public $totpView;
    public $totpLayout;

    public function getApplication(\yii\base\Module $module = null): Application {
        $module = $module ?? $this;

        return $module->module instanceof Application
            ? $module->module
            : $this->getApplication($module->module);
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->module->getResponse();
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        return $this->module->session;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->module->user;
    }

    public function init()
    {
        parent::init();

        $this->getApplication()->on(Application::EVENT_BEFORE_ACTION, function(ActionEvent $event) {
            $user = $this->getUser();

            /** @var TOTPInterface $identity */
            $identity = $user->identity;
            if (!is_null($identity) && !$identity instanceof TOTPInterface) {
                throw new Exception('Identity must implement ' . TOTPInterface::class);
            }

            if (
                $user->isGuest
                || is_null($identity->getTOTPSecret())
                || $this->getSession()->get($this->sessionPrefix . $this->sessionTOTPDonekey, false)
                || (
                    $event->action->id == 'totp'
                    && $event->action->controller->id == 'login'
                    && $event->action->controller->module->id == $this->id
                )
            ) {
                return;
            }

            return $this->getResponse()->redirect([$this->id . '/login/totp']);
        });

        Event::on(static::class, self::EVENT_TOTP_CONFIGURED, function(Event $event) {
            $this->setTotpChecked();
        });
    }

    public function setTotpChecked()
    {
        $this->getSession()->set($this->sessionPrefix . $this->sessionTOTPDonekey, true);
    }
}