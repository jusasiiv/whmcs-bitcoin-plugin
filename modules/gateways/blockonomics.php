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

			var inputLabels = document.getElementsByClassName('fieldlabel');

			for(var i = 0; i < inputLabels.length; i++) {
				inputLabels[i].style.paddingRight = '20px';
			}

			var inputMargin = document.getElementsByName('field[Margin]');
			inputMargin.forEach(function(element) {
				element.type = 'number';
				element.min = 0;
				element.max = 4;
				element.step = 0.01;
			});
		</script>
HTML;

	});

	$blockonomics = new Blockonomics();
	$blockonomics->createOrderTableIfNotExist();
	$secret_value = $blockonomics->getCallbackSecret();
	
	return array(
		'FriendlyName' => array(
			'Type'       => 'System',
			'Value'      => 'Blockonomics'
		),
		'ApiKey' => array(
			'FriendlyName' => 'API Key',
			'Description'  => 'BLOCKONOMICS API KEY (Click "Get Started For Free" on <a target="_blank" href="https://www.blockonomics.co/blockonomics#/merchants">Merchants</a> and follow setup wizard)  ',
			'Type'         => 'text'
		),
		'ApiSecret' => array(
			'FriendlyName' => 'Callback URL',
			'Description'  => 'CALLBACK URL (Copy this url and set in <a target="_blank" href="https://www.blockonomics.co/merchants#/page6">Merchants</a>)',
			'Type'         => 'text'
		),
		'Altcoins' => array(
				'FriendlyName' => 'Altcoins enabled',
				'Type' => 'yesno',
				'Description' => 'Select if you want to accept altcoins via Flyp.me',
		),
		'Margin' => array(
				'FriendlyName' => 'Extra Currency Rate Margin %',
				'Type' => 'text',
				'Size' => '5',
				'Default' => 0,
				'Description' => 'Increase live fiat to BTC rate by small percent',
		),
	);
}

function blockonomics_link($params) {
	
	if (false === isset($params) || true === empty($params)) {
		die('[ERROR] In modules/gateways/Blockonomics.php::Blockonomics_link() function: Missing or invalid $params data.');
	}

	$blockonomics = new Blockonomics();
	$system_url = $blockonomics->getSystemUrl();
	$form_url = $system_url . '/payment.php';

	$form = '<form action="' . $form_url . '" method="POST">';
	$form .= '<input type="hidden" name="price" value="'. $params['amount'] .'"/>';
	$form .= '<input type="hidden" name="currency" value="'. $params['currency'] .'"/>';
	$form .= '<input type="hidden" name="order_id" value="'. $params['invoiceid'] .'"/>';
	$form .= '<input type="submit" value="'. $params['langpaynow'] .'"/>';
	$form .= '</form>';

	return $form;
}