var btcAddress;
var btcAmount;

window.onload = function() {

	var totalProgress = 100;
  var totalTime = 10*60; //10m
  var currentTime = 10*60; //10m
  var currentProgress = 100;

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
	btcAddress = btcAddressDiv.dataset.address;

	var btcAmountDiv = document.getElementById("btc-amount");
	btcAmount = btcAmountDiv.dataset.amount;

	// Seconds now from epoch
	var d = new Date();
	var seconds = Math.round(d.getTime() / 1000);

	//Websocket
	var ws = new ReconnectingWebSocket("wss://www.blockonomics.co/payment/" + btcAddress + "?timestamp=" + seconds);

	redirUrl = systemUrl + 'viewinvoice.php?id=' + orderId + '&paymentsuccess=true';

	ws.onmessage = function (evt) {
		ws.close();
		window.location.href = redirUrl;
	}

	var timeDiv = document.getElementById("time-left");
	var minutesLeft = document.getElementById("time-left-minutes");
	var date = new Date(null);

	setInterval( function() { 
		currentTime = currentTime - 1;
		currentProgress = Math.floor(currentTime*totalProgress/totalTime);
		timeDiv.style.width = "" + currentProgress + "%";

		var result = new Date(currentTime * 1000).toISOString().substr(14, 5);
		minutesLeft.innerHTML = result;

		if (currentTime == 0) {
			document.getElementById("paywrapper").innerHTML = "<p>Payment expired, please place a new order</p>"
		}
	}, 1000);
}

function pay_altcoins() {
	document.getElementById("altcoin-waiting").style.display = "block";
	document.getElementById("paywrapper").style.display = "none";
	var altcoin_waiting = true;
	url = "https://shapeshift.io/shifty.html?destination=" + btcAddress + "&amount=" + btcAmount + "&output=BTC";
	window.open(url, '1418115287605','width=700,height=500,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=0,left=0,top=0');
}

function disableAltcoin() {
	document.getElementById("altcoin-waiting").style.display = "none";
	document.getElementById("paywrapper").style.display = "block";
}