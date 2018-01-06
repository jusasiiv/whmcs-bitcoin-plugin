<?php

require_once(dirname(__FILE__) . '/Blockonomics/Blockonomics.php');

use Blockonomics\Blockonomics;

function blockonomics_config() {

	// When loading payment gateway setup page, disable editing of callback url field
	add_hook('AdminAreaFooterOutput', 1, function($vars) {
    return <<<HTML
		<script type="text/javascript">
			var inputFields = document.getElementsByName('field[ApiSecret]');
			inputFields.forEach(function(element) {
				element.readOnly = true;
			});
		</script>
HTML;

	});

	$blockonomics = new Blockonomics();
	$blockonomics->createOrderTableIfNotExist();
	$blockonomics->addOrderStatusIfNotExists();
	$secret_value = $blockonomics->getCallbackSecret();
	
	return array(
		'FriendlyName' => array(
			'Type'       => 'System',
			'Value'      => 'Blockonomics'
		),
		'ApiKey' => array(
			'FriendlyName' => 'API Key',
			'Description'  => 'BLOCKONOMICS API KEY (Generate from <a target="_blank" href="https://www.blockonomics.co/blockonomics#/settings">Wallet Watcher</a> > Settings)  ',
			'Type'         => 'text'
		),
		'ApiSecret' => array(
			'FriendlyName' => 'Callback URL',
			'Description'  => 'CALLBACK URL (Copy this url and set in <a target="_blank" href="https://www.blockonomics.co/merchants#/page6">Merchants</a>)',
			'Type'         => 'text'
		)
	);
}

function blockonomics_link($params) {
	
	if (false === isset($params) || true === empty($params)) {
		die('[ERROR] In modules/gateways/Blockonomics.php::Blockonomics_link() function: Missing or invalid $params data.');
	}

	$blockonomics_params = array(
		'order_id'         => $params['invoiceid'],
		'price'            => number_format($params['amount'], 2, '.', ''),
		'currency'         => $params['currency'],
		'receive_currency' => $params['ReceiveCurrency'],
		'cancel_url'       => $params['systemurl'] . '/clientarea.php',
		'callback_url'     => $params['systemurl'] . '/modules/gateways/callback/Blockonomics.php',
		'success_url'      => $params['systemurl'] . '/viewinvoice.php?id=' . $params['invoiceid'],
		'title'            => $params['companyname'],
		'description'      => $params['description']
	);

	$authentication = array(
		'app_id' => $params['AppID'],
		'api_key' => $params['ApiKey'],
		'api_secret' => $params['ApiSecret'],
		'environment' => $params['Environment'],
		'user_agent' => 'Blockonomics - WHMCS Extension',
	);

	//$order = \Blockonomics\Merchant\Order::createOrFail($blockonomics_params, array(), $authentication);

	$form = '<form action="/whmcs/payment.php" method="POST">';
	$form .= '<input type="hidden" name="price" value="'. $params['amount'] .'"/>';
	$form .= '<input type="hidden" name="currency" value="'. $params['currency'] .'"/>';
	$form .= '<input type="hidden" name="order_id" value="'. $params['invoiceid'] .'"/>';
	$form .= '<input type="submit" value="'. $params['langpaynow'] .'"/>';
	$form .= '</form>';

	return $form;
}