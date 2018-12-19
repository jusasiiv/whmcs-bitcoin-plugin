var btcAddress;
var btcAmount;

window.onload = function() {
	if(document.getElementById("flyp-id")){
		var flypIdDiv = document.getElementById("flyp-id");
		flypId = flypIdDiv.dataset.uuid;
		infoOrder(flypId);
	}
	else{
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

		redirUrl = systemUrl + '/viewinvoice.php?id=' + orderId + '&paymentsuccess=true';

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
}

var interval;
var interval_check;
var email = 0;
function checkOrder(uuid){
	var alt_coin = document.getElementById("altcoin_select");
	var check = new XMLHttpRequest();
	check.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			var response = JSON.parse(this.responseText);
			if(response['status'] == "WAITING_FOR_DEPOSIT"){
	          set_alt_status(0);
	        }
	        if(response['status'] == "DEPOSIT_RECEIVED"){
	          if(email == 1){
	          	var systemUrlDiv = document.getElementById("system-url");
				var orderId = systemUrlDiv.dataset.orderid;
	            var send_email = new XMLHttpRequest();
				send_email.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						var response = JSON.parse(this.responseText);
					}
				};
				send_email.open("GET", "flyp.php?action=send_email&uuid="+uuid+"&coin="+alt_coin[alt_coin.selectedIndex].value+"&symbol="+alt_coin[alt_coin.selectedIndex].id+"&order_id="+orderId, true);
				send_email.send();
				email = 0;
	          }
	          set_alt_status(1);
	        }
	        if(response['status'] == "DEPOSIT_CONFIRMED"){
	          set_alt_status(2);
	        }
	        if(response['status'] == "EXECUTED"){
	          set_alt_status(3);
	          clearInterval(interval_check);
	        }
	        if(response['status'] == "REFUNDED"){
	          set_alt_status(4);
	          clearInterval(interval_check);
	        }
	        if(response['status'] == "CANCELED"){
	          set_alt_status(5);
	          clearInterval(interval_check);
	        }
	        if(response['status'] == "EXPIRED"){
	          set_alt_status(6);
	          clearInterval(interval_check);
	        }
		}
	};
	check.open("GET", "flyp.php?action=check_order&uuid="+uuid, true);
	check.send();
}

function infoOrder(uuid){
	interval_check = setInterval(function(response) {
		checkOrder(uuid);
	}, 10000);
	var info = new XMLHttpRequest();
	info.onreadystatechange = function() {
		if (this.readyState == 4 && this.status == 200) {
			var response = JSON.parse(this.responseText);
			if(response['order']['from_currency'] == 'ETH'){
			  Array.from(document.getElementsByClassName("alt-explorer")).forEach(
				function(element, index, array) {
				    element.href = 'https://etherscan.io/address/' + response['deposit_address'];
			    }
			  );
				Array.from(document.getElementsByClassName("cf")).forEach(
				function(element, index, array) {
				    element.classList.add('cf-eth');
			    });
			}
			else if(response['order']['from_currency'] == 'LTC'){
			  Array.from(document.getElementsByClassName("alt-explorer")).forEach(
				function(element, index, array) {
				    element.href = 'https://chainz.cryptoid.info/ltc/address.dws?' + response['deposit_address'];
			  });
				Array.from(document.getElementsByClassName("cf")).forEach(
				function(element, index, array) {
				    element.classList.add('cf-ltc');
			    });
			}
			Array.from(document.getElementsByClassName("alt-coin")).forEach(
				function(element, index, array) {
				    element.innerHTML = response['order']['from_currency'];
			    }
			);
			if(response['status'] == "WAITING_FOR_DEPOSIT"){
	          set_alt_status(0);
	        }
	        if(response['status'] == "DEPOSIT_RECEIVED"){
	          set_alt_status(1);
	        }
	        if(response['status'] == "DEPOSIT_CONFIRMED"){
	          set_alt_status(2);
	        }
	        if(response['status'] == "EXECUTED"){
	          set_alt_status(3);
	          clearInterval(interval_check);
	        }
	        if(response['status'] == "REFUNDED"){
	          set_alt_status(4);
	          clearInterval(interval_check);
	        }
	        if(response['status'] == "CANCELED"){
	          set_alt_status(5);
	          clearInterval(interval_check);
	        }
	        if(response['status'] == "EXPIRED"){
	          set_alt_status(6);
	          clearInterval(interval_check);
	        }
		}
	};
	info.open("GET", "flyp.php?action=info_order&uuid="+uuid, true);
	info.send();
}

