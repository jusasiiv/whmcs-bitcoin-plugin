<?php

namespace Blockonomics;

use WHMCS\Database\Capsule;

class Blockonomics {

	/*
	 * Try to get callback secret from db
	 * If no secret exists, create new
	 */
	public function getCallbackSecret() {

		$api_secret = '';

		try {
			$api_secret = Capsule::table('tblpaymentgateways')
					->where('gateway', 'blockonomics')
					->where('setting', 'ApiSecret')
					->value('value');

		} catch(\Exception $e) {
			echo "Error, could not get Blockonomics secret from database. {$e->getMessage()}";
		}

		if($api_secret == '') {
			$api_secret = $this->generateCallbackSecret();
		}

		return $api_secret;
	}

	/*
	 * Generate new callback secret using sha1, save it in db under tblpaymentgateways table
	 */
	private function generateCallbackSecret() {

		try {
			$callback_secret = sha1(openssl_random_pseudo_bytes(20));

			$callback_secret = $this->getSystemUrl() . 'modules/gateways/callback/blockonomics.php?secret=' . $callback_secret;

			$api_secret = Capsule::table('tblpaymentgateways')
					->where('gateway', 'blockonomics')
					->where('setting', 'ApiSecret')
					->update(['value' => $callback_secret]);

		} catch(\Exception $e) {
			echo "Error, could not get Blockonomics secret from database. {$e->getMessage()}";
		}

		return $callback_secret;
	}

	/*
	 * Generate new order status into the database if it does not exist
	 */
	public function addOrderStatusIfNotExists() {

		try {
			$order_status = Capsule::table('tblorderstatuses')
					->where('title', 'Waiting for Bitcoin Confirmation')
					->value('title');

			if(!$order_status) {

				Capsule::table('tblorderstatuses')->insert(
					[
						'title' => 'Waiting for Bitcoin Confirmation',
						'color' => '#1a4d80',
						'showpending' => 1,
						'showactive' => 0,
						'showcancelled' => 0,
						'sortorder' => 50,
					]);
			}
		} catch(\Exception $e) {
			echo "Error, could not get Blockonomics secret from database. {$e->getMessage()}";
		}
	}

	/*
	 * Get user configured API key from database
	 */
	public function getApiKey() {
		return Capsule::table('tblpaymentgateways')
			->where('gateway', 'blockonomics')
			->where('setting', 'ApiKey')
			->value('value');
	}

	/*
	 * Update order status to 'Waiting for Bitcoin Confirmation'
	 */
	public function updateOrderStatus($orderId, $status) {
		Capsule::table('tblorders')
			->where('id', $orderId)
			->update(['status' => $status]);
	}

	/*
	 * Update invoice status
	 */
	public function updateInvoiceStatus($invoiceId, $status) {
		Capsule::table('tblinvoices')
			->where('id', $invoiceId)
			->update(['status' => $status]);
	}

	/*
	 * Update order note
	 */
	public function updateOrderNote($orderId, $note) {
		Capsule::table('tblorders')
			->where('id', $orderId)
			->update(['notes' => $note]);
	}

	/*
	 * Update invoice note
	 */
	public function updateInvoiceNote($invoiceid, $note) {
		Capsule::table('tblinvoices')
			->where('id', $invoiceid)
			->update(['notes' => $note]);
	}

	/*
	 * Get order id by invoice id
	 */
	public function getOrderIdByInvoiceId($invoiceId) {
		return Capsule::table('tblorders')
			->where('invoiceid', $invoiceId)
			->value('id');
	}


	/*
	 * Get new address from Blockonomics Api
	 */
	public function getNewBitcoinAddress() {

		$api_key = $this->getApiKey();
		$secret = $this->getCallbackSecret();

		// Secret is formatted http://url.com?secret=abc123,
		// Get last 40 chars of the secret string
		$secret = substr($secret, -40);

		$options = [
			'http' => [
				'header'  => 'Authorization: Bearer ' . $api_key,
				'method'  => 'POST',
				'content' => '',
				'ignore_errors' => true
			]
		];

		$context = stream_context_create($options);
		$contents = file_get_contents("https://www.blockonomics.co/api/new_address?match_callback=$secret", false, $context);
		$responseObj = json_decode($contents);

		//Create response object if it does not exist
		if (!isset($responseObj)) $responseObj = new stdClass();
		$responseObj->{'response_code'} = $http_response_header[0];

		return $responseObj;
	}

	/*
	 * Convert fiat amount to BTC
	 */
	public function getBitcoinAmount($fiat_amount, $currency) {
		try {
			$options = [ 'http' => [ 'method'  => 'GET'] ];
			$context = stream_context_create($options);
			$contents = file_get_contents('https://www.blockonomics.co/api/price' . "?currency=$currency", false, $context);
			$price = json_decode($contents);
		} catch (\Exception $e) {
			echo "Error getting price from Blockonomics! {$e->getMessage()}";
		}

		return intval(1.0e8 * $fiat_amount/$price->price);
	}

