<script type="text/javascript" src="js/qrcode.min.js"></script>
<script type="text/javascript" src="js/reconnecting-websocket.min.js"></script>
<script type="text/javascript" src="js/payment.js"></script>

<div id="btc-href" data-href="bitcoin:{$btc_address}?amount={$btc_amount}"></div>
<div id="btc-address" data-address="{$btc_address}"></div>

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

<h3>Address = {$btc_address}</h3>