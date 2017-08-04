<?php

namespace MarketFlow\Yii2\TOTP;

use Exception;
use MarketFlow\Yii2\TOTP\interfaces\TOTPInterface;
use yii\base\ActionEvent;
use yii\base\Event;
use yii\web\Application;
use yii\web\Response;
use yii\web\Session;
use yii\web\User;

/**
 * Class Module
 * @package MarketFlow\Yii2\TOTP
 */
class Module extends \yii\base\Module
{
    public $codeParam = 'totpCode';

    public $sessionPrefix = 'totp';
    public $sessionTOTPDonekey = 'totpDone';

    public $totpView;

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

        Event::on(Application::class, Application::EVENT_BEFORE_ACTION, function(ActionEvent $event) {
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
    }

    public function setTotpChecked()
    {
        $this->getSession()->set($this->sessionPrefix . $this->sessionTOTPDonekey, true);
    }
}