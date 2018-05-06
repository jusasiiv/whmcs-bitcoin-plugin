<link rel="stylesheet" type="text/css" href="css/style.css">
<script type="text/javascript" src="js/qrcode.min.js"></script>
<script type="text/javascript" src="js/reconnecting-websocket.min.js"></script>
<script type="text/javascript" src="js/payment.js"></script>

{if not $error}

<div id="btc-href" data-href="bitcoin:{$btc_address}?amount={$btc_amount}"></div>
<div id="btc-address" data-address="{$btc_address}"></div>
<div id="btc-amount" data-amount="{$btc_amount}"></div>
<div id="system-url" data-url="{$system_url}" data-orderid="{$order_id}"></div>

<div id="paywrapper" class="payment-wrapper center">
	
	<h3>Order# {$order_id}</h3>
	<div class="clear"></div>

	<div class="qr-code-wrapper">
		<a id="btc-address-a" href="bitcoin:{$btc_address}?amount={$btc_amount}">
			<div id="qrcode"></div>
		</a>
		<p>Click on the QR code open in the wallet</p>

		{if $altcoins}
		<div class="bnomics-altcoin-pane">
			<a onclick="pay_altcoins()" href="#"><img style="margin: auto;" src="https://shapeshift.io/images/shifty/small_dark_altcoins.png" class="ss-button"></a>
		</div>
		{/if}

	</div>

	<div class="info center">
		
		<p>To confirm your order, please send the amount of <span>BTC</span> to the <b>given address</b></p>
		<h2>{$btc_amount} BTC</h2>
		<hr>
		<p>&asymp; {$fiat_amount} {$currency}</p>
		<div class="address"><b>{$btc_address}</b></div>

    <div class="time-wrapper">
       <div id="time-left"></div>
		</div>

		<p><span id="time-left-minutes"></span> min left to pay your order</p>
		<p class="powered">Powered by Blockonomics</p>

	</div>
</div>

<div id="altcoin-waiting" class="row">
	<div class="col-xs-12 altcoin-waiting">
		<h3>Waiting for BTC payment from shapeshift altcoin conversion</h3>
		<div class="bnomics-spinner"></div>
		<h3><a href="#" onclick="disableAltcoin()">Click here</a> to cancel and go back</h3>
	</div>
</div>

<div class="clear"></div>

{else}

<div id="address-error">
    <h3>Could not generate new bitcoin address.</h3>
    <i>Note to webmaster: {$error} </i>
    <i>If issue persists, log a ticket on <a href="https://blockonomics.freshdesk.com/support/solutions/articles/33000215104-troubleshooting-unable-to-generate-new-address" target="_blank">http://blockonomics.freshdesk.com/</a></i>
</div>

{/if}