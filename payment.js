
window.onload = function() {

var btcHrefDiv = document.getElementById("btc-href");
var btcHref = btcHrefDiv.dataset.href;

new QRCode(document.getElementById("qrcode"), {
	text: btcHref,
	width: 128,
	height: 128,
	correctLevel : QRCode.CorrectLevel.M
});

}