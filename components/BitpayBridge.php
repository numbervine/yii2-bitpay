<?php
/**
 * @license Copyright 2017 Thomas Varghese, MIT License
 * see https://github.com/numbervine/yii2-bitpay/blob/master/LICENSE
 */
namespace numbervine\bitpay\components;

use Bitpay\Storage\EncryptedFilesystemStorage;

use Bitpay\PrivateKey;
use Bitpay\PublicKey;
use Bitpay\Client\Client;
use Bitpay\Client\Adapter\CurlAdapter;
use Bitpay\SinKey;
use Bitpay\Invoice;
use Bitpay\Item;
use Bitpay\Currency;
use Bitpay\Token;
use Bitpay\AccessTokenManager;
// use Bitpay\Storage\EncryptedMongodbStorage;
use Bitpay\BitpayBridge;
use Bitpay\Network\Livenet;
use Bitpay\Network\Testnet;

use yii\base\NotSupportedException;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\helpers\VarDumper;
use yii\base\Component;
use yii\helpers\FileHelper;

// use common\models\XupaHelpers;

use numbervine\bitpay\models\AccessTokenStore;

/**
 *
 */
class BitpayBridge extends Component
{
	// const BUFFER_TIME_SECONDS = 60*60*8;
	//
	// const PRIVATE_KEY_ID = 'bitpay_private_key';
	// const PUBLIC_KEY_ID = 'bitpay_public_key';

// 	const TOKEN_ID = 'vjsez_testnet_token';


// 	const PAIRING_CODE = 'EupkXnM';
// 	const ENCRYPTION_CODE = 'b8tp1y123';

	public $module = null;

	public function init()
	{
			parent::init();

			// ... initialization after configuration is applied

			$this->module = \Yii::$app->getModule('bitpay');
	}

	// public static function getTokenId() {
	// 	$result = 'marilcha_testnet_token';
	//
	// 	if (YII_ENV_PROD) {
	// 		$result = 'marilcha_livenet_token';
	// 	}
	//
	// 	return $result;
	// }

	// public static function getPairingCode() {
	// 	$result = '2tCJPEQ';  // enter testnet pairing code
	//
	// 	if (YII_ENV_PROD) {
	// 		$result = 'xy69ktd';   // enter livenet pairing code here
	// 	}
	//
	// 	return $result;
	// }

	// public static function getEncryptionCode() {
	// 	$result = '93cf2c3f67c80f2a';  // enter testnet encryption code
	//
	// 	if (YII_ENV_PROD) {
	// 		$result = '09ae358ad8c39ba5';   // enter livenet encryption code here
	// 	}
	//
	// 	return $result;
	// }

	// public static function notificationLogCollectionName()
	// {
	// 	$result = 'bitpay_notification_log';
	//
	// 	if (YII_ENV_PROD) {
	// 		$result = 'bitpay_notification_log_live';   // live server
	// 	}
	//
	// 	return $result;
	// }

// 	const ACCESS_CODE_LABEL = 'test_bitpay';

// 	public static function collectionName()
// 	{
// 		return ['vjsez','bitpay_access_token'];
// 	}

// 	public function attributes()
// 	{
// 		return ['_id', 'access_token', 'server_response',
// 				'created_at', 'updated_at', 'created_by', 'updated_by'];
// 	}

// 	public function rules()
// 	{
// 		return [
// 				[['access_token'],'required'],
// 				[['access_token', 'server_response'],'string'],
// 				[['created_at', 'updated_at', 'created_by','updated_by'],'integer'],
// 				[['access_token', 'server_response', 'created_at', 'updated_at', 'created_by', 'updated_by'],'safe']
// 		];
// 	}

// 	/**
// 	 * @inheritdoc
// 	 */
// 	public function attributeLabels()
// 	{
// 		return [
// 				'_id' => 'ID',
// 				'access_token' => 'Access Token',
// 				'server_response' => 'Server Response',
// 				'created_at' => 'Created At',
// 				'updated_at' => 'Updated At',
// 				'created_by' => 'Created By',
// 				'updated_by' => 'Updated By',
// 		];
// 	}

// 	public function behaviors()
// 	{
// 		return [
// 				TimestampBehavior::className(),
// 				ConsoleBlameableBehavior::className(),
// 		];
// 	}

// 	/**
// 	 * @inheritdoc
// 	 * @return BitpayBridgeQuery the active query used by this AR class.
// 	 */
// 	public static function find()
// 	{
// 		return new BitpayBridgeQuery(get_called_class());
// 	}

