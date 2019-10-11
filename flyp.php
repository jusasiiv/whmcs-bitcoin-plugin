<?php
require(dirname(__FILE__) . '/init.php');
require(dirname(__FILE__) . '/includes/gatewayfunctions.php');
require(dirname(__FILE__) . '/includes/invoicefunctions.php');
require_once(dirname(__FILE__) . '/modules/gateways/Blockonomics/Blockonomics.php');

use Blockonomics\Blockonomics;

if ( isset( $_REQUEST['action'] ) ) {
    if( $_REQUEST['action'] == "save_uuid" ) {
        bnomics_save_uuid();
    }
    else if( $_REQUEST['action'] == "send_email" ) {
        bnomics_send_email();
    }
}

function bnomics_save_uuid(){
    $flypAddress    = $_REQUEST['address'];
    $flypUuid    = $_REQUEST['uuid'];
    $blockonomics = new Blockonomics();
    $whmcs_order = $blockonomics->getOrderByAddress($flypAddress);
    $whmcs_order_id = $whmcs_order["order_id"];
    $blockonomics->updateFlypIdInDb($whmcs_order_id, $flypUuid);
    $invoiceNote = "Flyp UUID: ".$flypUuid;
    $blockonomics->updateInvoiceNote($whmcs_invoice_id, $invoiceNote);
    print(json_encode($whmcs_order));
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