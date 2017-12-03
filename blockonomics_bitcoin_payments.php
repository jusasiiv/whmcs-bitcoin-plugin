<?php
/**
 * Blockonomics payment model
 *
 * @author      Blockonomics
 * @copyright   Blockonomics (https://blockonomics.com)
 */

function blockonomics_merchantgateway_MetaData()
{
	return array(
		'DisplayName' => 'Blockonomics Merchant Gateway Module',
		'APIVersion' => '1.1',
		'DisableLocalCreditCardInput' => true,
		'TokenisedStorage' => false,
	);
}

function blockonomics_merchantgateway_config()
{
		return array(
				// the friendly display name for a payment gateway should be
				// defined here for backwards compatibility
				'FriendlyName' => array(
						'Type' => 'System',
						'Value' => 'Blockonomics Merchant Gateway Module',
				),
				// a text field type allows for single line text input
				'title' => array(
						'FriendlyName' => 'Title',
						'Type' => 'text',
						'Size' => '50',
						'Default' => 'Bitcoin',
						'Description' => 'Payment method title',
				),
				// a text field type allows for single line text input
				'apiKey' => array(
						'FriendlyName' => 'API Key',
						'Type' => 'text',
						'Size' => '50',
						'Default' => '',
						'Description' => 'Enter your API key here',
				),
		);
}

?>