<script type="text/javascript" src="qrcode.min.js"></script>
<script type="text/javascript" src="payment.js"></script>

<div id="btc-href" data-href="bitcoin:{$btc_address}?amount={$btc_amount}"></div>

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
		<h5>{$btc_amount} BTC ⇌ {$fiat_amount} USD</h5>
</div>

<h3>Address = {$btc_address}</h3>