<?php

namespace MarketFlow\Yii2\TOTP;

use Exception;
use MarketFlow\Yii2\TOTP\actions\TOTPAction;
use MarketFlow\Yii2\TOTP\interfaces\TOTPInterface;
use yii\base\Application;
use yii\base\ActionEvent;
use yii\base\Event;
use yii\base\InlineAction;
use yii\base\InvalidConfigException;
use yii\base\Security;
use yii\web\Cookie;
use yii\web\Request;
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

    const TOTP_DONE_KEY = 'totpDone';
    const TOTP_DEVICE_TOKEN = 'totpDeviceToken';

    public $sessionPrefix = 'totp';
    public $cookiePrefix = 'totp';

    public $allowRememberDevice = true;

    public $totpAction;

    public function __construct(string $id, $parent = null, array $config = [])
    {
        parent::__construct($id, $parent, $config);

        $this->totpAction = $this->totpAction ?? ($this->getUniqueId() . '/login/totp');
        if ($this->allowRememberDevice && !$this->has('security')) {
            throw new InvalidConfigException('Module requires security component');
        }
    }

    public function createControllerByID($id)
    {
        if ($this->totpAction === ($this->getUniqueId() . '/login/totp')) {
            return parent::createControllerByID($id);
        }
    }

    public function getApplication(\yii\base\Module $module = null): Application {
        $module = $module ?? $this;

        return $module->module instanceof Application
            ? $module->module
            : $this->getApplication($module->module);
    }

    /**
     * @return null|Request
     * @throws InvalidConfigException
     */
    public function getRequest()
    {
        return $this->get('request');
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->get('response');
    }

    /**
     * @return Session
     */
    public function getSession()
    {
        return $this->get('session');
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->get('user');
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
                || $this->getSession()->get($this->sessionPrefix . self::TOTP_DONE_KEY, false)
                || ltrim($this->totpAction, '/') == $event->action->getUniqueId()
                || ltrim($this->get('errorHandler')->errorAction) == $event->action->getUniqueId()
            ) {
                return;
            }

            $cookieName = $this->cookiePrefix . self::TOTP_DEVICE_TOKEN;

            if ($this->getRequest()->cookies->has($cookieName) && $this->allowRememberDevice) {
                $cookie = $this->getRequest()->cookies->get($cookieName);
                $expiration = \DateTime::createFromFormat(\DateTime::ATOM, $cookie->value['expiration']);
                if ($this->getUser()->identity->validateAuthKey($cookie->value['authKey']) && $expiration > (new \DateTime())) {
                    $this->setTotpChecked();
                    return;
                }
            }

            return $this->getResponse()->redirect($this->totpAction);
        });

        Event::on(static::class, self::EVENT_TOTP_CONFIGURED, function(Event $event) {
            $this->setTotpChecked();
        });
    }

    public function setTotpChecked()
    {
        $this->getSession()->set($this->sessionPrefix . self::TOTP_DONE_KEY, true);
    }
}