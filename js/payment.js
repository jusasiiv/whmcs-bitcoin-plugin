window.onload = function() {

	var systemUrlDiv = document.getElementById("system-url");
	var systemUrl = systemUrlDiv.dataset.url;
	var orderId = systemUrlDiv.dataset.orderid;

	var btcHrefDiv = document.getElementById("btc-href");
	var btcHref = btcHrefDiv.dataset.href;

	new QRCode(document.getElementById("qrcode"), {
		text: btcHref,
		width: 128,
		height: 128,
		correctLevel : QRCode.CorrectLevel.M
	});

	var btcAddressDiv = document.getElementById("btc-address");
	var btcAddress = btcAddressDiv.dataset.address;

	// Seconds now from epoch
	var d = new Date();
	var seconds = Math.round(d.getTime() / 1000);

	//Websocket
	var ws = new ReconnectingWebSocket("wss://www.blockonomics.co/payment/" + btcAddress + "?timestamp=" + seconds);

	redirUrl = systemUrl + 'viewinvoice.php?id=' + orderId + '&paymentsuccess=true';

	ws.onmessage = function (evt) {
		ws.close();
		redirectToURL(redirUrl);
	}

}