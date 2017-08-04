<?php

namespace MarketFlow\Yii2\TOTP\validators;

use GAuth\Auth;
use yii\validators\Validator;

/**
 * Class TOTPValidator
 * @package MarketFlow\Yii2\TOTP\validators
 */
class TOTPSecretValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $gAuth = new Auth();
        $request = app()->request;

        $gAuth->setInitKey($model->{$attribute});
        if (!$gAuth->validateCode($request->getBodyParam('totpCode'))) {
            $model->addError($attribute, \Yii::t('yii2-totp', 'Invalid code'));
        }
    }
}