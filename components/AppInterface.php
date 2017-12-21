<?php
/**
 * @license Copyright 2017 Thomas Varghese, MIT License
 * see https://github.com/numbervine/yii2-bitpay/blob/master/LICENSE
 */
namespace numbervine\bitpay\components;

/**
 * Interface for connecting bitpay to application
 *
 * @package yii2-bitpay
 */
interface AppInterface
{
    public function processBitpayInvoice(payload=[]);
}
