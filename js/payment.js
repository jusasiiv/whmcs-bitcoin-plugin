window.onload = function() {

	var totalProgress = 100;
    var totalTime = 10*6; //10m
    var currentTime = 10*6; //10m
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
	var btcAddress = btcAddressDiv.dataset.address;

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
		console.log(result);
		minutesLeft.innerHTML = result;

		if (currentTime == 0) {
			document.getElementById("paywrapper").innerHTML = "<p>Payment expired, please place a new order</p>"
		}

	}, 1000);
}