	public function isValid() {
		$result = true;

// 		$response = json_decode($this->server_response,true);
// 		if (time()>(intval($this->created_at)+intval($response['expires_in'])-self::BUFFER_TIME_SECONDS)) {
// 			$result = false;
// 		}

		return $result;
	}

	public function keyPairExists() {
		$result = true;

// 		$document_identifier_config = ['id'=> self::PRIVATE_KEY_ID];
// // 		$key_obj_private = XupaHelpers::findOneMongoDbDocument(EncryptedMongodbStorage::BITPAY_KEYS_COLLECTION, $document_identifier_config);
// 		$key_obj_private = XupaHelpers::findOneMongoDbDocument(EncryptedMongodbStorage::getBitpayKeysCollection(), $document_identifier_config);
// 		$document_identifier_config = ['id'=> self::PUBLIC_KEY_ID];
// // 		$key_obj_public = XupaHelpers::findOneMongoDbDocument(EncryptedMongodbStorage::BITPAY_KEYS_COLLECTION, $document_identifier_config);
// 		$key_obj_public = XupaHelpers::findOneMongoDbDocument(EncryptedMongodbStorage::getBitpayKeysCollection(), $document_identifier_config);
//
// 		$result = $key_obj_private && $key_obj_public;

		$result &= file_exists($this->module->storage_path.$this->module::PRIVATE_KEY);
		$result &= file_exists($this->module->storage_path.$this->module::PUBLIC_KEY);

		return $result;
	}

	// public function test1() {
	// 	// $module = \Yii::$app->controller->module;
	// 	echo 'here'.PHP_EOL;
	//
	//
	// 	FileHelper::createDirectory($module->storage_path);
	//
	// 	echo $module->storage_path.PHP_EOL;
	// 	$privateKey = new PrivateKey($module->storage_path.$module::PRIVATE_KEY);
	// 	// Generate a random number
	// 	$privateKey->generate();
	// 	// You can generate a private key with only one line of code like so
	// 	$privateKey = PrivateKey::create($module->storage_path.$module::PRIVATE_KEY)->generate();
	// 	// NOTE: This has overridden the previous $privateKey variable, although its
	// 	//       not an issue in this case since we have not used this key for
	// 	//       anything yet.
	// 	/**
	// 	 * Once we have a private key, a public key is created from it.
	// 	 */
	// 	$publicKey = new PublicKey($module->storage_path.$module::PUBLIC_KEY);
	// 	// Inject the private key into the public key
	// 	$publicKey->setPrivateKey($privateKey);
	// 	// Generate the public key
	// 	$publicKey->generate();
	// 	// NOTE: You can again do all of this with one line of code like so:
	// 	//       `$publicKey = \Bitpay\PublicKey::create('/tmp/bitpay.pub')->setPrivateKey($privateKey)->generate();`
	// 	/**
	// 	 * Now that you have a private and public key generated, you will need to store
	// 	 * them somewhere. This optioin is up to you and how you store them is up to
	// 	 * you. Please be aware that you MUST store the private key with some type
	// 	 * of security. If the private key is comprimised you will need to repeat this
	// 	 * process.
	// 	 */
	// 	/**
	// 	 * It's recommended that you use the EncryptedFilesystemStorage engine to persist your
	// 	 * keys. You can, of course, create your own as long as it implements the StorageInterface
	// 	 */
	// 	$storageEngine = new EncryptedFilesystemStorage($module->encryption_nonce);
	// 	$storageEngine->persist($privateKey);
	// 	$storageEngine->persist($publicKey);
	// }

