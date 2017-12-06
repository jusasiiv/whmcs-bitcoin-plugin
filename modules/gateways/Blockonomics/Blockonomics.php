<?php

namespace Blockonomics;

use WHMCS\Database\Capsule;

class Blockonomics {

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
}
