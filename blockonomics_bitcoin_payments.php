<?php
/**
 * Blockonomics payment model
 *
 * @author      Blockonomics
 * @copyright   Blockonomics (https://blockonomics.com)
 */

function blockonomics_MetaData()
{
	return array(
		'DisplayName' => 'Blockonomics Merchant Gateway Module',
		'APIVersion' => '1.1',
		'DisableLocalCreditCardInput' => true,
		'TokenisedStorage' => false,
	);
}

function blockonomics_config()
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

function blockonomics_link($params)
{
    // Gateway Configuration Parameters
    $title = $params['title'];
    $apiKey = $params['apiKey'];
    // Invoice Parameters
    $invoiceId = $params['invoiceid'];
    $description = $params["description"];
    $amount = $params['amount'];
    $currencyCode = $params['currency'];
    // Client Parameters
    $firstname = $params['clientdetails']['firstname'];
    $lastname = $params['clientdetails']['lastname'];
    $email = $params['clientdetails']['email'];
    $address1 = $params['clientdetails']['address1'];
    $address2 = $params['clientdetails']['address2'];
    $city = $params['clientdetails']['city'];
    $state = $params['clientdetails']['state'];
    $postcode = $params['clientdetails']['postcode'];
    $country = $params['clientdetails']['country'];
    $phone = $params['clientdetails']['phonenumber'];
    // System Parameters
    $companyName = $params['companyname'];
    $systemUrl = $params['systemurl'];
    $returnUrl = $params['returnurl'];
    $langPayNow = $params['langpaynow'];
    $moduleDisplayName = $params['name'];
    $moduleName = $params['paymentmethod'];
    $whmcsVersion = $params['whmcsVersion'];
    $url = 'https://www.demopaymentgateway.com/do.payment';
    $postfields = array();
    $postfields['username'] = $username;
    $postfields['invoice_id'] = $invoiceId;
    $postfields['description'] = $description;
    $postfields['amount'] = $amount;
    $postfields['currency'] = $currencyCode;
    $postfields['first_name'] = $firstname;
    $postfields['last_name'] = $lastname;
    $postfields['email'] = $email;
    $postfields['address1'] = $address1;
    $postfields['address2'] = $address2;
    $postfields['city'] = $city;
    $postfields['state'] = $state;
    $postfields['postcode'] = $postcode;
    $postfields['country'] = $country;
    $postfields['phone'] = $phone;
    $postfields['callback_url'] = $systemUrl . '/modules/gateways/callback/' . $moduleName . '.php';
    $postfields['return_url'] = $returnUrl;
    $htmlOutput = '<form method="post" action="' . $url . '">';
    foreach ($postfields as $k => $v) {
        $htmlOutput .= '<input type="hidden" name="' . $k . '" value="' . urlencode($v) . '" />';
    }
    $htmlOutput .= '<input type="submit" value="' . $langPayNow . '" />';
    $htmlOutput .= '</form>';
    return $htmlOutput;
}

function blockonomics_refund($params)
{
    // Gateway Configuration Parameters
    $title = $params['title'];
    $apiKey = $params['apiKey'];
    // Transaction Parameters
    $transactionIdToRefund = $params['transid'];
    $refundAmount = $params['amount'];
    $currencyCode = $params['currency'];
    // Client Parameters
    $firstname = $params['clientdetails']['firstname'];
    $lastname = $params['clientdetails']['lastname'];
    $email = $params['clientdetails']['email'];
    $address1 = $params['clientdetails']['address1'];
    $address2 = $params['clientdetails']['address2'];
    $city = $params['clientdetails']['city'];
    $state = $params['clientdetails']['state'];
    $postcode = $params['clientdetails']['postcode'];
    $country = $params['clientdetails']['country'];
    $phone = $params['clientdetails']['phonenumber'];
    // System Parameters
    $companyName = $params['companyname'];
    $systemUrl = $params['systemurl'];
    $langPayNow = $params['langpaynow'];
    $moduleDisplayName = $params['name'];
    $moduleName = $params['paymentmethod'];
    $whmcsVersion = $params['whmcsVersion'];
    // perform API call to initiate refund and interpret result
    return array(
        // 'success' if successful, otherwise 'declined', 'error' for failure
        'status' => 'success',
        // Data to be recorded in the gateway log - can be a string or array
        'rawdata' => $responseData,
        // Unique Transaction ID for the refund transaction
        'transid' => $refundTransactionId,
        // Optional fee amount for the fee value refunded
        'fees' => $feeAmount,
    );
}

function blockonomics_cancelSubscription($params)
{
    // Gateway Configuration Parameters
    $title = $params['title'];
    $apiKey = $params['apiKey'];
    // Subscription Parameters
    $subscriptionIdToCancel = $params['subscriptionID'];
    // System Parameters
    $companyName = $params['companyname'];
    $systemUrl = $params['systemurl'];
    $langPayNow = $params['langpaynow'];
    $moduleDisplayName = $params['name'];
    $moduleName = $params['paymentmethod'];
    $whmcsVersion = $params['whmcsVersion'];
    // perform API call to cancel subscription and interpret result
    return array(
        // 'success' if successful, any other value for failure
        'status' => 'success',
        // Data to be recorded in the gateway log - can be a string or array
        'rawdata' => $responseData,
    );
}


?>