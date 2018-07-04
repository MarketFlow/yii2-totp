<?php

namespace MarketFlow\Yii2\TOTP\models;

use GAuth\Auth;
use MarketFlow\Yii2\TOTP\interfaces\TOTPInterface;
use yii\base\Model;
use yii\validators\BooleanValidator;
use yii\validators\RegularExpressionValidator;
use yii\validators\RequiredValidator;
use yii\validators\StringValidator;

class SecondFactorForm extends Model
{
    const SCENARIO_NO_REMEMBER = 'noRemember';

    /**
     * @var string
     */
    public $code;

    /**
     * @var TOTPInterface
     */
    private $identity;

    /**
     * @var bool
     */
    public $rememberDevice = false;

    /**
     * SecondFactorForm constructor.
     * @param TOTPInterface $identity
     * @param array $config
     */
    public function __construct(TOTPInterface $identity, bool $allowRemember = false, array $config = [])
    {
        $this->identity = $identity;
        $this->scenario = $allowRemember ? self::SCENARIO_DEFAULT : self::SCENARIO_NO_REMEMBER;
        parent::__construct($config);
    }

    public function attributeLabels()
    {
        return [
            'rememberDevice' => \Yii::t('yii2-totp', 'Remember device')
        ];
    }

    private function getGAuth(): Auth
    {
        $gAuth = new Auth($this->identity->getTOTPSecret());
        $gAuth->setCodeLength(6);
        return $gAuth;
    }

    public function rules()
    {
        $codeLength = $this->getGAuth()->getCodeLength();
        return [
            [['code'], RequiredValidator::class],
            [['code'], RegularExpressionValidator::class, 'pattern' => '/^\d{' . $codeLength . '}$/', 'message' => \Yii::t('yii2-totp', 'TOTP code must contain exactly {n} digits', ['n' => $codeLength])],
            [['code'], function($attribute, $params, $validator) {
                if (!$this->getGAuth()->validateCode($this->{$attribute})) {
                    $this->addError($attribute, \Yii::t('yii2-totp', 'Invalid TOTP code'));
                }
            }],
            [['rememberDevice'], BooleanValidator::class]
        ];
    }
    
    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            self::SCENARIO_NO_REMEMBER => ['code']
        ]);
    }

}