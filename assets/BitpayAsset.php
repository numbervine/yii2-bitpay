<?php
/**
 * @license Copyright 2017 Thomas Varghese, MIT License
 * see https://github.com/numbervine/yii2-bitpay/blob/master/LICENSE
 */
namespace numbervine\bitpay\assets;

use yii\web\AssetBundle;

class BitpayAsset extends AssetBundle
{
  public $sourcePath = '@vendor/numbervine/yii2-bitpay/assets/';

  public $js = YII_ENV_PROD ? [
    'js/bitpay_btn_render.js',
    'https://bitpay.com/bitpay.js'
  ] : [
    'js/bitpay_btn_render_dev.js',
    'js/bitpay_btn_render.js',
    'https://bitpay.com/bitpay.js'
  ] ;

  public $depends = [
    'yii\web\YiiAsset',
    'numbervine\sweetalert2\SweetAlert2Asset'
  ];
}
