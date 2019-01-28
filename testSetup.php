<?php

require_once(dirname(__FILE__) . '/modules/gateways/Blockonomics/Blockonomics.php');
require_once 'init.php';

use WHMCS\ClientArea;
use WHMCS\Database\Capsule;
use Blockonomics\Blockonomics;


$blockonomics = new Blockonomics();

$response->error = false;

$error = $blockonomics->testSetup();

if(isset($error) && $error != '') {
  $response->error = true;
  $response->errorStr = $error;
}

$responseJSON = json_encode($response);

echo $responseJSON;