	/*
	 * If no Blockonomics order table exists, create it
	 */
	public function createOrderTableIfNotExist() {

		if (!Capsule::schema()->hasTable('blockonomics_bitcoin_orders')) {

			try {
				Capsule::schema()->create( 'blockonomics_bitcoin_orders', function ($table) {
							$table->increments('id');
							$table->integer('id_order');
							$table->text('txid');
							$table->integer('timestamp');
							$table->text('addr');
							$table->integer('status');
							$table->float('value');
							$table->integer('bits');
							$table->integer('bits_payed');
						}
				);
			} catch (\Exception $e) {
					echo "Unable to create blockonomics_bitcoin_orders: {$e->getMessage()}";
			}
		}
	}

	/*
	 * Try to insert new order to database
	 * If order exists, return with false
	 */
	public function insertOrderToDb($id_order, $address, $value, $bits) {

		try {
			$existing_order = Capsule::table('blockonomics_bitcoin_orders')
				->where('id_order', $id_order)
				->value('id');
		} catch (\Exception $e) {
				echo "Unable to select order from blockonomics_bitcoin_orders: {$e->getMessage()}";
		}

		if($existing_order) {
			return false;
		}

		try {
			Capsule::table('blockonomics_bitcoin_orders')->insert(
				[
					'id_order' => $id_order,
					'addr' => $address,
					'timestamp' => time(),
					'status' => -1,
					'value' => $value,
					'bits' => $bits,
				]
			);
		} catch (\Exception $e) {
				echo "Unable to insert new order into blockonomics_bitcoin_orders: {$e->getMessage()}";
		}

		return true;
	}

	/*
	 * Try to get order row from db by address
	 */
	public function getOrderByAddress($bitcoinAddress) {
		try {
			$existing_order = Capsule::table('blockonomics_bitcoin_orders')
				->where('addr', $bitcoinAddress)
				->first();
		} catch (\Exception $e) {
				echo "Unable to select order from blockonomics_bitcoin_orders: {$e->getMessage()}";
		}

		$row_in_array = array(
			"id" => $existing_order->id,
			"order_id" => $existing_order->id_order,
			"timestamp"=> $existing_order->timestamp,
			"status" => $existing_order->status,
			"value" => $existing_order->value,
			"bits" => $existing_order->bits,
			"bits_payed" => $existing_order->bits_payed
		);

		return $row_in_array;
	}

	public function updateOrderInDb($addr, $txid, $status, $bits_payed) {
		try {
			Capsule::table('blockonomics_bitcoin_orders')
					->where('addr', $addr)
					->update([
						'txid' => $txid,
						'status' => $status,
						'bits_payed' => $bits_payed
					]
				);
			} catch (\Exception $e) {
				echo "Unable to update order to blockonomics_bitcoin_orders: {$e->getMessage()}";
		}
	}

	/*
	 * Get URL of the WHMCS installation
	 */
	public function getSystemUrl() {
		return Capsule::table('tblconfiguration')
			->where('setting', 'SystemURL')
			->value('value');
	}

	private function checkForErrors($responseObj) {

		if(!isset($responseObj->response_code)) {
				$error_str = 'Your webhost is blocking outgoing HTTPS connections. Blockonomics requires an outgoing HTTPS POST (port 443) to generate new address. Check with your webhosting provider to allow this.';

		} elseif(!ini_get('allow_url_fopen')) {
				$error_str = 'The allow_url_fopen is not enabled, please enable this option to allow address generation.';

		} else {

				switch ($responseObj->response_code) {

					case 'HTTP/1.1 200 OK':
							break;

					case 'HTTP/1.1 401 Unauthorized': {
							$error_str = 'API Key is incorrect. Make sure that the API key set in admin Blockonomics module configuration is correct.';
							break;
					}

					case 'HTTP/1.1 500 Internal Server Error': {

						if(isset($responseObj->message)) {

							$error_code = $responseObj->message;

							switch ($error_code) {
								case "Could not find matching xpub":
										$error_str = 'There is a problem in the Callback URL. Make sure that you have set your Callback URL from the admin Blockonomics module configuration to your Merchants > Settings.';
										break;
								case "This require you to add an xpub in your wallet watcher":
										$error_str = 'There is a problem in the XPUB. Make sure that the you have added an address to Wallet Wathcer > Address Wathcer. If you have added an address make sure that it is an XPUB address and not a Bitcoin address.';
										break;
								default:
										$error_str = $responseObj->message;
							}
							break;
						} else {
								$error_str = $responseObj->response_code;
								break;
						}
					}

					default:
							$error_str = $responseObj->response_code;
							break;
				}
		}

		if(isset($error_str)) {
			return $error_str;
		}

		// No errors
		return false;
	}

}
