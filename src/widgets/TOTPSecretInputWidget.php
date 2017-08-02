<?php

namespace MarketFlow\Yii2\TOTP\widgets;

use GAuth\Auth;
use yii\helpers\Html;
use yii\widgets\InputWidget;

class TOTPSecretInputWidget extends InputWidget
{
    public $holder;
    public $name;
    public $hint;

    public function init()
    {
        parent::init();
        $this->hint = $this->hint ?? \Yii::t('yii2-topt', 'Enter the verification code');
    }


    public function run()
    {
        $result = parent::run();
        $gAuth = new Auth();
        $qr = $gAuth->generateQrImage(
            ' ' . $this->name,
            $this->holder,
            200
        );

        $result.= Html::img('data:image/png;base64,'.base64_encode($qr));
        $result .= Html::textInput('code', null, ['class' => 'form-control', 'style' => ['margin-top' => '10px']]);
        $result .= $this->hint;


        return $result;
    }

}