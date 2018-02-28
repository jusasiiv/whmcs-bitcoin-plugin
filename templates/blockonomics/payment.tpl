<link rel="stylesheet" type="text/css" href="css/style.css">
<script type="text/javascript" src="js/qrcode.min.js"></script>
<script type="text/javascript" src="js/reconnecting-websocket.min.js"></script>
<script type="text/javascript" src="js/payment.js"></script>

{if $btc_address}

<div id="btc-href" data-href="bitcoin:{$btc_address}?amount={$btc_amount}"></div>
<div id="btc-address" data-address="{$btc_address}"></div>
<div id="system-url" data-url="{$system_url}" data-orderid="{$order_id}"></div>

<h1>Order# {$order_id}</h1>
<h2>To pay, send exact amount of BTC to the given address</h2>

<div id="address-div">
	<h4>Bitcoin address</h4>
	<a id="btc-address-a" href="bitcoin:{$btc_address}?amount={$btc_amount}">
		<div id="qrcode"></div>
	</a>
	<h4>Click on the qr code above to open in wallet</h4>
</div>

<div id="amount-div">
		<h4>Amount</h4>
		<h5>{$btc_amount} BTC ⇌ {$fiat_amount} {$currency}</h5>
</div>

<div class="clear"></div>

<label id="btc-address-label">{$btc_address}</label>

<h4>Powered by blockonomics</h4>

{else}

<div id="address-error">
    <h3>Could not generate new bitcoin address.</h3>
    <i>Note to webmaster: Your webhost is blocking outgoing HTTPS connections. Blockonomics requires an outgoing HTTPS (port 443) to generate new address. Check with your webhost to allow this. Also make sure that <a href="https://www.crybit.com/enable-allow_url_fopen/" target="_blank">allow_url_fopen is On</a> on your server. If issue persists, log a ticket on <a href="https://blockonomics.freshdesk.com/support/solutions/articles/33000215104-troubleshooting-unable-to-generate-new-address" target="_blank">http://blockonomics.freshdesk.com/</a></i>
</div>

{/if}