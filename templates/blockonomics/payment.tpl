<link rel="stylesheet" type="text/css" href="css/style.css">
<link rel="stylesheet" type="text/css" href="css/icons/icons.css">
<link rel="stylesheet" type="text/css" href="css/cryptofont/cryptofont.css">
<script type="text/javascript" src="js/qrcode.min.js"></script>
<script type="text/javascript" src="js/reconnecting-websocket.min.js"></script>
<script type="text/javascript" src="js/payment.js"></script>

{if $flyp_id}
<div id="flyp-id" data-uuid="{$flyp_id}"></div>
<div id="system-url" data-url="{$system_url}" data-orderid="{$order_id}"></div>
<div class="alt-paywrapper center">
	<!-- Waiting -->
	<div id="alt_status_0" class="row" style="display: none">
		<div class="bnomics-btc-info">
	        <div class="col-md-12">
	          <div class="bnomics-altcoin-bg-color">
	          	<h4>Waiting for Deposit</h4>
	          	<h4><i class="material-icons bnomics-alt-icon">error</i></h4>
	            <div class="bnomics-order-status-wrapper">
	              <span class="bnomics-order-status-title">
                	<p>This payment has not been sent.</p></span>
	            </div>
	          </div>
			  <p class="powered">Powered by Blockonomics</p>
        </div>
   	  </div>
	</div>
	<!-- DEPOSIT_RECEIVED -->
	<div id="alt_status_1" class="row">
	      <div class="bnomics-btc-info">
	        <div class="col-md-12">
	          <div class="bnomics-altcoin-bg-color">
	          	<h4>Processing</h4>
	          	<h4><i class="cf bnomics-alt-icon"></i></h4>
	            <div class="bnomics-order-status-wrapper">
	              <span class="bnomics-order-status-title">
	              	<a class="alt-explorer" href="#" target="_blank"><p><span class="alt-coin"></span> Deposit Confirmation</p></a>
                	<p>This will take a while for the network to confirm your payment.</p></span>
	            </div>
	          </div>
			  <p class="powered">Powered by Blockonomics</p>
        </div>
   	  </div>
	</div>
	<!-- DEPOSIT_CONFIRMED -->
	<div id="alt_status_2" class="row">
	      <div class="bnomics-btc-info">
	        <div class="col-md-12">
	          <div class="bnomics-altcoin-bg-color">
	          	<h4>Processing</h4>
	          	<h4><i class="cf bnomics-alt-icon"></i></h4>
	            <div class="bnomics-order-status-wrapper">
	              <span class="bnomics-order-status-title">
	              	<a class="alt-explorer" href="#" target="_blank"><p><span class="alt-coin"></span> Deposit Confirmation</p></a>
                	<p>This will take a while for the network to confirm your payment.</p></span>
	            </div>
	          </div>
			  <p class="powered">Powered by Blockonomics</p>
        </div>
   	  </div>
	</div>
	<!-- EXECUTED -->
	<div id="alt_status_3" class="row">
	      <div class="bnomics-btc-info">
	        <div class="col-md-12">
	          <div class="bnomics-altcoin-bg-color">
	          	<h4>Completed</h4>
	          	<h4><i class="material-icons bnomics-alt-icon">receipt</i></h4>
	            <div class="bnomics-order-status-wrapper">
	              <span class="bnomics-order-status-title">
	              	<a class="alt-finish-url" href="viewinvoice.php?id={$order_id}&paymentsuccess=true" target="_blank">View Order Confirmation</a>
	            </div>
	          </div>
			  <p class="powered">Powered by Blockonomics</p>
        </div>
   	  </div>
	</div>
	<!-- REFUNDED -->
	<div id="alt_status_4" class="row">
	      <div class="bnomics-btc-info">
	        <div class="col-md-12">
	          <div class="bnomics-altcoin-bg-color">
	          	<h4>Refunded</h4>
	          	<h4><i class="material-icons bnomics-alt-icon">cached</i></h4>
	            <div class="bnomics-order-status-wrapper">
	              <span class="bnomics-order-status-title">
                	<p>This payment has been refunded.</p>
                  </span>
	            </div>
	          </div>
			  <p class="powered">Powered by Blockonomics</p>
        </div>
   	  </div>
	</div>
	<!-- CANCELED -->
	<div id="alt_status_5" class="row">
	      <div class="bnomics-btc-info">
	        <div class="col-md-12">
	          <div class="bnomics-altcoin-bg-color">
	          	<h4>Canceled</h4>
	          	<h4><i class="material-icons bnomics-alt-icon">cancel</i></h4>
	            <div class="bnomics-order-status-wrapper">
	              <span class="bnomics-order-status-title">
                	<p>This probably happened because you paid less than the expected amount.<br>Please contact <a href="mailto:hello@flyp.me">hello@flyp.me</a> with the below order id for a refund:</p>
                	<p class="alt-uuid"></p>
                  </span>
	            </div>
	          </div>
			  <p class="powered">Powered by Blockonomics</p>
        </div>
   	  </div>
	</div>
	<!-- EXPIRED -->
	<div id="alt_status_6" class="row">
	      <div class="bnomics-btc-info">
	        <div class="col-md-12">
	          <div class="bnomics-altcoin-bg-color">
	          	<h4>Expired</h4>
	          	<h4><i class="material-icons bnomics-alt-icon">timer</i></h4>
	            <div class="bnomics-order-status-wrapper">
	              <span class="bnomics-order-status-title">
                	<p>Payment Expired (Use the browser back button and try again).</p></span>
	            </div>
	          </div>
			  <p class="powered">Powered by Blockonomics</p>
        </div>
   	  </div>
	</div>
	<!-- Low/High -->
	<div id="alt_status_7" class="row"></div>
