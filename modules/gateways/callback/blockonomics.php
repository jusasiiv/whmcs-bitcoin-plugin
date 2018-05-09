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
$transactionId = $order['id'];
$bits = $order['bits'];

if($status == 0) {

	$orderNote = "Bitcoin transaction id: $txid \r" .
		"Expected amount: $bits \r" .
		"Paid amount: $value \r" .
		"You can view the transaction at:\r" .
		"https://www.blockonomics.co/api/tx?txid=$txid&addr=$addr";


	$invoiceNote = "<b>Waiting for confirmation</b>\r\r" .
		"Bitcoin transaction id:\r" .
		"<a target=\"_blank\" href=\"https://www.blockonomics.co/api/tx?txid=$txid&addr=$addr\">$txid</a>";

	$blockonomics->updateOrderInDb($addr, $txid, $status, $value);
	$true_order_id = $blockonomics->getOrderIdByInvoiceId($invoiceId);
	$blockonomics->updateOrderNote($true_order_id, $orderNote);
	$blockonomics->updateInvoiceNote($invoiceId, $invoiceNote);
	$blockonomics->updateInvoiceStatus($invoiceId, "Payment Pending");

	die();
}

if($status != 2) {
	die();
}

$true_order_id = $blockonomics->getOrderIdByInvoiceId($invoiceId);

if($value < $bits) {
	$orderNote = "NOTICE! PAID AMOUNT WAS LESS THAN EXPECTED \r" .
		"Bitcoin transaction id: $txid \r" .
		"Expected amount: $bits \r" .
		"Paid amount: $value \r" .
		"You can view the transaction at:\r" .
		"https://www.blockonomics.co/api/tx?txid=$txid&addr=$addr";

	$blockonomics->updateOrderNote($true_order_id, $orderNote);
}

$invoiceNote = "Bitcoin transaction id:\r" .
	"<a target=\"_blank\" href=\"https://www.blockonomics.co/api/tx?txid=$txid&addr=$addr\">$txid</a>";

$blockonomics->updateInvoiceNote($invoiceId, $invoiceNote);
$order_status = 'Active';
$blockonomics->updateOrderStatus($true_order_id, $order_status);

$blockonomics->updateOrderInDb($addr, $txid, $status, $value);

$transaction_unique_id = 'blockonomics_' . $transactionId;

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

checkCbTransID($transaction_unique_id);
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
	$transaction_unique_id,
	$paymentAmount,
	$paymentFee,
	$gatewayModuleName
);