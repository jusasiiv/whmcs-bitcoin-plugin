<?php

// Require libraries needed for gateway module functions.
include '../../../init.php';
include '../../../includes/gatewayfunctions.php';
include '../../../includes/invoicefunctions.php';

include '../Blockonomics/Blockonomics.php';

use Blockonomics\Blockonomics;
// Init Blockonomics class
$blockonomics = new Blockonomics();

$gatewayModuleName = 'blockonomics';

// Fetch gateway configuration parameters.
$gatewayParams = getGatewayVariables($gatewayModuleName);

// Die if module is not active.
if (!$gatewayParams['type']) {
	die("Module Not Activated");
}

// Retrieve data returned in payment gateway callback
$secret = $_GET['secret'];
$status = $_GET['status'];
$addr = $_GET['addr'];
$value = $_GET['value'];
$txid = $_GET['txid'];

/**
 * Validate callback authenticity.
 */
$secret_value = $blockonomics->getCallbackSecret();
$secret_value = substr($secret_value, -40);

if ($secret_value != $secret) {
	$transactionStatus = 'Secret verification failure';
	$success = false;

	echo "Verification error";
	die();
}

$order = $blockonomics->getOrderByAddress($addr);
$invoiceId = $order['order_id'];
$bits = $order['bits'];

if($status == 0) {

	$orderNote = "Waiting for Confirmation on Bitcoin network \r" .
		"Bitcoin transaction id: $txid \r" .
		"You can view the transaction at:\r" .
		"https://www.blockonomics.co/api/tx?txid=$txid&addr=$addr";


	$invoiceNote = "<b>Waiting for Confirmation on Bitcoin network</b>\r\r" .
		"Bitcoin transaction id:\r" .
		"<a target=\"_blank\" href=\"https://www.blockonomics.co/api/tx?txid=$txid&addr=$addr\">$txid</a>";

	$blockonomics->updateOrderInDb($addr, $txid, $status, $value);
	$true_order_id = $blockonomics->getOrderIdByInvoiceId($invoiceId);
	$blockonomics->updateOrderNote($true_order_id, $orderNote);
	$blockonomics->updateInvoiceNote($invoiceId, $invoiceNote);

	die();
}

if($status != 2) {
	die();
}

$true_order_id = $blockonomics->getOrderIdByInvoiceId($invoiceId);

$expected = $bits / 1.0e8;
$paid = $value / 1.0e8;
$orderNote = "";

if($value < $bits) {
	$price_by_expected = $blockonomics->getPriceByExpected($invoiceId);
	$paymentAmount = round($paid*$price_by_expected, 2);
} else {
	$paymentAmount = '';
}

$invoiceNote = "Bitcoin transaction id:\r" .
	"<a target=\"_blank\" href=\"https://www.blockonomics.co/api/tx?txid=$txid&addr=$addr\">$txid</a>";
$blockonomics->updateInvoiceNote($invoiceId, $invoiceNote);

$orderNote .= "Bitcoin transaction id: $txid\r" .
	"Expected amount: $expected BTC\r" .
	"Paid amount: $paid BTC\r" .
	"You can view the transaction at:\r" .
	"https://www.blockonomics.co/api/tx?txid=$txid&addr=$addr";

$blockonomics->updateOrderNote($true_order_id, $orderNote);
$blockonomics->updateOrderInDb($addr, $txid, $status, $value);

/**
 * Validate Callback Invoice ID.
 *
 * Checks invoice ID is a valid invoice number. Note it will count an
 * invoice in any status as valid.
 *
 * Performs a die upon encountering an invalid Invoice ID.
 *
 * Returns a normalised invoice ID.
 *
 * @param int $invoiceId Invoice ID
 * @param string $gatewayName Gateway Name
 */
$invoiceId = checkCbInvoiceID($invoiceId, $gatewayParams['name']);

/**
 * Check Callback Transaction ID.
 *
 * Performs a check for any existing transactions with the same given
 * transaction number.
 *
 * Performs a die upon encountering a duplicate.
 *
 * @param string $transactionId Unique Transaction ID
 */

// If this is test transaction, generate new transaction ID
if($txid == 'WarningThisIsAGeneratedTestPaymentAndNotARealBitcoinTransaction') {
	$txid = 'WarningThisIsATestTransaction_' . md5(uniqid(rand(), true));
}

checkCbTransID($txid);

/**
 * Log Transaction.
 *
 * Add an entry to the Gateway Log for debugging purposes.
 *
 * The debug data can be a string or an array. In the case of an
 * array it will be
 *
 * @param string $gatewayName        Display label
 * @param string|array $debugData    Data to log
 * @param string $transactionStatus  Status
 */
logTransaction($gatewayParams['name'], $_GET, "Successful");

$paymentFee = 0;

/**
 * Add Invoice Payment.
 *
 * Applies a payment transaction entry to the given invoice ID.
 *
 * @param int $invoiceId         Invoice ID
 * @param string $transactionId  Transaction ID
 * @param float $paymentAmount   Amount paid (defaults to full balance)
 * @param float $paymentFee      Payment fee (optional)
 * @param string $gatewayModule  Gateway module name
 */
addInvoicePayment(
	$invoiceId,
	$txid,
	$paymentAmount,
	$paymentFee,
	$gatewayModuleName
);
