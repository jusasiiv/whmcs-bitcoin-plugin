<link rel="stylesheet" type="text/css" href="css/order.css">
<link rel="stylesheet" type="text/css" href="css/icons/icons.css">
<link rel="stylesheet" type="text/css" href="css/cryptofont/cryptofont.min.css">

{if !$flyp_id }
<div id="btc-href" data-href="bitcoin:{$btc_address}?amount={$btc_amount}"></div>
<div id="btc-address" data-address="{$btc_address}"></div>
<div id="btc-amount" data-amount="{$btc_amount}"></div>
<div id="system-url" data-url="{$system_url}" data-orderid="{$order_id}"></div>
<div id="time-period" data-timeperiod="{$time_period}"></div>

<div ng-app="shopping-cart-demo">
  <div ng-controller="CheckoutController">
    <div class="bnomics-order-container">
      <!-- Heading row -->
      <div class="bnomics-order-heading">
        <div class="bnomics-order-heading-wrapper">
          {if $altcoins}
          <div class="bnomics-payment-option" ng-hide="order.status == -3">
            <span class="bnomics-paywith-label" ng-cloak>Pay with </span>
            <span>
              <span class="bnomics-paywith-option bnomics-paywith-btc bnomics-paywith-selected" ng-click="show_altcoin=0">BTC</span><span class="bnomics-paywith-option bnomics-paywith-altcoin" ng-click="show_altcoin=1">Altcoins</span>     
            </span>
          </div><br>
          {/if}
          <div class="bnomics-order-id">
            <span class="bnomics-order-number" ng-cloak> Order # [[order.order_id]]</span>
          </div>
        </div>
      </div>
      <!-- Spinner -->
      <div class="bnomics-spinner" ng-show="spinner" ng-cloak><div class="bnomics-ring"><div></div><div></div><div></div><div></div></div></div>
      <!-- Amount row -->
      <div class="bnomics-order-panel">
        <div class="bnomics-order-info">

          <div class="bnomics-bitcoin-pane" ng-hide="show_altcoin != 0" ng-init="show_altcoin=0;" ng-cloak>
            <div class="bnomics-btc-info">
              <!-- QR and Amount -->
              <div class="bnomics-qr-code" ng-hide="order.status == -3">
                <div class="bnomics-qr">
                          <a href="bitcoin:[[order.address]]?amount=[[order.bits/1.0e8]]">
                            <qrcode data="bitcoin:[[order.address]]?amount=[[order.bits/1.0e8]]" size="160" version="6">
                              <canvas class="qrcode"></canvas>
                            </qrcode>
                          </a>
                </div>
                <div class="bnomics-qr-code-hint">Click on the QR code to open in the wallet</div>
              </div>
              <!-- BTC Amount -->
              <div class="bnomics-amount">
              <div class="bnomics-bg">
                <!-- Order Status -->
                <div class="bnomics-order-status-wrapper">
                  <span class="bnomics-order-status-title" ng-show="order.status == -1" ng-cloak >To confirm your order, please send the exact amount of <strong>BTC</strong> to the given address</span>
                  <span class="warning bnomics-status-warning" ng-show="order.status == -3" ng-cloak>Payment Expired (Use the browser back button and try again)</span>
                  <span class="warning bnomics-status-warning" ng-show="order.status == -2" ng-cloak>Payment Error</span>
                </div>
                    <h4 class="bnomics-amount-title" for="invoice-amount" ng-hide="order.status == -3">
                     [[order.bits/1.0e8]] BTC
                    </h4>
                    <div class="bnomics-amount-wrapper" ng-hide="order.status == -3">
                      <hr class="bnomics-amount-seperator"> ≈
                      <span ng-cloak>[[order.value]]</span>
                      <small ng-cloak>[[order.currency]]</small>
                    </div>
              <!-- Bitcoin Address -->
                <div class="bnomics-address" ng-hide="order.status == -3">
                  <input ng-click="btc_address_click()" id="bnomics-address-input" class="bnomics-address-input" type="text" ng-value="order.address" readonly="readonly">
                  <i ng-click="btc_address_click()" class="material-icons bnomics-copy-icon">file_copy</i>
                </div>
                <div class="bnomics-copy-text" ng-hide="order.status == -3 || copyshow == false" ng-cloak>Copied to clipboard</div>
            <!-- Countdown Timer -->
                <div ng-cloak ng-hide="order.status != -1" class="bnomics-progress-bar-wrapper">
                  <div class="bnomics-progress-bar-container">
                    <div class="bnomics-progress-bar" style="width: [[progress]]%;"></div>
                  </div>
                </div>
                <span class="ng-cloak bnomics-time-left" ng-hide="order.status != -1">[[clock*1000 | date:'mm:ss' : 'UTC']] min left to pay your order</span>
              </div>
        <!-- Blockonomics Credit -->
            <div class="bnomics-powered-by">
             Powered by Blockonomics
            </div>
              </div>
            </div>
          </div>
        {if $altcoins}
          <div class="bnomics-altcoin-pane" ng-hide="show_altcoin != 1">
            <div class="bnomics-altcoin-bg">
                <div class="bnomics-altcoin-bg-color" ng-hide="altcoin_waiting" ng-cloak>
                 <div class="bnomics-altcoin-info-wrapper">
                  <span class="bnomics-altcoin-info" >Select your preferred <strong>Altcoin</strong> then click on the button below.</span>
                 </div>
                 </br>
                 <!-- Coin Select -->
                 <div class="bnomics-address">
                   <select ng-model="altcoinselect" ng-options="x for (x, y) in altcoins" ng-init="altcoinselect='Ethereum'"></select>
                 </div>
                 <div class="bnomics-altcoin-button-wrapper">
                  <a ng-click="pay_altcoins()" href=""><button><i class="cf" ng-hide="altcoinselect!='Ethereum'" ></i><i class="cf" ng-hide="altcoinselect!='Litecoin'" ></i> Pay with [[altcoinselect]]</button></a>
                 </div>
                </div>
            </div>
          </div>
		{/if}
        </div>
      </div>
    </div>
    <script>
    var blockonomics_time_period=10;
    </script>
    <script>
    var get_uuid="";
    </script>
  </div>
