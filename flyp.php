<?php
require(dirname(__FILE__) . '/init.php');
require(dirname(__FILE__) . '/includes/gatewayfunctions.php');
require(dirname(__FILE__) . '/includes/invoicefunctions.php');
require_once(dirname(__FILE__) . '/modules/gateways/Blockonomics/Flyp.php');
require_once(dirname(__FILE__) . '/modules/gateways/Blockonomics/Blockonomics.php');

use Blockonomics\FlypMe;
use Blockonomics\Blockonomics;

if ( isset( $_REQUEST['action'] ) ) {
	if( $_REQUEST['action'] == "fetch_limit" ) {
		bnomics_fetch_limit();
	}
	else if( $_REQUEST['action'] == "create_order" ) {
		bnomics_create_order();
	}
	else if( $_REQUEST['action'] == "check_order" ) {
		bnomics_check_order();
	}
	else if( $_REQUEST['action'] == "info_order" ) {
		bnomics_info_order();
	}
	else if( $_REQUEST['action'] == "send_email" ) {
		bnomics_send_email();
	}
}

function bnomics_fetch_limit(){
    require_once(dirname(__FILE__) . '/modules/gateways/Blockonomics/Flyp.php');
    $flypFrom           = $_REQUEST['altcoin'];
    $flypTo             = "BTC";
    $flypme = new FlypMe();
    $limits = $flypme->orderLimits($flypFrom, $flypTo);
    if(isset($limits)){
        print(json_encode($limits));
    }
    die();
}

function bnomics_create_order(){
    $flypFrom           = $_REQUEST['altcoin'];
    $flypAmount         = $_REQUEST['amount'];
    $flypDestination    = $_REQUEST['address'];
    $flypTo             = "BTC";
    $whmcs_invoice_id = $_REQUEST['order_id'];
    $flypme = new FlypMe();
    $order = $flypme->orderNew($flypFrom, $flypTo, $flypAmount, $flypDestination);
    if(isset($order->order->uuid)){
    	$blockonomics = new Blockonomics();
    	$whmcs_order_id = $blockonomics->getOrderIdByInvoiceId($whmcs_invoice_id);
    	$blockonomics->updateFlypIdInDb($whmcs_order_id, $order->order->uuid);
        $actual_link = $blockonomics->getSystemUrl();
        $invoiceNote = "<b>Waiting for Confirmation on $flypFrom network</b>\r\r" .
            "Flyp UUID:\r" .
            "<a target=\"_blank\" href=\"" . $actual_link . "payment.php?uuid=".$order->order->uuid. "\">" .$order->order->uuid. "</a>";
        $blockonomics->updateInvoiceNote($whmcs_invoice_id, $invoiceNote);
        $order = $flypme->orderAccept($order->order->uuid);
        if(isset($order->deposit_address)){
            print(json_encode($order));
        }
    }
    die();
}

function bnomics_check_order(){
    require_once(dirname(__FILE__) . '/modules/gateways/Blockonomics/Flyp.php');
    $flypID             = $_REQUEST['uuid'];
    $flypme = new FlypMe();
    $order = $flypme->orderCheck($flypID);
    if(isset($order)){
        print(json_encode($order));
    }
    die();
}

function bnomics_info_order(){
    require_once(dirname(__FILE__) . '/modules/gateways/Blockonomics/Flyp.php');
    $flypID             = $_REQUEST['uuid'];
    $flypme = new FlypMe();
    $order = $flypme->orderInfo($flypID);
    if(isset($order)){
        print(json_encode($order));
    }
    die();
}

function bnomics_send_email(){
    $blockonomics = new Blockonomics();
    $actual_link = $blockonomics->getSystemUrl();
    $flypID                 = $_REQUEST['uuid'];
    $flypCoin               = $_REQUEST['coin'];
    $flypSymbol             = $_REQUEST['symbol'];
    $whmcs_invoice_id       = $_REQUEST['order_id'];
    $subject = $flypCoin . ' Payment Recieved';
	$command = 'SendEmail';
	$postData = array(
	    'messagename' => 'Altcoin Payment Email',
	    'id' => $whmcs_invoice_id,
	    'customtype' => 'invoice',
	    'customsubject' => $subject,
	    'custommessage' => '<p>Your payment has been received. It will take a while for the network to confirm your order.</p>
	    					<p>To view your payment status, copy and use the link below.</p>
	     					<a href="{$link}">{$link}</a>',
	    'customvars' => base64_encode(serialize(array("link"=>$actual_link . 'payment.php?uuid='.$flypID))),
	);
	// For Versions before WHMCS 7.2
	//$adminUsername = 'ADMIN_USERNAME'; // Enter Admin user name
	//$results = localAPI($command, $postData, $adminUsername);
	$results = localAPI($command, $postData);

	print_r(json_encode($results));
    die();
}

?>