</div>
{else if not $error}

<div id="btc-href" data-href="bitcoin:{$btc_address}?amount={$btc_amount}"></div>
<div id="btc-address" data-address="{$btc_address}"></div>
<div id="btc-amount" data-amount="{$btc_amount}"></div>
<div id="system-url" data-url="{$system_url}" data-orderid="{$order_id}"></div>
<div id="time-period" data-timeperiod="{$time_period}"></div>

<div id="paywrapper" class="payment-wrapper center">

	{if $altcoins}
	<div class="altcoin-select">
		<span>Pay with </span>
		<span id="btc" onclick="toggleCoin('btc')" type="button" class="bnomics-paywith-btc bnomics-paywith-option bnomics-paywith-selected"><b>BTC</b></span><!--
	 --><span id="altcoin" onclick="toggleCoin('altcoin')" type="button" class="bnomics-paywith-altcoin bnomics-paywith-option"><b>Altcoins</b></span>
	</div>
	{/if}
	
	<div id="bnomics-btc-pane">
		<h3>Order# {$order_id}</h3>
		<div class="row clear">
			<div class="col-md-4">
				<div class="qr-code-wrapper">
					<a id="btc-address-a" href="bitcoin:{$btc_address}?amount={$btc_amount}">
						<div id="qrcode"></div>
					</a>
					<p>Click on the QR code to open in the wallet</p>
				</div>
			</div>
			<div class="col-md-8">
				<div class="info center">
					<div class="bnomics-altcoin-bg-color">
							<p>To pay, please send exact amount of <span>BTC</span> to the <b>given address</b></p>
							<h2>{$btc_amount} BTC</h2>
							<hr class="amount-seperator">
							<p>&asymp; {$fiat_amount} {$currency}</p>
							<b><input id="bitcoin_address" class="address" value="{$btc_address}" onclick="btc_copy_click()" readonly></b>
							<i class="material-icons content-copy" onclick="btc_copy_click()">content_copy</i>
							<div id="btc-copy-text" class="btc-copy-text copy-text">Copied to clipboard</div>
					    <div class="time-wrapper">
					       <div id="time-left"></div>
							</div>
						<p><span id="time-left-minutes"></span> min left to pay your order</p>
					</div>
					<p class="powered">Powered by Blockonomics</p>
				</div>
			</div>
		</div>
	</div>

	{if $altcoins}
	<div id="bnomics-altcoin-pane" class="bnomics-altcoin-pane">
        <div class="bnomics-altcoin-bg-color">
         <div class="bnomics-altcoin-info-wrapper">
        	<span class="bnomics-altcoin-info" >Select your preferred <strong>Altcoin</strong> then click on the button below.</span>
         </div>
         </br>
         <!-- Coin Select -->
         <div class="bnomics-address">
           <select id="altcoin_select" onchange="altcoin_select()">
           	<option value="ETH" id="ethereum">Ethereum</option>
           	<option value="LTC" id="litecoin">Litecoin</option>
           </select>
         </div>
         </br>
         <!-- Pay Button -->
         <div class="bnomics-altcoin-button-wrapper">
          <a onclick="pay_altcoins()" href="#"><button><i id="pay_with_icon" class="cf cf-eth"></i></i>Pay with <span id="alt_selected">ETH</span></button></a>
         </div>
        </div>
		<p class="powered">Powered by Blockonomics</p>
	</div>
	{/if}

