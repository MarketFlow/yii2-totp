<?php

namespace MarketFlow\Yii2\TOTP\widgets;

use GAuth\Auth;
use yii\helpers\Html;
use yii\widgets\InputWidget;

class TOTPSecretInputWidget extends InputWidget
{
    static $counter = 0;

    public $holder;
    public $name;

    public function init()
    {
        parent::init();
        static::$counter++;
        if (static::$counter > 1) {
            throw new \Exception('You should only use 1 ' . static::class);
        }
    }

    public function run()
    {
        $result = parent::run();
        $gAuth = new Auth();
        $this->model->{$this->attribute} = $this->model->{$this->attribute} ?? $gAuth->generateCode();
        $this->value = $this->model->{$this->attribute};
        $gAuth->setInitKey($this->value);
        $qr = $gAuth->generateQrImage(
            ' ' . $this->name,
            $this->holder,
            200
        );

        $result.= Html::tag('div', Html::img('data:image/png;base64,'.base64_encode($qr)), ['class' => 'text-center']);
        $result .= Html::activeHiddenInput($this->model, $this->attribute);
        $result .= Html::textInput('totpCode', null, ['class' => 'form-control', 'style' => ['margin-top' => '10px']]);

        return $result;
    }

}