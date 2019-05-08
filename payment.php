<?php

require_once(dirname(__FILE__) . '/modules/gateways/Blockonomics/Blockonomics.php');

use WHMCS\ClientArea;
use WHMCS\Database\Capsule;
use Blockonomics\Blockonomics;

define('CLIENTAREA', true);
require 'init.php';

// Init Blockonomics class
$blockonomics = new Blockonomics();

$ca = new ClientArea();

$ca->setPageTitle('Bitcoin Payment');

$ca->addToBreadCrumb('index.php', Lang::trans('globalsystemname'));
$ca->addToBreadCrumb('payment.php', 'Bitcoin Payment');

$ca->initPage();

/*
 * SET POST PARAMETERS TO VARIABLES AND CHECK IF THEY EXIST
 */
$uuid = $_REQUEST['uuid'];
$fiat_amount = $_POST['price'];
$currency = $_POST['currency'];
$order_id = $_POST['order_id'];

$system_url = $blockonomics->getSystemUrl();
$ca->assign('system_url', $system_url);

/**
 * Try to generate a new Bitcoin address for payment.
 * Set error for template if not able
 * Return BTC address if success, null if error
 */
function generate_address($blockonomics, $ca) {
	$response_obj = $blockonomics->getNewBitcoinAddress();
	$error = $blockonomics->checkForErrors($response_obj);
	if ($error) {
		$error = True;
		$ca->assign('error', $error);
		return null;
	} else {
		$btc_address = $response_obj->address;
		$ca->assign('btc_address', $btc_address);
		return $btc_address;
	}
}

if ($uuid) {
	$existing_order = $blockonomics->getOrderByUuid($uuid);
	$ca->assign('altcoins', 1);
	$ca->assign('flyp_id', $uuid);
	// No order exists, exit
	if(is_null($existing_order['order_id'])) {
		exit;
	} else {
		$order_id = $existing_order['order_id'];
		$ca->assign('order_id', $order_id);

		$fiat_amount = $existing_order['value'];
		$ca->assign('fiat_amount', $fiat_amount);

		$btc_address = $existing_order['address'];
		$ca->assign('btc_address', $btc_address);

		$btc_amount = $existing_order['bits'];
		$ca->assign('btc_amount', $btc_amount / 1.0e8);
	}

	# Define the template filename to be used without the .tpl extension
	$ca->setTemplate('../blockonomics/payment');

	$ca->output();
}else{
	if(!$fiat_amount || !$currency || !$order_id) {
		echo "<b>Error: Failed to fetch order data.</b> <br> 
					Note to admin: Please check that your System URL is configured correctly.
					If you are using SSL, verify that System URL is set to use HTTPS and not HTTP. <br>
					To configure System URL, please go to WHMCS admin > Setup > General Settings > General";
		exit;
	}

	// Check if Altcoins are enabled
	$altcoins = $blockonomics->getAltcoins();
	$ca->assign('altcoins', $altcoins);

	$ca->assign('fiat_amount', $fiat_amount);
	$ca->assign('currency', $currency);
	$ca->assign('order_id', $order_id);

	$time_period_from_db = $blockonomics->getTimePeriod();
	$time_period = isset($time_period_from_db) ? $time_period_from_db : '10';
	$ca->assign('time_period', $time_period);

	$existing_order = $blockonomics->getOrderById($order_id);

	// No order exists, create new and add to db
	if(is_null($existing_order['order_id'])) {

			$btc_address = generate_address($blockonomics, $ca);

			if ($btc_address) {
				/*
				 * PRICE GENERATION
				 */
				$btc_amount = $blockonomics->getBitcoinAmount($fiat_amount, $currency);
				$ca->assign('btc_amount', $btc_amount / 1.0e8);

				/*
				 * ADD ORDER TO DB
				 */
				$blockonomics->insertOrderToDb($order_id, $btc_address, $fiat_amount, $btc_amount);
			}
		
	} else {

		/**
		 * Check what is the current status of payment to determine what to do
		 * Status -1: Order exists but has not received any payment, show payment screen
		 * Status 0 or 1: Order has a pending payment, show info about pending transaction and disallow payment
		 * Status 2: Order has received an underpayment, generate new address
		 */
		if($existing_order['status'] == -1) {
			$btc_address = $existing_order['address'];
		} elseif ($existing_order['status'] == 0 || $existing_order['status'] == 1) {
			$ca->assign('pending', true);
			$ca->assign('txid', $existing_order['txid']);
		} elseif ($existing_order['status'] == 2) {
			$btc_address = generate_address($blockonomics, $ca);
			$blockonomics->updateOrderAddress($order_id, $btc_address);
		}
		
		// Only set BTC address to template if it has been generated successfully
		if ($btc_address) {
			$ca->assign('btc_address', $btc_address);
		}
		$btc_amount = $blockonomics->getBitcoinAmount($fiat_amount, $currency);
		$ca->assign('btc_amount', $btc_amount / 1.0e8);
		$blockonomics->updateOrderExpected($order_id, $btc_amount, $fiat_amount);
	}

	# Define the template filename to be used without the .tpl extension
	$ca->setTemplate('../blockonomics/payment');

	$ca->output();
}
?>