</div>
{else}
<div ng-app="shopping-cart-demo">
  <div ng-controller="AltcoinController">
    <div class="bnomics-order-container">
      <!-- Heading row -->
      <div class="bnomics-order-heading">
        <div class="bnomics-order-heading-wrapper">
          <div class="bnomics-order-id">
            <span class="bnomics-order-number" ng-cloak>Order #[[order.order_id]]</span>
          </div>
        </div>
      </div>
      <!-- Spinner -->
      <div class="bnomics-spinner" ng-show="spinner" ng-cloak><div class="bnomics-ring"><div></div><div></div><div></div><div></div></div></div>
      <!-- Amount row -->
      <div class="bnomics-order-panel">
        <div class="bnomics-order-info" ng-init="altcoin_waiting=true">
          <div class="bnomics-bitcoin-pane" ng-hide="show_altcoin != 0" ng-init="show_altcoin=1"></div>
          <div class="bnomics-altcoin-pane" ng-hide="show_altcoin != 1">
            <div class="bnomics-altcoin-waiting" ng-show="altcoin_waiting" ng-init="altcoin_waiting=true" ng-cloak>
              <!-- WAITING_FOR_DEPOSIT -->
              <div class="bnomics-btc-info" style="display: flex;flex-wrap: wrap;" ng-show="order.altstatus == 'waiting'" ng-cloak>
                <div style="flex: 1">
                  <!-- QR Code -->
                  <div class="bnomics-qr-code">
                    <div class="bnomics-qr">
                      <a href="[[altcoinselect]]:[[order.altaddress]]?amount=[[order.altamount]]&value=[[order.altamount]]">
                        <qrcode data="[[altcoinselect]]:[[order.altaddress]]?amount=[[order.altamount]]&value=[[order.altamount]]" size="160" version="6">
                          <canvas class="qrcode"></canvas>
                        </qrcode>
                      </a>
                    </div>
                    <div class="bnomics-qr-code-hint">
                      Click on the QR code to open in the wallet
                    </div>
                  </div>
                </div>
                <div style="flex: 2;">
                  <div class="bnomics-altcoin-bg-color">
                    <!-- Payment Text -->
                    <div class="bnomics-order-status-wrapper">
                      <span class="bnomics-order-status-title" ng-show="order.altstatus == 'waiting'" ng-cloak >
                        To confirm your order, please send the exact amount of <strong>[[altcoinselect]]</strong> to the given address
                      </span>
                    </div>
                    <h4 class="bnomics-amount-title" for="invoice-amount">
                      [[order.altamount]] [[order.altsymbol]]
                    </h4>
                    <!-- Altcoin Address -->
                    <div class="bnomics-address">
                      <input ng-click="alt_address_click()" id="bnomics-alt-address-input" class="bnomics-address-input" type="text" ng-value="order.altaddress" readonly="readonly">
                      <i ng-click="alt_address_click()" class="material-icons bnomics-copy-icon">file_copy</i>
                    </div>
                    <div class="bnomics-copy-text" ng-show="copyshow" ng-cloak>
                      Copied to clipboard
                    </div>
                    <!-- Countdown Timer -->
                    <div ng-cloak ng-hide="order.altstatus != 'waiting'  || alt_clock <= 0" class="bnomics-progress-bar-wrapper">
                      <div class="bnomics-progress-bar-container">
                        <div class="bnomics-progress-bar" style="width: [[alt_progress]]%;">
                        </div>
                      </div>
                    </div>
                    <span class="ng-cloak bnomics-time-left" ng-hide="order.altstatus != 'waiting' || alt_clock <= 0">[[alt_clock*1000 | date:'mm:ss' : 'UTC']] min left to pay your order
                    </span>
                  </div>
                  <div class="bnomics-altcoin-cancel">
                    <a href="" ng-click="go_back()">Click here</a> to go back
                  </div>
                  <!-- Blockonomics Credit -->
                  <div class="bnomics-powered-by">
                    Powered by Blockonomics
                  </div>
                </div>
              </div>
              <!-- RECEIVED -->
              <div class="bnomics-altcoin-bg-color" ng-show="order.altstatus == 'received'" ng-cloak>
                <h4>Received</h4>
                <h4><i class="material-icons bnomics-alt-icon">check_circle</i></h4>
                Your payment has been received and your order will be processed shortly.
              </div>
              <!-- ADD_REFUND -->
              <div class="bnomics-status-flex bnomics-altcoin-bg-color" ng-show="order.altstatus == 'add_refund'" ng-cloak >
                <h4>Refund Required</h4>
                <p ng-hide="hide_refund_reason">Your order couldn\'t be processed as you didn\'t pay the exact expected amount.<br>The amount you paid will be refunded.</p>
                <h4><i class="material-icons bnomics-alt-icon">error</i></h4>
                <p id="bnomics-refund-message">Enter your refund address and click the button below to recieve your refund.</p>
                <div id="bnomics-refund-errors"></div>
                <input type="text" id="bnomics-refund-input" placeholder="[[order.altsymbol]] Address">
                <br>
                <button id="alt-refund-button" ng-click="add_refund_click()">Refund</button>
              </div>
              <!-- REFUNDED -->
              <div class="bnomics-status-flex bnomics-altcoin-bg-color" ng-show="order.altstatus == 'refunded'" ng-cloak >
                <h4>Refund Submitted</h4>
                <div>Your refund details have been submitted. The refund will be automatically sent to <b>[[altrefund]]</b></div>
                <h4><i class="material-icons bnomics-alt-icon">autorenew</i></h4>
                <div>If you don\'t get refunded in a few hours, contact <a href="mailto:support@flyp.me">support@flyp.me</a> with the following uuid:<br><span id="alt-uuid"><b>[[altuuid]]</b></span></div>
                <div>We have emailed you the information on this page. You can safely close this window or navigate away</div>
              </div>
              <!-- EXPIRED -->
              <div class="bnomics-status-flex bnomics-altcoin-bg-color" ng-show="order.altstatus == 'expired'" ng-cloak >
                <h4>Expired</h4>
                <h4><i class="material-icons bnomics-alt-icon">timer</i></h4>
                <p>Use the browser back button and try again.</p>
                <p>If you already paid, <strong><a href="" ng-click="get_refund()">click here</a></strong> to get a refund.</p>
              </div>
              <!-- LOW/HIGH -->
              <div class="bnomics-status-flex bnomics-altcoin-bg-color" ng-show="order.altstatus == 'low_high'" ng-cloak >
                <h4>Error</h4>
                <h4><i class="material-icons bnomics-alt-icon">error</i></h4>
                <p>Order amount too <strong>[[lowhigh]]</strong> for [[order.altsymbol]] payment.</p>
                <p><a href="" ng-click="go_back()">Click here</a> to go back and use BTC to complete the payment.</p>
              </div>
            </div>
          <!-- Blockonomics Credit -->
          <div class="bnomics-powered-by" ng-hide="order.altstatus == 'waiting'">Powered by Blockonomics</div>
        </div>
      </div>
    </div>
  </div>
  <script>
    var blockonomics_time_period=10;
  </script>
  <script>
    var get_uuid="";
  </script>
</div>
{/if}
{if $error}

<div id="address-error">
    <h3>Could not generate new bitcoin address.</h3>
    <i>Note to webmaster: Please login to admin and go to Setup > Payments > Payment Gateways > Manage Existing Gateways and use the Test Setup button to diagnose the error. </i>
</div>

{/if}

{if $pending}

<div id="address-error">
    <h3>Payment is pending</h3>
    <i>Additional payments to invoice are only allowed after current pending transaction is confirmed. Monitor the transaction here: 
      <a href="https://www.blockonomics.co/api/tx?txid={$txid}" target="_blank">{$txid}</a></i>
</div>

{/if}

<script type="text/javascript" src="js/angular.min.js"></script>
<script type="text/javascript" src="js/angular-resource.min.js"></script>
<script type="text/javascript" src="js/app.js"></script>
<script type="text/javascript" src="js/qrcode-generator/qrcode.js"></script>
<script type="text/javascript" src="js/qrcode-generator/qrcode_UTF8.js"></script>
<script type="text/javascript" src="js/angular-qrcode/angular-qrcode.js"></script>
<script type="text/javascript" src="js/reconnecting-websocket.min.js"></script>