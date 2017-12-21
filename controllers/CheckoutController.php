<?php
/**
 * @license Copyright 2017 Thomas Varghese, MIT License
 * see https://github.com/numbervine/yii2-bitpay/blob/master/LICENSE
 */
namespace numbervine\bitpay\controllers;

use yii\filters\AccessControl;

class CheckoutController extends \yii\web\Controller
{
	public $module = null;

	public function init()
	{
			parent::init();
			$this->module = \Yii::$app->getModule('bitpay');
	}

	public function behaviors()
	{
		return [
				'access' => [
						'class' => AccessControl::className(),
						'rules' => [
								[
										'allow' => true,
										// 'roles' => ['@']
								],
						],
				],
		];
	}

	private function bundleResponse($success, $payload, $msg=null, $errors=null) {
		$result = [
			'success' => $success,
			'message' => $msg,
			'errors' => $errors,
			'code' => 401
		];

		if ($success) {
			$_success_msg = $msg ? $msg : 'success';
			$_errors = null;
			$result = [
				'success' => $success,
				'message' => $_success_msg,
				'errors' => $_errors,
				'code' => 200
			];
			if ($payload) {
				$result['payload'] = $payload;
			}
		}

		$response = \Yii::createObject([
				'class' => 'yii\web\Response',
				'format' => \yii\web\Response::FORMAT_JSON,
				'data' => $result
		]);

		$headers = $response->headers;
		$headers->add('Access-Control-Allow-Origin', '*');
		$headers->add('Access-Control-Allow-Credentials', true);

		return $response;
	}

	public function actionCreateInvoice()
	{
		$content = [];
		$success = false;
		$message = 'NA';
		$errors = null;

  	if (\Yii::$app->request->isAjax) {
			if (isset($data['invoice_id']) && isset($data['customer_id']) && isset($data['amount'])) {

				$invoice_config = [];
				$invoice_config['currency'] = $order_details['amount']['currency'];
				$invoice_config['total'] = $order_details['amount']['total'];
				$invoice_config['description'] = $order_details['description'];
				$invoice_config['vjs_trx_id'] = $invoice_id;
				$invoice_config['buyer_email'] = User::findIdentity($customer_id)->email;
				$invoice_config['notification_url'] = \Yii::$app->urlManager->createAbsoluteUrl('/bitpay/checkout/notify','https');

				$bitpay_invoice = $this->module->bitpay_bridge->createInvoice($invoice_config);
				$process_report = $this->module->app_bridge->processBitpayInvoice(['bitpay_invoice'=>$bitpay_invoice]);

				$success = true;
				$content['bitpay_invoice'] = $bitpay_invoice;
				$content['process_report'] = $process_report;
				$message = 'success';
			}
		}

		return $this->bundleResponse($success,$content,$message,$errors);
	}

	public function actionQueryInvoice()
	{
		$content = [];
		$success = false;
		$message = 'NA';
		$errors = null;

  	if (\Yii::$app->request->isAjax) {

			$data = json_decode(stripslashes($_POST['data']),true);
  		if (isset($data['bitpay_invoice_id'])) {
  			$bitpay_invoice_id = $data['bitpay_invoice_id'];
				$bitpay_invoice = $this->module->bitpay_bridge->getInvoice($bitpay_invoice_id);
				$process_report = $this->module->app_bridge->processBitpayInvoice(['bitpay_invoice'=>$bitpay_invoice]);

				$success = true;
				$content['bitpay_invoice'] = $bitpay_invoice;
				$content['process_report'] = $process_report;
				$message = 'success';
			}
		}

		return $this->bundleResponse($success,$content,$message,$errors);
	}

  public function actionNotify()
  {
  	if (isset($_POST)) {
  		// $collection_name = BitpayBridge::notificationLogCollectionName();
  		// $document_identifier_config = [];
  		// $update_arr = $_POST;
			//
  		// XupaHelpers::addOrUpdateMongoDbDocument($collection_name, $document_identifier_config, $update_arr);
  	}
  }


  public function beforeAction($action) {
  	// this is specifically to address a phantomjs CSRF cookie related bug
  	// https://github.com/yiisoft/yii2/issues/5808
  	// https://github.com/ariya/phantomjs/issues/14047
  	if ($action->id=='notify') {
  		$this->enableCsrfValidation = false;
  	}
  	return parent::beforeAction($action);
  }
}
