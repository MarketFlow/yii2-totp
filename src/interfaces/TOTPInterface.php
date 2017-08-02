<?php

namespace MarketFlow\Yii2\TOTP\interfaces;

/**
 * Interface TOTPInterface
 * @package MarketFlow\Yii2\TOTP\interfaces
 */
interface TOTPInterface
{
    /**
     * Returns the TOTP secret
     * @return string
     */
    public function getSecret(): string;
}