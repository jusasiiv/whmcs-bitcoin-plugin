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
	 * Get user configured API key from database
	 */
	public function getApiKey() {
		return Capsule::table('tblpaymentgateways')
			->where('gateway', 'blockonomics')
			->where('setting', 'ApiKey')
			->value('value');
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
				'content' => ''
			]
		];

		try {
			$context = stream_context_create($options);
			$separator = '?reset=1&';
			$contents = file_get_contents('https://www.blockonomics.co/api/new_address'.$separator."match_callback=$secret", false, $context);
			$new_address = json_decode($contents);
		} catch (\Exception $e) {
			echo "Error getting new address from Blockonomics! {$e->getMessage()}";
		}

		return $new_address->address;
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
							$table->integer('timestamp');
							$table->text('addr');
							$table->text('txid');
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

}
