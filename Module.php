<?php
namespace numbervine\bitpay;

use Bitpay\Network\Testnet;
use Bitpay\Network\Livenet;

use yii\base\InvalidConfigException;

/**
 * bitpay module definition class
 */
class Module extends \yii\base\Module
{
    const ENCRYPTION_NONCE = 'ecd50c68b93c76929377f0c8e8d754cd3b4547cfcc4b28a6635b9f9af171b372';

    const PRIVATE_KEY = 'bitpay.pri';
    const PUBLIC_KEY = 'bitpay.pub';
    const ACCESS_TOKEN = 'access.tok';

    public $controllerNamespace = 'numbervine\bitpay\controllers';

    public $token_label = null;
    public $pairing_code = null;
    public $encryption_nonce = null;
    public $network = null;
    public $storage_path = null;
    public $bitpay_config = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
      parent::init();

      $bitpay_config = YII_ENV_PROD ? ( isset($this->bitpay_config['livenet']) ? $this->bitpay_config['livenet'] : []) : (isset($this->bitpay_config['testnet']) ? $this->bitpay_config['testnet'] : []);
      if ($bitpay_config) {
        if (!isset($bitpay_config['token_label'])) {
          throw new InvalidConfigException("Bitpay configuration error: 'token_label' needs to be set");
        } else {
          $this->token_label = $bitpay_config['token_label'];
        }
        if (!isset($bitpay_config['pairing_code'])) {
          throw new InvalidConfigException("Bitpay configuration error: 'pairing_code' needs to be set");
        } else {
          $this->pairing_code = $bitpay_config['pairing_code'];
        }

        $this->encryption_nonce = isset($bitpay_config['encryption_nonce']) ? $bitpay_config['encryption_nonce'] : self::ENCRYPTION_NONCE;
      } else {
        throw new InvalidConfigException("Bitpay configuration error: empty");
      }

      if (!isset($this->storage_path)) {
        $this->storage_path = \Yii::getAlias('@runtime'.DIRECTORY_SEPARATOR.'bitpay'.DIRECTORY_SEPARATOR.(YII_ENV_PROD ? 'livenet' : 'testnet').DIRECTORY_SEPARATOR.'keys'.DIRECTORY_SEPARATOR);
        if (!empty(\Yii::getAlias('@console'))) {
          $this->storage_path = \Yii::getAlias('@common'.DIRECTORY_SEPARATOR.'runtime'.DIRECTORY_SEPARATOR.'bitpay'.DIRECTORY_SEPARATOR.(YII_ENV_PROD ? 'livenet' : 'testnet').DIRECTORY_SEPARATOR.'keys'.DIRECTORY_SEPARATOR);
        }
      }

		  $this->network = YII_ENV_PROD ? new Livenet() : new Testnet();
    }
}
