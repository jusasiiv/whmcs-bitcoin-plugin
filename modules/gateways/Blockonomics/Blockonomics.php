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
	 * Get new address from Blockonomics Api
	 */
	public function getNewBitcoinAddress() {
		$api_key = Capsule::table('tblpaymentgateways')
			->where('gateway', 'blockonomics')
			->where('setting', 'ApiKey')
			->value('value');

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
			echo "Error getting new address from Blockonomics!";;
		}

		return $new_address->address;
	}

}
