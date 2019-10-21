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
    $whmcs_invoice_id = $_REQUEST['order_id'];
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
    $order_id = $_REQUEST['order_id'];
    $uuid = $_REQUEST['order_uuid'];
    $order_coin = $_REQUEST['order_coin'];
    $refund_address = $_REQUEST['refund_address'];
    $subject = $order_coin . ' ' . 'Refund';
    $command = 'SendEmail';
    $postData = array(
        'messagename' => 'Altcoin Payment Email',
        'id' => $order_id,
        'customtype' => 'invoice',
        'customsubject' => $subject,
        'custommessage' => '<p>Your refund details have been submitted. The refund will be automatically sent to<br>
                            <b>{$refund_address}</b><br>
                            If you don&#39;t get refunded in a few hours, contact <a href=\'mailto:support@flyp.me\'>support@flyp.me</a> with the following uuid:<br>
                            <b>{$uuid}</b></p>',
        'customvars' => base64_encode(serialize(array("refund_address"=>$refund_address,"uuid"=>$uuid))),
    );
    // For Versions before WHMCS 7.2
    //$adminUsername = 'ADMIN_USERNAME'; // Enter Admin user name
    //$results = localAPI($command, $postData, $adminUsername);
    $results = localAPI($command, $postData);

    print_r(json_encode($results));
    die();
}

?>