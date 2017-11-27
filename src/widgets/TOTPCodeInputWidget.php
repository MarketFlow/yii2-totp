<?php

namespace MarketFlow\Yii2\TOTP\widgets;

use GAuth\Auth;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\widgets\InputWidget;

/**
 * Class TOTPCodeInputWidget
 * @package MarketFlow\Yii2\TOTP\widgets
 */
class TOTPCodeInputWidget extends InputWidget
{
    public $holder;
    public $displayName;

    public $secretAttribute;
    public $secretName;
    public $secretValue;

    public function init()
    {
        parent::init();
        if (
            !($this->hasModel() && isset($this->secretAttribute))
            && !isset($this->secretName, $this->secretValue)
        ) {
            throw new InvalidConfigException("Either 'secretName' and 'secretValue', or 'model' and 'secretAttribute' properties must be specified.");
        }
    }

    public function run()
    {
        $result = '';
        $gAuth = new Auth($this->hasModel() ? $this->model->{$this->secretAttribute} : $this->secretValue);

        $qr = $gAuth->generateQrImage(
            ' ' . $this->displayName,
            $this->holder,
            200
        );
        $result.= Html::tag('div', Html::img('data:image/png;base64,'.base64_encode($qr)), ['class' => 'text-left']);
        $secretInputOptions = [
            'class' => 'form-control disabled',
            'readonly' => true,
            'style' => [
                'margin-top' => '10px'
            ],
        ];
        $codeInputOptions = [
            'class' => 'form-control',
            'style' => [
                'margin-top' => '10px'
            ],
            'enableAjaxValidation' => true
        ];
        if ($this->hasModel()) {
            $result .= Html::activeTextInput($this->model, $this->secretAttribute, $secretInputOptions);
            $result .= Html::activeTextInput($this->model, $this->attribute, $codeInputOptions);
        } else {
            $result .= Html::textInput($this->secretName, $this->secretValue, $secretInputOptions);
            $result .= Html::textInput($this->name, $this->value, $codeInputOptions);
        }

        return $result;
    }

}