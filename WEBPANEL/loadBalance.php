<?php
die("Topping up has been disabled");
require_once("includes/config.php");
include("includes/userutils.php");

$pageTitle = "Top-up";
?>
<!DOCTYPE html>
<html>
<?php include_once("includes/header.php"); ?>
<body class="theme-red">
<!-- Page Loader -->
<?php include_once("includes/loader.php"); ?>

<!-- Overlay For Sidebars -->
<div class="overlay"></div>

<!-- Top Bar -->
<?php include_once("includes/topbar.php"); ?>

<!-- Left & Right bar menu -->
<?php include_once("includes/sidebar.php"); ?>
<style>
.currencyinput {
    border: 1px inset #ccc;
}
.currencyinput input {
    border: 0;
}
</style>
<section class="content">
  <div class="block-header">
        <div class="row">
            <div class="col-md-8 col-sm-7 col-xs-12">
                <div class="h-left clearfix">
                    <h2>Top-up</h2>
                    <small class="text-muted">From here you can top up your account easily. Minimum deposit amount is $1.</small>
                    <ol class="breadcrumb breadcrumb-col-pink p-l-0">
                        <li><a href="javascript:void(0);">Account</a></li>
                        <li class="active">Buy</li>
                    </ol>
					
                </div>
            </div>
            
        </div>
    </div>
	
<div class="container-fluid">
<div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card" >
                    <div class="header ">
                        <h2> SUMMER PROMOTION </h2>
                    </div>
                    <div class="body bg-blue">
					<p>Deposit <b>$5</b> and get an <b>extra</b> dollar!</p>
                    </div>
                </div>
            </div>
        </div>	
   <div class="row clearfix">
   <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	<div class="card">
		<div class="header">
		  <h2 id="status">Load balance</h2>
		</div>
        <div class="body">
		
                 
                    <div class="input-group"> <span class="input-group-addon"> <i class="material-icons">attach_money</i> </span>
                                        <div class="form-line">
                                            <input type="number" value="5" min="1" id="amount" class="form-control money-dollar" onchange="updateValues();" placeholder="Ex: 99,99 $">
                                        </div>
                                    </div>
					<button type="button" data-toggle="modal" data-target="#ploxwait" onclick="paypal();" class="btn  btn-raised btn-primary waves-effect">Paypal</button>
					<button type="button" data-toggle="modal" data-target="#ploxwait" onclick="paygol();" class="btn  btn-raised btn-primary waves-effect">Paygol(Paysafe, SMS)</button>
					</div>
					
			  
		</div>
		</div>
        </div>
      </div>
</section>
<div class="modal fade" id="ploxwait" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="smallModalLabel">Please wait</h4>
            </div>
            <div class="modal-body"> Redirecting to the payment gateway...</div>
        </div>
    </div>
</div>
<div class="modal fade" id="paygolCheckout" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="largeModalLabel">Checkout with Paygol</h4>
            </div>
            <div class="modal-body">
			<form id="paygolform" name="pg_frm" method="post" action="https://www.paygol.com/pay" >
   <input type="hidden" name="pg_serviceid" value="364110">
   <input type="hidden" name="pg_currency" value="USD">
   <input type="hidden" name="pg_name" value="NoLag Billing">
   <input type="hidden" name="pg_custom" value="<?php echo getUsername(); ?>">
   <input type="hidden" id="amountt" name="pg_price" value="5">
   <input type="hidden" name="pg_return_url" value="https://nolag.host/panel/loadBalance">
   <input type="hidden" name="pg_cancel_url" value="https://nolag.host/panel/loadBalance">
   <input type="image" name="pg_button" src="https://www.paygol.com/webapps/buttons/en/white.png" border="0" alt="Make payments with PayGol: the easiest way!" title="Make payments with PayGol: the easiest way!" >     
</form>
			</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal"><?php echo $messages['console_modal_close']; ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="paypalCheckout" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="largeModalLabel">Checkout with Paypal</h4>
            </div>
            <div class="modal-body">
			<form id="paypalform" name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="business" value="dimitrispetropoulos@protonmail.com">
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name="item_name" value="BOTSTACK PRIVATE HOSTING LTD CREDITS">
<input type="hidden" id="amountp" name="amount" value="5">
<input type="hidden" name="custom" value="<?php echo getUsername(); ?>">
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_buynow_LG.gif" border="0" name="submit" alt="Make payments with PayPal - it's fast, free and secure!">
</form>
			</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal"><?php echo $messages['console_modal_close']; ?></button>
            </div>
        </div>
    </div>
</div>
<!-- Jquery Core Js --> 
<script src="assets/bundles/libscripts.bundle.js"></script> <!-- Lib Scripts Plugin Js --> 
<script src="assets/bundles/vendorscripts.bundle.js"></script> <!-- Lib Scripts Plugin Js --> 

<script src="assets/plugins/jquery-sparkline/jquery.sparkline.js"></script> <!-- Sparkline Plugin Js --> 

<script src="assets/bundles/mainscripts.bundle.js"></script><!-- Custom Js --> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<script src="assets/plugins/bootstrap-notify/bootstrap-notify.js"></script>
		 <script>
		 				function showNotification(colorName, text, placementFrom, placementAlign, animateEnter, animateExit) {
    if (colorName === null || colorName === '') { colorName = 'bg-black'; }
    if (text === null || text === '') { text = 'Turning standard Bootstrap alerts'; }
    if (animateEnter === null || animateEnter === '') { animateEnter = 'animated fadeInDown'; }
    if (animateExit === null || animateExit === '') { animateExit = 'animated fadeOutUp'; }
    var allowDismiss = true;

    $.notify({
        message: text
    },
        {
            type: colorName,
            allow_dismiss: allowDismiss,
            newest_on_top: true,
            timer: 1000,
            placement: {
                from: placementFrom,
                align: placementAlign
            },
            animate: {
                enter: animateEnter,
                exit: animateExit
            },
            template: '<div data-notify="container" class="bootstrap-notify-container alert alert-dismissible {0} ' + (allowDismiss ? "p-r-35" : "") + '" role="alert">' +
            '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">?</button>' +
            '<span data-notify="icon"></span> ' +
            '<span data-notify="title">{1}</span> ' +
            '<span data-notify="message">{2}</span>' +
            '<div class="progress" data-notify="progressbar">' +
            '<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
            '</div>' +
            '<a href="{3}" target="{4}" data-notify="url"></a>' +
            '</div>'
        });
}

		 function paypal(){
			 document.getElementById('paypalform').submit();
		 }
		 function paygol(){
			 document.getElementById('paygolform').submit();
		 }
  function updateValues() {
	  var amount = document.getElementById('amount').value;
	  if(amount < 1){
		  showNotification("bg-red", "You need to deposit atleast $1.", "top", "right", "animated bounceInRight", "animated bounceOutRight");
		  document.getElementById('amount').value = 1;
		  amount = 1;
	  }
	  document.getElementById('amountp').value = amount;
	   document.getElementById('amountt').value = amount;
  }
		</script>
</body>
</html>