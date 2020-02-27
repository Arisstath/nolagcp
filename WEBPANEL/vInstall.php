<?php
require_once("includes/config.php");
include("includes/userutils.php");
include 'includes/security.php';
$key = "u>M&3gPCUMnc['7S";

$id = $_GET["id"];
$vServer = getvServer($db,$id);
$service = getService($db,$vServer["serviceid"]);


$expired = false;

if($service['active'] == 3 || $service['active'] == 4){
$expired = true;
}
		if(getRank() < 2){
if($vServer["username"] != getUsername()){
	if(!isSubuser($db, $id)){
	header("Location: dashboard");
	die("no access here");
	}
}
$service = getService($db,$vServer["serviceid"]);

if($service['active'] == 3 || $service['active'] == 4){
	header("Location: suspended");
	die("Your service is suspended.");
}
if($service['active'] == 0){
	header("Location: dashboard");
	die("You do not have access here.");
}
		}else {
	if($vServer["username"] != getUsername()){
	logAdmin($db, "ACCESS_PAGE", "Accessed vInstall page of server #" . $id);
}

}
function formatBytes($size, $precision = 2)
{
    $base = log($size, 1024);
    $suffixes = array('', 'KB', 'MB', 'GB', 'TB');

    return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
}

$vInfo = solusInfo($vServer['solusvm']);
$pageTitle = "Install OS #" . $id;
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

<section class="content">
  <div class="block-header">
        <div class="row">
            <div class="col-md-8 col-sm-7 col-xs-12">
                <div class="h-left clearfix">
                    <h2>Install OS</h2>
                    <small class="text-muted">From here you can install an operating system to your server.</small>
                    <ol class="breadcrumb breadcrumb-col-pink p-l-0">
                        <li><a href="javascript:void(0);">Servers</a></li>
                        <li class="active">OS Installer</li>
                    </ol>
                </div>
            </div>

        </div>
    </div>
<div class="container-fluid">
    <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-6 col-xs-6">
                <div class="card">
                    <div class="body">
                        <div class="row clearfix">
                            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs tab-nav-right" role="tablist">
                                    <li role="presentation" class="active"><a href="#vanilla" data-toggle="tab">Linux</a></li>
                                </ul>

                                <!-- Tab panes -->
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane animated flipInX active" id="vanilla">
									<select id="vanillai" class="form-control show-tick">
									 <option value="" disabled selected><?php echo $messages['install_choose']; ?></option>
										<option value="centos-6-x86_64">Centos 6</option>
										<option value="centos-7-x86_64">Centos 7</option>
										<option value="suse-13.2-x86_64">Suse 13.2</option>
										<option value="ubuntu-14.04-x86">Ubuntu 14.04</option>
										<option value="ubuntu-16.04-x86_64">Ubuntu 16.04</option>
										<option value="debian-8.0-x86_64">Debian 8.0</option>
									</select>
                                        <button onclick="installVanilla();" type="button" onclick="exec();" class="btn  btn-raised btn-primary waves-effect"><?php echo $messages['install_button']; ?></button>
                                    </div>
                                </div>
                            </div>

                        </div>
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
		var jsVersion = <?php echo $jsVersion; ?>;
		<?php $id = $_GET["id"];
		$id = htmlspecialchars($id, ENT_QUOTES, 'UTF-8');?>
		var token = localStorage.token;
		var id = "<?php echo($id); ?>";


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

					function installVanilla(){
					var e = document.getElementById("vanillai");
            var strUser = e.options[e.selectedIndex].value;
		$.ajax({
    type: "GET",
    url: "api/vInstall?id=" + id + "&csrf=<?php echo($csrfToken); ?>" + "&template=" + strUser,
    success: function (data) {
	var obj = JSON.parse(data);
	if(obj.success == 1){
		swal("Installation Success", "Your new root password is <b>" + JSON.parse(data).password + " </b>. Please note it down.", "info");
		showNotification("bg-green", JSON.parse(data).msg, "top", "right", "animated bounceInRight", "animated bounceOutRight");
		window.location = "vServer?id=" + id;
	} else if (obj.success == 3) {
		swal("Rebuilding server", "Your server is being rebuilt. Please allow this process a few moments to complete. Your password will remain the same.", "success");
		showNotification("bg-green", JSON.parse(data).msg, "top", "right", "animated bounceInRight", "animated bounceOutRight");
		window.location = "vServer?id=" + id;
	} else {
		showNotification("bg-red", JSON.parse(data).msg, "top", "right", "animated bounceInRight", "animated bounceOutRight");
	}
    },
	complete: function(xhr, textStatus) {
        if(xhr.status != 200){
			swal("Failed!", "Node is offline", "error");
		}
    }
});
	}







		</script>
</body>
</html>