	public function createKeyPair() {
		FileHelper::createDirectory($this->module->storage_path);
		$privateKey = new PrivateKey($this->module->storage_path.$this->module::PRIVATE_KEY);
		// Generate a random number
		$privateKey->generate();
		// You can generate a private key with only one line of code like so
		$privateKey = PrivateKey::create($this->module->storage_path.$this->module::PRIVATE_KEY)->generate();
		// NOTE: This has overridden the previous $privateKey variable, although its
		//       not an issue in this case since we have not used this key for
		//       anything yet.
		/**
		 * Once we have a private key, a public key is created from it.
		 */
		$publicKey = new PublicKey($this->module->storage_path.$this->module::PUBLIC_KEY);
		// Inject the private key into the public key
		$publicKey->setPrivateKey($privateKey);
		// Generate the public key
		$publicKey->generate();
		// NOTE: You can again do all of this with one line of code like so:
		//       `$publicKey = \Bitpay\PublicKey::create('/tmp/bitpay.pub')->setPrivateKey($privateKey)->generate();`
		/**
		 * Now that you have a private and public key generated, you will need to store
		 * them somewhere. This optioin is up to you and how you store them is up to
		 * you. Please be aware that you MUST store the private key with some type
		 * of security. If the private key is comprimised you will need to repeat this
		 * process.
		 */
		/**
		 * It's recommended that you use the EncryptedFilesystemStorage engine to persist your
		 * keys. You can, of course, create your own as long as it implements the StorageInterface
		 */
// 		$storageEngine = new EncryptedMongodbStorage(BitpayBridge::ENCRYPTION_CODE);
		$storageEngine = new EncryptedFilesystemStorage($this->module->encryption_nonce);
		$storageEngine->persist($privateKey);
		$storageEngine->persist($publicKey);
	}