function pay_altcoins() {
	document.getElementById("altcoin-waiting").style.display = "block";
	document.getElementById("paywrapper").style.display = "none";
	var systemUrlDiv = document.getElementById("system-url");
	var orderId = systemUrlDiv.dataset.orderid;
	var altcoin_waiting = true;
	email = 1;
	document.getElementById("alt-qrcode").innerHTML = "";
	var alt_coin = document.getElementById("altcoin_select");
	( function( promises ){
	    return new Promise( ( resolve, reject ) => {
	        Promise.all( promises )
	            .then( values => {
	            	var alt_limits = JSON.parse(values[0]);
					var alt_minimum = alt_limits['min'];
					var alt_maximum = alt_limits['max'];
					if(btcAmount >= alt_minimum && btcAmount <= alt_maximum){
	                	var response = JSON.parse(values[1]);
	                	document.getElementById("alt-address").value = response['deposit_address'];
						if(alt_coin[alt_coin.selectedIndex].value == 'ETH'){
						  Array.from(document.getElementsByClassName("alt-explorer")).forEach(
							function(element, index, array) {
							    element.href = 'https://etherscan.io/address/' + response['deposit_address'];
						    }
						  );
						  Array.from(document.getElementsByClassName("cf")).forEach(
							function(element, index, array) {
							    element.classList.add('cf-eth');
						  });
						}else if(alt_coin[alt_coin.selectedIndex].value == 'LTC'){
						  Array.from(document.getElementsByClassName("alt-explorer")).forEach(
							function(element, index, array) {
							    element.href = 'https://chainz.cryptoid.info/ltc/address.dws?' + response['deposit_address'];
						  });
						  Array.from(document.getElementsByClassName("cf")).forEach(
							function(element, index, array) {
							    element.classList.add('cf-ltc');
						  });
						}
						document.getElementById("alt-amount").innerHTML = response['order']['invoiced_amount'];
						document.getElementById("alt-symbol").innerHTML = response['order']['from_currency'];
						var alt_qr_code = alt_coin[alt_coin.selectedIndex].id+":"+ response['deposit_address'] +"?amount="+ response['order']['invoiced_amount'] +"&value="+ response['order']['invoiced_amount'];
						document.getElementById("alt-qrcode").href = alt_qr_code;
						Array.from(document.getElementsByClassName("alt-coin")).forEach(
							function(element, index, array) {
							    element.innerHTML = alt_coin[alt_coin.selectedIndex].innerHTML;
						    }
						);
						new QRCode(document.getElementById("alt-qrcode"), {
							text: alt_qr_code,
							width: 128,
							height: 128,
							correctLevel : QRCode.CorrectLevel.M
						});
						var uuid = response['order']['uuid'];
						Array.from(document.getElementsByClassName("alt-uuid")).forEach(
							function(element, index, array) {
							    element.innerHTML = uuid;
						    }
						);
						interval_check = setInterval(function(response) {
						  checkOrder(uuid);
						}, 10000);
						var altMinutesLeft = document.getElementById("alt-time-left-minutes");
						var altTotalProgress = 100;
						var altTotalTime = 20 * 60; //20m
						var altCurrentTime = 20 * 60; //20m
						var altCurrentProgress = 100;
						var altTimeDiv = document.getElementById("alt-time-left");
						interval = setInterval( function() { 
							altCurrentTime = altCurrentTime - 1;
							altCurrentProgress = Math.floor(altCurrentTime*altTotalProgress/altTotalTime);
							altTimeDiv.style.width = "" + altCurrentProgress + "%";

							var result = new Date(altCurrentTime * 1000).toISOString().substr(14, 5);
							altMinutesLeft.innerHTML = result;

							if (altCurrentTime <= 0) {
								document.getElementById("alt-time-wrapper").style.display = "none";
								document.getElementById("alt-time-left-minutes").style.display = "none";
							}
						}, 1000);
					}else{
						set_alt_status(7);
					}
	                resolve( values );
	            })
	            .catch( err => {
	                console.dir( err );
	                throw err;
	            });
	    });
	})([ 
	    new Promise( ( resolve, reject ) => {
	    		var limits = new XMLHttpRequest();
				limits.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						resolve( this.responseText );
					}
				};
				limits.open("GET", "flyp.php?action=fetch_limit&altcoin="+document.getElementById("altcoin_select").value, true);
				limits.send();
	    }),
	    new Promise( ( resolve, reject ) => {
	        	var order = new XMLHttpRequest();
				order.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						resolve( this.responseText );
					}
				};
				order.open("GET", "flyp.php?action=create_order&altcoin="+document.getElementById("altcoin_select").value+"&amount="+btcAmount+"&address="+btcAddress+"&order_id="+orderId, true);
				order.send();
	    })
	 ]);
}

