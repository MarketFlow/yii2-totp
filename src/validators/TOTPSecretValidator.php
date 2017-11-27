<?php

namespace MarketFlow\Yii2\TOTP\validators;

use GAuth\Auth;
use yii\validators\Validator;

/**
 * Class TOTPSecretValidator
 * @package MarketFlow\Yii2\TOTP\validators
 */
class TOTPSecretValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $gAuth = new Auth();

        try {
            $gAuth->base32_decode($model->{$attribute});
        } catch (\InvalidArgumentException $e) {
            $model->addError($attribute, \Yii::t('yii2-totp', 'Invalid secret'));
        }
    }
}