	public function currentToken() {

// 		$storageEngine = new EncryptedMongodbStorage(BitpayBridge::ENCRYPTION_CODE);
		$storageEngine = new EncryptedFilesystemStorage($this->module->encryption_nonce);

		$access_token = null;
		$request_new_token = true;

		if (file_exists($this->module->storage_path.$this->module::ACCESS_TOKEN)) {
			try {
				$access_token = $storageEngine->load($this->module->storage_path.$this->module::ACCESS_TOKEN);
			} catch (Exception $ex) {
				throw $ex;
			}
			if ($access_token->isValid()) {
				$request_new_token = false;
			}
		}

		if ($request_new_token) {
			if (!$this->keyPairExists()) {
				$this->createKeyPair();
			}

	    	$privateKey    = $storageEngine->load($this->module->storage_path.$this->module::PRIVATE_KEY);
	    	$publicKey     = $storageEngine->load($this->module->storage_path.$this->module::PUBLIC_KEY);
	    	/**
	    	 * Create the client, there's a lot to it and there are some easier ways, I am
	    	 * showing the long form here to show how various things are injected into the
	    	 * client.
	    	 */
	    	$client = new Client();
	    	/**
	    	 * The network is either livenet or testnet. You can also create your
	    	 * own as long as it implements the NetworkInterface. In this example
	    	 * we will use testnet
	    	 */
	    	// $network = YII_ENV_PROD ? new Livenet() : new Testnet();
				$network = $this->module->network;
	    	/**
	    	 * The adapter is what will make the calls to BitPay and return the response
	    	 * from BitPay. This can be updated or changed as long as it implements the
	    	 * AdapterInterface
	    	 */
	    	$adapter = new CurlAdapter();
	    	/**
	    	 * Now all the objects are created and we can inject them into the client
	    	 */
	    	$client->setPrivateKey($privateKey);
	    	$client->setPublicKey($publicKey);
	    	$client->setNetwork($network);
	    	$client->setAdapter($adapter);
	    	/**
	    	 * Visit https://test.bitpay.com/api-tokens and create a new pairing code. Pairing
	    	 * codes can only be used once and the generated code is valid for only 24 hours.
	    	 */
	    	//     	$pairingCode = 'InsertPairingCodeHere';
// 	    	$pairingCode = BitpayBridge::PAIRING_CODE;
	    	// $pairingCode = BitpayBridge::getPairingCode();
				$pairingCode = $this->module->pairing_code;

	    	/**
	    	 * Currently this part is required, however future versions of the PHP SDK will
	    	 * be refactor and this part may become obsolete.
	    	 */
	    	$sin = SinKey::create()->setPublicKey($publicKey)->generate();
	    	/**** end ****/
	    	$token = null;
	    	try {
	    		$token = $client->createToken(
    				array(
    						'pairingCode' => $pairingCode,
    						'label'       => $this->module->token_label,
    						'id'          => (string) $sin,
    				)
  				);
	    	} catch (\Exception $e) {

	    		// echo $e->getMessage().PHP_EOL;
	    		// echo VarDumper::export($e).PHP_EOL;

					throw $e;

	    		//     		echo VarDumper::export($token).PHP_EOL;

	    		/**
	    		 * The code will throw an exception if anything goes wrong, if you did not
	    		 * change the $pairingCode value or if you are trying to use a pairing
	    		 * code that has already been used, you will get an exception. It was
	    		 * decided that it makes more sense to allow your application to handle
	    		 * this exception since each app is different and has different requirements.
	    		 */
	    		// $request  = $client->getRequest();
	    		// $response = $client->getResponse();
	    		// /**
	    		//  * You can use the entire request/response to help figure out what went
	    		//  * wrong, but for right now, we will just var_dump them.
	    		//  */
	    		// echo (string) $request.PHP_EOL.PHP_EOL.PHP_EOL;
	    		// echo (string) $response.PHP_EOL.PHP_EOL;
	    		// /**
	    		//  * NOTE: The `(string)` is include so that the objects are converted to a
	    		//  *       user friendly string.
	    		//  */
	    		// exit(1); // We do not want to continue if something went wrong

// 				throw $e;
	    	}
	    	/**
	    	 * You will need to persist the token somewhere, by the time you get to this
	    	 * point your application has implemented an ORM such as Doctrine or you have
	    	 * your own way to persist data. Such as using a framework or some other code
	    	 * base such as Drupal.
	    	 */

// 	    	$storageEngine->persistToken(BitpayBridge::TOKEN_ID, $token);
	    	// $storageEngine->persistToken(BitpayBridge::getTokenId(), $token);


				$persistThisValue = $token->getToken();
				$accessTokenStore = (new AccessTokenStore($this->module->storage_path.$this->module::ACCESS_TOKEN))->setAccessToken($persistThisValue);
	      // $accessTokenStore->setAccessToken($persistThisValue);
	      $storageEngine->persist($accessTokenStore);

	    	$access_token = $accessTokenStore;

// 	    	$persistThisValue = $token->getToken();
// 	    	echo 'Token obtained: '.$persistThisValue.PHP_EOL;
	    	/**
	    	 * Make sure you persist the token, you will need it for the next tutorial
	    	 */


		}

		return $access_token->getAccessToken();
	}