</div>

<div id="altcoin-waiting" class="alt-paywrapper center">
	<!-- Waiting -->
	<div id="alt_status_0" class="row">
		<div class="col-md-12 altcoin-waiting">
		      <div class="bnomics-btc-info">
		        <div class="col-md-4">
					<div class="qr-code-wrapper">
						<a id="alt-qrcode" href="">
							<div id="qrcode"></div>
						</a>
						<p>Click on the QR code to open in the wallet</p>
					</div>
		        </div>
		        <div class="col-md-8">
		          <div class="bnomics-altcoin-bg-color">
		            <div class="bnomics-order-status-wrapper">
		              <span class="bnomics-order-status-title">To confirm your order, please send the exact amount of <strong><span class="alt-coin"></span></strong> to the given address</span>
		            </div>
					<h2><span id="alt-amount"></span> <span id="alt-symbol"></span></h2>
					<b><input id="alt-address" class="address" onclick="alt_copy_click()" readonly></b>
					<i class="material-icons content-copy" onclick="alt_copy_click()">content_copy</i>
		              <div id="alt-copy-text" class="alt-copy-text copy-text">Copied to clipboard</div>
		              <div id="alt-time-wrapper">
			              <div class="time-wrapper">
					      	<div id="alt-time-left"></div>
						  </div>
						  <p><span id="alt-time-left-minutes"></span> min left to pay your order</p>
					  </div>
					  <div class="bnomics-altcoin-cancel"><a href="#" onclick="go_back()"> Click here</a> to go back</div>
		          </div>
				  <p class="powered">Powered by Blockonomics</p>
	        </div>
	   	  </div>
		</div>
	</div>
	<!-- DEPOSIT_RECEIVED -->
	<div id="alt_status_1" class="row">
	      <div class="bnomics-btc-info">
	        <div class="col-md-12">
	          <div class="bnomics-altcoin-bg-color">
	          	<h4>Received</h4>
	          	<h4><i class="material-icons bnomics-alt-icon">check_circle</i></h4>
	            <div class="bnomics-order-status-wrapper">
	              <span class="bnomics-order-status-title">
                	<p>Your payment has been received. You can track your order using the link sent to your email.</p></span>
	            </div>
	          </div>
			  <p class="powered">Powered by Blockonomics</p>
        </div>
   	  </div>
	</div>
	<!-- DEPOSIT_CONFIRMED -->
	<div id="alt_status_2" class="row">
	      <div class="bnomics-btc-info">
	        <div class="col-md-12">
	          <div class="bnomics-altcoin-bg-color">
	          	<h4>Processing</h4>
	          	<h4><i class="cf bnomics-alt-icon"></i></h4>
	            <div class="bnomics-order-status-wrapper">
	              <span class="bnomics-order-status-title">
	              	<a class="alt-explorer" href="#" target="_blank"><p><span class="alt-coin"></span> Deposit Confirmation</p></a>
                	<p>This will take a while for the network to confirm your payment.</p></span>
	            </div>
	          </div>
			  <p class="powered">Powered by Blockonomics</p>
        </div>
   	  </div>
	</div>
	<!-- EXECUTED -->
	<div id="alt_status_3" class="row">
	      <div class="bnomics-btc-info">
	        <div class="col-md-12">
	          <div class="bnomics-altcoin-bg-color">
	          	<h4>Completed</h4>
	          	<h4><i class="material-icons bnomics-alt-icon">receipt</i></h4>
	            <div class="bnomics-order-status-wrapper">
	              <span class="bnomics-order-status-title">
	              	<a class="alt-finish-url" href="viewinvoice.php?id={$order_id}&paymentsuccess=true" target="_blank">View Order Confirmation</a>
	            </div>
	          </div>
			  <p class="powered">Powered by Blockonomics</p>
        </div>
   	  </div>
	</div>
	<!-- REFUNDED -->
	<div id="alt_status_4" class="row">
	      <div class="bnomics-btc-info">
	        <div class="col-md-12">
	          <div class="bnomics-altcoin-bg-color">
	          	<h4>Refunded</h4>
	          	<h4><i class="material-icons bnomics-alt-icon">cached</i></h4>
	            <div class="bnomics-order-status-wrapper">
	              <span class="bnomics-order-status-title">
                	<p>This payment has been refunded.</p>
                  </span>
	            </div>
	          </div>
			  <p class="powered">Powered by Blockonomics</p>
        </div>
   	  </div>
	</div>
	<!-- CANCELED -->
	<div id="alt_status_5" class="row">
	      <div class="bnomics-btc-info">
	        <div class="col-md-12">
	          <div class="bnomics-altcoin-bg-color">
	          	<h4>Canceled</h4>
	          	<h4><i class="material-icons bnomics-alt-icon">cancel</i></h4>
	            <div class="bnomics-order-status-wrapper">
	              <span class="bnomics-order-status-title">
                	<p>This probably happened because you paid less than the expected amount.<br>Please contact <a href="mailto:hello@flyp.me">hello@flyp.me</a> with the below order id for a refund:</p>
                	<p class="alt-uuid"></p>
                  </span>
	            </div>
	          </div>
			  <p class="powered">Powered by Blockonomics</p>
        </div>
   	  </div>
	</div>
	<!-- EXPIRED -->
	<div id="alt_status_6" class="row">
	      <div class="bnomics-btc-info">
	        <div class="col-md-12">
	          <div class="bnomics-altcoin-bg-color">
	          	<h4>Expired</h4>
	          	<h4><i class="material-icons bnomics-alt-icon">timer</i></h4>
	            <div class="bnomics-order-status-wrapper">
	              <span class="bnomics-order-status-title">
                	<p>Payment Expired (Use the browser back button and try again).</p></span>
	            </div>
	          </div>
			  <p class="powered">Powered by Blockonomics</p>
        </div>
   	  </div>
	</div>
	<!-- Low/High -->
	<div id="alt_status_7" class="row">
	      <div class="bnomics-btc-info">
	        <div class="col-md-12">
	          <div class="bnomics-altcoin-bg-color">
	          	<h4>Error</h4>
	          	<h4><i class="material-icons bnomics-alt-icon">error</i></h4>
	            <div class="bnomics-order-status-wrapper">
	              <span class="bnomics-order-status-title">
                	<p>Order amount too <strong>Low/High</strong> for <span class="alt-coin"></span> payment.</p></span>
                	<div class="bnomics-altcoin-cancel"><a href="#" onclick="go_back()"> Click here</a> to go back and use BTC to complete the payment.</div>
	            </div>
	          </div>
			  <p class="powered">Powered by Blockonomics</p>
        </div>
   	  </div>
	</div>
</div>

<div class="clear"></div>

{else}

<div id="address-error">
    <h3>Could not generate new bitcoin address.</h3>
    <i>Note to webmaster: Please login to admin and go to Setup > Payments > Payment Gateways > Manage Existing Gateways and use the Test Setup button to diagnose the error. </i>
</div>

{/if}