<?php

use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require __DIR__ . '/init.php';

$ca = new ClientArea();

$ca->setPageTitle('Blockonomics Bitcoin Payment');

$ca->addToBreadCrumb('index.php', Lang::trans('globalsystemname'));
$ca->addToBreadCrumb('payment.php', 'Blockonomics Bitcoin Payment');

$ca->initPage();

//$ca->requireLogin(); // Uncomment this line to require a login to access this page

// To assign variables to the template system use the following syntax.
// These can then be referenced using {$variablename} in the template.

$ca->assign('btc_amount', 0.0001);

// Check login status
if ($ca->isLoggedIn()) {

	/**
	 * User is logged in - put any code you like here
	 *
	 * Here's an example to get the currently logged in clients first name
	 */

	$clientName = Capsule::table('tblclients')
		->where('id', '=', $ca->getUserID())->pluck('firstname');
		// 'pluck' was renamed within WHMCS 7.0.  Replace it with 'value' instead.
		// ->where('id', '=', $ca->getUserID())->value('firstname');
	$ca->assign('clientname', $clientName);

} else {

	// User is not logged in
	$ca->assign('clientname', 'Random User');

}

/*
 * ADDRESS GENERATION
 */
$api_key = Capsule::table('tblpaymentgateways')
			->where('gateway', 'blockonomics')
			->where('setting', 'ApiKey')
			->value('value');

$secret = Capsule::table('tblpaymentgateways')
			->where('gateway', 'blockonomics')
			->where('setting', 'ApiSecret')
			->value('value');

$options = [
	'http' => [
		'header'  => 'Authorization: Bearer '. $api_key,
		'method'  => 'POST',
		'content' => ''
	]
];

try {
	$context = stream_context_create($options);
	$separator = '?reset=1&';
	$contents = file_get_contents('https://www.blockonomics.co/api/new_address'.$separator."match_callback=$secret", false, $context);
	$new_address = json_decode($contents);
} catch (\Exception $e) {
	echo "Error getting new address from Blockonomics!";;
}

$btc_address = $new_address->address;

$ca->assign('btc_address', $btc_address);
$ca->assign('secret', $secret);


/**
 * Set a context for sidebars
 *
 * @link http://docs.whmcs.com/Editing_Client_Area_Menus#Context
 */
Menu::addContext();

/**
 * Setup the primary and secondary sidebars
 *
 * @link http://docs.whmcs.com/Editing_Client_Area_Menus#Context
 */
//Menu::primarySidebar('announcementList');
//Menu::secondarySidebar('announcementList');

# Define the template filename to be used without the .tpl extension

$ca->setTemplate('payment');

$ca->output();

?>