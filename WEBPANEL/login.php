<?php
$pageTitle = "Login";
?>
<!DOCTYPE html>
<html>
<?php include_once("includes/header.php"); ?>

<body class="theme-cyan">
<div class="authentication">
	<div class="container-fluid">
		<div class="row clearfix">
			<div class="col-lg-9 col-md-8 col-xs-12">
				<div class="l-detail">
                            <h5>Welcome</h5>
                            <h1>NoLag<span>CP</span></h1>
                            <h3>Sign in to start your session</h3>
                            <p>v1.3.build-124</p>                            
                            <ul class="list-unstyled l-social">
                                <li><a href="#"><i class="zmdi zmdi-facebook-box"></i></a></li>                                
                                <li><a href="#"><i class="zmdi zmdi-twitter"></i></a></li>
                                <li><a href="#"><i class="zmdi zmdi-youtube-play"></i></a></li>
                            </ul>
                        </div>
			</div>
			<div class="col-lg-3 col-md-4 col-xs-12">
				<div class="card">
				    <h4 class="l-login">Login</h4>
                    <form class="col-md-12" id="loginfrm">
							<div class="form-group form-float">
								<div class="form-line">
									<input type="text" name="email" class="form-control">
									<label class="form-label">E-Mail</label>
								</div>
							</div>
							<div class="form-group form-float">
								<div class="form-line">
									<input type="password" name="password" class="form-control">
									<label class="form-label">Password</label>
								</div>
							</div>
                            <div>
                                <input type="checkbox" name="tos" id="tos" class="filled-in chk-col-cyan">
                                <label for="tos">I agree to the <a href="https://nolag.host/tos.html">Terms of Service</a></label>
                            </div>
                            <a onclick="submitLogin();" class="btn btn-raised waves-effect bg-red" type="submit">Login</a> <a href="register" class="btn btn-raised waves-effect" type="submit">Register</a>
                            <div class="text-left"> <a href="resetPassword">Forgot Password?</a> </div>
						</form>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Jquery Core Js --> 
<script src="https://nolag.r.worldssl.net/panel/assets/bundles/libscripts.bundle.js"></script> <!-- Lib Scripts Plugin Js -->
<script src="https://nolag.r.worldssl.net/panel/assets/bundles/vendorscripts.bundle.js"></script> <!-- Lib Scripts Plugin Js --> 
<script src="https://nolag.r.worldssl.net/panel/assets/plugins/css-gradientify/gradientify.min.js"></script><!-- Gradientify Js -->

<script src="https://nolag.r.worldssl.net/panel/assets/bundles/mainscripts.bundle.js"></script><!-- Custom Js --> 
<script src="https://nolag.r.worldssl.net/panel/assets/plugins/bootstrap-notify/bootstrap-notify.js"></script> <!-- Bootstrap Notify Plugin Js -->
<script type="text/javascript">
    $(document).ready(function() {
        $("body").gradientify({
            gradients: [
                { start: [49,76,172], stop: [242,159,191] },
                { start: [255,103,69], stop: [240,154,241] },
                { start: [33,229,241], stop: [235,236,117] }
            ]
        });
    });
</script>
<script type = "text/javascript" language = "javascript">
function submitLogin() {
	var target;
	<?php
	if(isset($_GET['location'])) {
		$target = htmlentities($_GET["location"], ENT_QUOTES, 'UTF-8');
		if(startsWith(trim($target), "/panel/")){
			echo "target = \"" . $target . "\";";
		}

}
function startsWith($haystack, $needle)
{
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}
	?>
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
            '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
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

var parts = window.location.search.substr(1).split("&");
var $_GET = {};
for (var i = 0; i < parts.length; i++) {
    var temp = parts[i].split("=");
    $_GET[decodeURIComponent(temp[0])] = decodeURIComponent(temp[1]);
}//from stackoverflow - i don't know js for shit ~ rapture



	if(!document.getElementById("tos").checked){
		showNotification("bg-red", "You must agree with our terms of service", "bottom", "left", "animated bounceInRight", "animated bounceOutRight");
		return;
	}
	  $.ajax({
            url : 'api/login',
            type: "POST",
            data: $('#loginfrm').serialize(),
            success: function (data) {
				var obj = JSON.parse(data);
				if(obj.success == 1) {
					if (target == null){
                        if ($_GET['x']) {
                             window.location = "buy";
                             return;
                        }
                      window.location = "dashboard";
                     } else {
						 window.location = target.replace("panel","").substring(2); //memes lmao
						 showNotification("bg-black", obj.msg, "bottom", "left", "animated bounceInRight", "animated bounceOutRight");
					 }

					//set token for rest api
					localStorage.setItem('token', obj.token);
				}
				showNotification("bg-black", obj.msg, "bottom", "left", "animated bounceInRight", "animated bounceOutRight");
                //$("#loginfrm").html(data);
            },
            error: function (jXHR, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
}

function addAlert(message) {
    $('#alerts').append(
        '<div class="alert">' +
            '<button type="button" class="close" data-dismiss="alert">' +
            '&times;</button>' + message + '</div>');
}

      </script>
</body>
</html>