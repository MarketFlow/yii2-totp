<?php

namespace MarketFlow\Yii2\TOTP\models;

use GAuth\Auth;
use MarketFlow\Yii2\TOTP\validators\TOTPCodeValidator;
use MarketFlow\Yii2\TOTP\validators\TOTPSecretValidator;
use yii\base\Model;
use yii\validators\RequiredValidator;
use yii\validators\StringValidator;

/**
 * Class TOTPModel
 * @package MarketFlow\Yii2\TOTP\models
 */
class TOTPModel extends Model
{
    public $secret;
    public $code;

    public function init()
    {
        parent::init();
        $gAuth = new Auth();
        $this->secret = $this->secret ?? $gAuth->generateCode();
    }

    public function rules()
    {
        return [
            [['code', 'secret'], RequiredValidator::class],
            [['code', 'secret'], StringValidator::class],
            [['code'], TOTPCodeValidator::class, 'secretAttribute' => 'secret'],
            [['secret'], TOTPSecretValidator::class]
        ];
    }
}