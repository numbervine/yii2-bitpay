<?php
/**
 * @license Copyright 2017 Thomas Varghese, MIT License
 * see https://github.com/numbervine/yii2-bitpay/blob/master/LICENSE
 */
namespace numbervine\bitpay\controllers;

use yii\console\Controller;

/**
 * Console command controller to setup `bitpay` module keys
 */
class SetupController extends Controller
{

  public $module = null;

	public function init()
	{
			parent::init();
			$this->module = \Yii::$app->getModule('bitpay');
	}

  public function actionIndex()
  {
    if (!$this->module->bitpay_bridge->keyPairExists()) {
      $this->module->bitpay_bridge->createKeyPair();
    }
    $token = $this->module->bitpay_bridge->currentToken();
    return ($token ? true : false);
  }
}
