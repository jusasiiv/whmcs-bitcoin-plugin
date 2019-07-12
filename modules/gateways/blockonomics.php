<?php

require_once(dirname(__FILE__) . '/Blockonomics/Blockonomics.php');

use Blockonomics\Blockonomics;

function blockonomics_config() {

	// When loading plugin setup page, run custom JS
	add_hook('AdminAreaFooterOutput', 1, function($vars) {

		$blockonomics = new Blockonomics();
		$system_url = $blockonomics->getSystemUrl();
		$callback_url = $blockonomics->getCallbackSecret();

		return <<<HTML
		<script type="text/javascript">
			/**
			 * Disable callback url editing
			 */
			var inputFields = document.getElementsByName('field[ApiSecret]');
			inputFields.forEach(function(element) {
				element.value = '$callback_url';
				element.readOnly = true;
			});

			/**
			 * Padding for config labels
			 */
			var inputLabels = document.getElementsByClassName('fieldlabel');

			for(var i = 0; i < inputLabels.length; i++) {
				inputLabels[i].style.paddingRight = '20px';
			}

			/**
			 * Set available values for margin setting
			 */
			var inputMargin = document.getElementsByName('field[Margin]');
			inputMargin.forEach(function(element) {
				element.type = 'number';
				element.min = 0;
				element.max = 4;
				element.step = 0.01;
			});
			var inputSlack = document.getElementsByName('field[Slack]');
			inputSlack.forEach(function(element) {
				element.type = 'number';
				element.min = 0;
				element.max = 10;
				element.step = 0.01;
			});

			/**
			 * Generate Test Setup button and setup result field
			 */
			var settingsTable = document.getElementById("Payment-Gateway-Config-blockonomics");

			var testSetupBtnRow = settingsTable.insertRow(settingsTable.rows.length - 1);
			var testSetupLabelCell = testSetupBtnRow.insertCell(0);
			var testSetupBtnCell = testSetupBtnRow.insertCell(1);
			testSetupBtnCell.className = "fieldarea";

			var testSetupResultRow = settingsTable.insertRow(settingsTable.rows.length - 1);
			testSetupResultRow.style.display = "none";
			var testSetupResultLabel = testSetupResultRow.insertCell(0);
			var testSetupResultCell = testSetupResultRow.insertCell(1);
			testSetupResultCell.className = "fieldarea";

			var newBtn = document.createElement('BUTTON');
			newBtn.className = "btn btn-primary";

			var t = document.createTextNode("Test Setup");
			newBtn.appendChild(t);

			testSetupBtnCell.appendChild(newBtn);

			function reqListener () {
				var responseObj = {};
				try {
					responseObj = JSON.parse(this.responseText);
				} catch (err) {
					var testSetupUrl = "$system_url" + "testSetup.php";
					responseObj.error = true;
					responseObj.errorStr = 'Unable to locate/execute ' + testSetupUrl + '. Contact blockonomics support for help';
				}
				if (responseObj.error) {
					testSetupResultCell.innerHTML = "<label style='color:red;'>Error:</label> " + responseObj.errorStr + 
					"<br>For more information, please consult <a href='https://blockonomics.freshdesk.com/support/solutions/articles/33000215104-troubleshooting-unable-to-generate-new-address' target='_blank'>this troubleshooting article</a>";
				} else {
					testSetupResultCell.innerHTML = "<label style='color:green;'>Congrats! Setup is all done</label>";
				}
				newBtn.disabled = false;
			}

			newBtn.onclick = function() {
				testSetupResultRow.style.display = "table-row";
				var testSetupUrl = "$system_url" + "testSetup.php";
				var systemUrlProtocol = new URL("$system_url").protocol;
				if (systemUrlProtocol != location.protocol) {
					testSetupResultCell.innerHTML = "<label style='color:red;'>Error:</label> \
							System URL has a different protocol than current URL. Go to Setup > General Settings and verify that WHMCS System URL has \
							correct protocol set (HTTP or HTTPS).";
					return false;
				}
				var oReq = new XMLHttpRequest();
				oReq.addEventListener("load", reqListener);
				oReq.open("GET", testSetupUrl);
				oReq.send();

				newBtn.disabled = true;
				testSetupResultCell.innerHTML = "Testing setup...";

				return false;
			}

			/**
			 * Prompt to save changes after setting a new API key 
			 */
			var apiKeyField = document.getElementsByName('field[ApiKey]')[0];
			apiKeyField.onchange = function() {
				testSetupResultRow.style.display = "table-row";
				testSetupResultCell.innerHTML = "<label style='color:#337ab7;'>New API Key: Save your changes and then click 'Test Setup'</label>";
			}

		</script>
HTML;

	});

	$blockonomics = new Blockonomics();
	$blockonomics->createOrderTableIfNotExist();
	
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
    'TimePeriod' => array(
        'FriendlyName' => 'Time Period',
        'Type' => 'dropdown',
        'Options' => array(
            '10' => '10',
            '15' => '15',
            '20' => '20',
            '25' => '25',
            '30' => '30',
        ),
        'Description' => 'Time period of countdown timer on payment page (in minutes)',
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
		'Slack' => array(
				'FriendlyName' => 'Underpayment Slack %',
				'Type' => 'text',
				'Size' => '5',
				'Default' => 0,
				'Description' => 'Allow payments that are off by a small percentage',
		),
	);
}

function blockonomics_link($params) {
	
	if (false === isset($params) || true === empty($params)) {
		die('[ERROR] In modules/gateways/Blockonomics.php::Blockonomics_link() function: Missing or invalid $params data.');
	}

	$blockonomics = new Blockonomics();
	$system_url = $blockonomics->getSystemUrl();
	$form_url = $system_url . 'payment.php';

	$form = '<form action="' . $form_url . '" method="POST">';
	$form .= '<input type="hidden" name="price" value="'. $params['amount'] .'"/>';
	$form .= '<input type="hidden" name="currency" value="'. $params['currency'] .'"/>';
	$form .= '<input type="hidden" name="order_id" value="'. $params['invoiceid'] .'"/>';
	$form .= '<input type="submit" value="'. $params['langpaynow'] .'"/>';
	$form .= '</form>';

	return $form;
}
