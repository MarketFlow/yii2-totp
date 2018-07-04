<?php

namespace MarketFlow\Yii2\TOTP\interfaces;

use yii\web\IdentityInterface;

/**
 * Interface TOTPInterface
 * @package MarketFlow\Yii2\TOTP\interfaces
 */
interface TOTPInterface extends IdentityInterface
{
    /**
     * Returns the TOTP secret
     * @return string
     */
    public function getTOTPSecret();
}