function altcoin_select() {
	var element = document.getElementById("alt_selected");
	var selected_altcoin = document.getElementById("altcoin_select");
	element.innerHTML = selected_altcoin.value;
	var pay_with_icon = document.getElementById("pay_with_icon");
	if(selected_altcoin.value == 'ETH'){
		pay_with_icon.classList.add('cf-eth');
		pay_with_icon.classList.remove('cf-ltc');
	}
	else if(selected_altcoin.value == 'LTC'){
		pay_with_icon.classList.add('cf-ltc');
		pay_with_icon.classList.remove('cf-eth');
	}
}

function disableAltcoin() {
	document.getElementById("altcoin-waiting").style.display = "none";
	document.getElementById("paywrapper").style.display = "block";
}

function toggleCoin(coin) {

	var btcBtn = document.getElementById('btc');
	var altcoinBtn = document.getElementById('altcoin');

	var btcDiv = document.getElementById('bnomics-btc-pane');
	var altcoinDiv = document.getElementById('bnomics-altcoin-pane');

	if(coin === 'btc') {
		btcBtn.classList.add('bnomics-paywith-selected');
		altcoinBtn.classList.remove('bnomics-paywith-selected');
		btcDiv.style.display = "block";
		altcoinDiv.style.display = "none";
	}

	if(coin === 'altcoin') {
		btcBtn.classList.remove('bnomics-paywith-selected');
		altcoinBtn.classList.add('bnomics-paywith-selected');
		btcDiv.style.display = "none";
		altcoinDiv.style.display = "block";
	}
}

function go_back() {
	document.getElementById("altcoin-waiting").style.display = "none";
	document.getElementById("paywrapper").style.display = "block";
	altcoin_waiting = false;
	document.getElementById("alt-address").value = '';
	document.getElementById("alt-amount").innerHTML = '';
	document.getElementById("alt-symbol").innerHTML = '';
	document.getElementById("alt-qrcode").href = '';
	clearInterval(interval);
	clearInterval(interval_check);
}

function set_alt_status(status) {
	for (var i = 7; i >= 0; i--) {
		if(status == i){
			document.getElementById("alt_status_"+i).style.display = "block";
		}else{
			document.getElementById("alt_status_"+i).style.display = "none";
		}
	}
}

function btc_copy_click() {
    var copyText = document.getElementById("bitcoin_address");
    copyText.select();
    document.execCommand("copy");
    document.getElementById("btc-copy-text").style.display = "block";
    setTimeout(function() {
        document.getElementById("btc-copy-text").style.display = "none";
    }, 2000); 
}

function alt_copy_click() {
	var copyText = document.getElementById("alt-address");
    copyText.select();
    document.execCommand("copy");
    document.getElementById("alt-copy-text").style.display = "block";
    setTimeout(function() {
        document.getElementById("alt-copy-text").style.display = "none";
    }, 2000);
}
