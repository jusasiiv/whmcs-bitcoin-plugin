<?php

require_once(dirname(__FILE__) . '/modules/gateways/Blockonomics/Blockonomics.php');
require __DIR__ . '/init.php';

use WHMCS\ClientArea;
use WHMCS\Database\Capsule;
use Blockonomics\Blockonomics;

define('CLIENTAREA', true);

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
$fiat_amount = $_POST['price'];
$currency = $_POST['currency'];
$order_id = $_POST['order_id'];
$system_url = $blockonomics->getSystemUrl();

if(!$fiat_amount || !$currency || !$order_id) {
	exit;
}

$ca->assign('fiat_amount', $fiat_amount);
$ca->assign('currency', $currency);
$ca->assign('order_id', $order_id);
$ca->assign('system_url', $system_url);

/*
 * ADDRESS GENERATION
 */
$response_obj = $blockonomics->getNewBitcoinAddress();
$error_str = $blockonomics->checkForErrors($response_obj);

if ($error_str) {

	$ca->assign('error', $error_str);

} else {

	$btc_address = $responseObj->address;
	$ca->assign('btc_address', $btc_address);

	/*
	 * PRICE GENERATION
	 */
	$btc_amount = $blockonomics->getBitcoinAmount($fiat_amount, $currency);

	$ca->assign('btc_amount', $btc_amount / 1.0e8);

	/*
	 * ÁDD ORDER TO DB
	 */
	$blockonomics->insertOrderToDb($order_id, $btc_address, $fiat_amount, $btc_amount);

	/*
	 * UPDATE ORDER STATUS
	 */
	$true_order_id = $blockonomics->getOrderIdByInvoiceId($order_id);
	$order_status = 'Waiting for Bitcoin Confirmation';
	$blockonomics->updateOrderStatus($true_order_id, $order_status);

}

/**
 * Set a context for sidebars
 *
 * @link http://docs.whmcs.com/Editing_Client_Area_Menus#Context
 */
Menu::addContext();


# Define the template filename to be used without the .tpl extension
$ca->setTemplate('../blockonomics/payment');

$ca->output();

?>