	public function createInvoice($config = []) {

// 		$storageEngine = new EncryptedMongodbStorage(self::ENCRYPTION_CODE); // Password may need to be updated if you changed it
		$storageEngine = new EncryptedMongodbStorage($this->module->encryption_nonce); // Password may need to be updated if you changed it
		$privateKey    = $storageEngine->load($this->module->storage_path.$this->module::PRIVATE_KEY);
		$publicKey     = $storageEngine->load($this->module->storage_path.$this->module::PUBLIC_KEY);
		$client        = new Client();
		// $network       = YII_ENV_PROD ? new Livenet() : new Testnet();
		$network = $this->module->network;
		$adapter       = new CurlAdapter();
		$client->setPrivateKey($privateKey);
		$client->setPublicKey($publicKey);
		$client->setNetwork($network);
		$client->setAdapter($adapter);
		// ---------------------------
		/**
		* The last object that must be injected is the token object.
		*/
		// $access_token = self::currentToken()->getToken();
		$access_token = $this->currentToken();

		$token = new Token();
		$token->setToken($access_token); // UPDATE THIS VALUE
		/**
		* Token object is injected into the client
		*/
		$client->setToken($token);

		foreach ($config as $key=>$val) {
			$$key = $val;
		}

		if (!(isset($vjs_trx_id) && isset($description) && isset($total) && isset($currency) && isset($buyer_email) && isset($notification_url))) {
			throw new NotSupportedException();
		}

		/**
		 * This is where we will start to create an Invoice object, make sure to check
		 * the InvoiceInterface for methods that you can use.
		 */
		$invoice = new Invoice();
		/**
		 * Item is used to keep track of a few things
		 */
		$item = new Item();
		$item
		->setCode($vjs_trx_id)
		->setDescription($description)
		->setPrice($total);
		$invoice->setItem($item);

		/**
		 * BitPay supports multiple different currencies. Most shopping cart applications
		 * and applications in general have defined set of currencies that can be used.
		 * Setting this to one of the supported currencies will create an invoice using
		 * the exchange rate for that currency.
		 *
		 * @see https://test.bitpay.com/bitcoin-exchange-rates for supported currencies
		 *
		 */
		$invoice->setCurrency(new Currency($currency));

		$buyer = new Buyer();
		$buyer->setEmail($buyer_email);

		$invoice->setNotificationUrl($notification_url);

		$invoice->setBuyer($buyer);

		/**
		 * Updates invoice with new information such as the invoice id and the URL where
		 * a customer can view the invoice.
		 */
		try {
			$client->createInvoice($invoice);
		} catch (\Exception $e) {
			$request  = $client->getRequest();
			$response = $client->getResponse();
			// echo (string) $request.PHP_EOL.PHP_EOL.PHP_EOL;
			// echo (string) $response.PHP_EOL.PHP_EOL;
// 			exit(1); // We do not want to continue if something went wrong
			throw $e;
		}

		return $invoice;
	}

	public function getInvoice($invoice_id) {

// 		$storageEngine = new EncryptedMongodbStorage(BitpayBridge::ENCRYPTION_CODE); // Password may need to be updated if you changed it
		$storageEngine = new EncryptedMongodbStorage($this->module->encryption_nonce); // Password may need to be updated if you changed it
		$privateKey    = $storageEngine->load($this->module->storage_path.$this->module::PRIVATE_KEY);
		$publicKey     = $storageEngine->load($this->module->storage_path.$this->module::PUBLIC_KEY);
		$client        = new Client();
		// $network       = YII_ENV_PROD ? new Livenet() : new Testnet();
		$network = $this->module->network;
		$adapter       = new CurlAdapter();
		$client->setPrivateKey($privateKey);
		$client->setPublicKey($publicKey);
		$client->setNetwork($network);
		$client->setAdapter($adapter);
		// ---------------------------
		/**
		* The last object that must be injected is the token object.
		*/
		// $access_token = BitpayBridge::currentToken()->getToken();
		$access_token = $this->currentToken();

		$token = new Token();
		$token->setToken($access_token); // UPDATE THIS VALUE
		/**
		* Token object is injected into the client
		*/
		$client->setToken($token);

		$invoice = null;

		try {
			$invoice = $client->getInvoice($invoice_id);
		} catch (\Exception $e) {
			$request  = $client->getRequest();
			$response = $client->getResponse();
			echo (string) $request.PHP_EOL.PHP_EOL.PHP_EOL;
			echo (string) $response.PHP_EOL.PHP_EOL;
		}

		return $invoice;
	}
}
