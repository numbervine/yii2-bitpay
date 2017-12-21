<?php
/**
 * @license Copyright 2017 Thomas Varghese, MIT License
 * see https://github.com/numbervine/yii2-bitpay/blob/master/LICENSE
 */
namespace numbervine\bitpay\components;

use yii\base\Component;
use Bitpay\Invoice;

class AppBridge extends Component implements AppInterface
{
  public $module = null;

	public function init()
	{
			parent::init();
			$this->module = \Yii::$app->getModule('bitpay');
	}

  public function processBitpayInvoice(payload=[])
  {
    
  }
}
