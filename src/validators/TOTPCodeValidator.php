<?php

namespace MarketFlow\Yii2\TOTP\validators;

use GAuth\Auth;
use yii\validators\Validator;

/**
 * Class TOTPCodeValidator
 * @package MarketFlow\Yii2\TOTP\validators
 */
class TOTPCodeValidator extends Validator
{
    public $secretAttribute;

    public function validateAttribute($model, $attribute)
    {
        $secretAttribute = $this->secretAttribute ?? $attribute . '_secret';

        $gAuth = new Auth();

        $gAuth->setInitKey($model->{$secretAttribute});

        try {
            $error = !$gAuth->validateCode($model->{$attribute});
        } catch (\InvalidArgumentException $e) {
            $error = true;
        }

        if ($error) {
            $model->addError($attribute, \Yii::t('yii2-totp', 'Invalid code'));
        }
    }
}