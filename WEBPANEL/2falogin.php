<?php
$pageTitle = "2FA Login";
require_once("includes/config.php");
include("includes/userutils.php");

if($_SESSION['2fa']){
	header("Location: dashboard");
    die("Redirecting to dashboard");
}
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
                    <h2>2FA Login</h2>
                    <small class="text-muted">It seems that you have enabled 2-Factor authentication. Please type the code that is being shown at the Google Authenticator or Authy.</small>
                    <ol class="breadcrumb breadcrumb-col-pink p-l-0">
                        <li><a href="javascript:void(0);">Account</a></li>
                        <li class="active">2FA</li>
                    </ol>
					
                </div>
            </div>
            
        </div>
    </div>
	
<div class="container-fluid">
   <div class="row clearfix">
   <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	<div class="card">
		<div class="header">
		  <h2 id="status">2FA Login</h2>
		</div>
        <div class="body">
		
                 
                    <div class="input-group"> <span class="input-group-addon"> <i class="material-icons">lock</i> </span>
                                        <div class="form-line">
                                            <input type="text" min="1" id="code" class="form-control" onchange="updateValues();" placeholder="2FA Token">
                                        </div>
                                    </div>
					<button type="button" onclick="login2FA();" class="btn  btn-raised btn-primary waves-effect">Login</button>
					</div>
					
			  
		</div>
		</div>
        </div>
      </div>
</section>
<!-- Jquery Core Js --> 
<script src="https://nolag.r.worldssl.net/panel/assets/bundles/libscripts.bundle.js"></script> <!-- Lib Scripts Plugin Js --> 
<script src="https://nolag.r.worldssl.net/panel/assets/bundles/vendorscripts.bundle.js"></script> <!-- Lib Scripts Plugin Js --> 

<script src="https://nolag.r.worldssl.net/panel/assets/plugins/jquery-sparkline/jquery.sparkline.js"></script> <!-- Sparkline Plugin Js --> 

<script src="https://nolag.r.worldssl.net/panel/assets/bundles/mainscripts.bundle.js"></script><!-- Custom Js --> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<script src="https://nolag.r.worldssl.net/panel/assets/plugins/bootstrap-notify/bootstrap-notify.js"></script>
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


		 function login2FA(){
		$.ajax({
    type: "GET",
    url: "api/2falogin?code="+ document.getElementById('code').value + "&csrf=<?php echo($csrfToken); ?>",
    success: function (data) {
		var obj = JSON.parse(data);
		if(obj.success == 1){
			location.reload();
			showNotification("bg-green", obj.msg, "top", "right", "animated bounceInRight", "animated bounceOutRight");
		} else {
			showNotification("bg-red", obj.msg, "top", "right", "animated bounceInRight", "animated bounceOutRight");
		}

	//document.getElementById('code1').value = obj.msg;

    }
});
	}
		</script>
</body>
</html>