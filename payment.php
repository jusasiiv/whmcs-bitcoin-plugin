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

$ca->setPageTitle('Blockonomics Bitcoin Payment');

$ca->addToBreadCrumb('index.php', Lang::trans('globalsystemname'));
$ca->addToBreadCrumb('payment.php', 'Blockonomics Bitcoin Payment');

$ca->initPage();

/***********************************************
 * SET POST PARAMETERS TO VARIABLES AND CHECK IF THEY EXIST
 */
$fiat_amount = $_POST['price'];
$currency = $_POST['currency'];
$order_id = $_POST['order_id'];

if(!$fiat_amount || !$currency || !$order_id) {
	exit;
}

/***********************************************
 * ADDRESS GENERATION
 */
$btc_address = $blockonomics->getNewBitcoinAddress();

$ca->assign('btc_address', $btc_address);

/************************************************/

/***********************************************
 * PRICE GENERATION
 */
$options = [ 'http' => [ 'method'  => 'GET'] ];
$context = stream_context_create($options);
$contents = file_get_contents('https://www.blockonomics.co/api/price' . "?currency=$currency", false, $context);
$price = json_decode($contents);

$btc_amount = intval(1.0e8 * $fiat_amount/$price->price) / 1.0e8;

$ca->assign('btc_amount', $btc_amount);
$ca->assign('fiat_amount', $fiat_amount);
$ca->assign('currency', $currency);
$ca->assign('order_id', $order_id);

/************************************************/

/**
 * Set a context for sidebars
 *
 * @link http://docs.whmcs.com/Editing_Client_Area_Menus#Context
 */
Menu::addContext();


# Define the template filename to be used without the .tpl extension
$ca->setTemplate('payment');

$ca->output();

?>