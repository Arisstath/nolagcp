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
if($service['active'] == 1){
	header("Location: vInstall?id=".$id);
	die("Your service needs configuration.");
}
		}else {
	if($vServer["username"] != getUsername()){
	logAdmin($db, "ACCESS_PAGE", "Accessed vServer page of server #" . $id);
}

}
function formatBytes($size, $precision = 2)
{
    $base = log($size, 1024);
    $suffixes = array('', 'KB', 'MB', 'GB', 'TB');

    return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
}

$vInfo = solusInfo($vServer['solusvm']);
$pageTitle = "vServer #" . $id;
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
	textarea {
    background-color: #000;
    border: 1px solid #000;
    color: #ffffff;
    padding: 8px;
    font-family: courier new;
	height: 500px;
}

</style>
<section class="content">

	<div class="container-fluid">
      <div class="row clearfix">
		<!--
            <div class="col-md-8 col-sm-7 col-xs-12">
                <div class="h-left clearfix">
                    <h2>Overview #<?php echo $id; ?></h2>
                    <small class="text-muted">Welcome to your server's overview page. From here you can control and view stats of your Minecraft server</small>

                </div>
            </div>
			!-->
	<!-- Indicators
	<div class="card">
        <div class="header">
            <h2>Server Stats</h2>
			</div>
			<div class="col-md-2">


                 <input id="ramusage" type="text" class="knob" value="35" data-width="125" data-height="125" data-thickness="0.25" data-angleArc="250" data-angleoffset="-125" data-fgColor="#f67a82" readonly>
				  <h4 style="position: relative;left: 18px;top: -30px;">RAM Usage</h4>
             </div>
			 <div class="col-md-2">
                 <input id="cpuusage" type="text" class="knob" value="35" data-width="125" data-height="125" data-thickness="0.25" data-angleArc="250" data-angleoffset="-125" data-fgColor="#f67a82" readonly>
				 <h4 style="position: relative;left: 18px;top: -30px;">CPU Usage</h4>
             </div>
    </div>
	!-->
	<!-- Console !-->

	<!-- Controls !-->


	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	<div class="card">
		<div class="header">
		  <h2 id="status"><?php echo $messages['console_card4_title']; ?></h2>
		</div>
        <div class="body">
					<div class="body table-responsive">
                        <table class="table table-hover">

                            <tbody>
							<tr>
                             <td>IP Address:</td>
							<td><code><?php echo $vInfo->mainipaddress; ?></code></td>
							</tr>
							<tr>
                             <td>Status:</td>
							<td><code><?php echo $vInfo->state; ?></code></td>
							</tr>
							<tr>
                             <td>Memory:</td>
							<td><code><?php
							$ram = $vInfo->memory;
						    $slices = explode(",", $ram);
							$totalMemory = $slices[0];
							$usedMemory = $slices[1];
							echo(formatBytes($usedMemory) . "/" . formatBytes($totalMemory));
							?></code></td>
							</tr>
							<tr>
                             <td>Disk Space:</td>
							<td><code><?php
							$bandwith = $vInfo->hdd;
						    $slices = explode(",", $bandwith);
							$totalBandwith = $slices[0];
							$usedBandwith = $slices[1];
							echo(formatBytes($usedBandwith) . "/" . formatBytes($totalBandwith));

							?></code></td>
							</tr>
							<tr>
                             <td>Bandwidth:</td>
							<td><code><?php
							$bandwith = $vInfo->bandwidth;
						    $slices = explode(",", $bandwith);
							$totalBandwith = $slices[0];
							$usedBandwith = $slices[1];
							echo(formatBytes($usedBandwith) . "/" . formatBytes($totalBandwith));

							?></code></td>
							</tr>
                            </tbody>
                        </table>
                    </div>
        </div>


		</div>
		</div>
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	<div class="card">
		<div class="header">
		  <h2 id="status"><?php echo $messages['console_card2_title']; ?></h2>
		</div>
        <div class="body">
					<button type="button" onclick="startServer();" id="startbtn" class="btn  btn-raised btn-success waves-effect"><?php echo $messages['console_card2_start']; ?></button>
					<button type="button" onclick="stopServer();" id="stopbtn" class="btn  btn-raised btn-danger waves-effect"><?php echo $messages['console_card2_stop']; ?></button>
					<button type="button" onclick="restartServer();" id="restartbtn" class="btn  btn-raised btn-warning waves-effect"><?php echo $messages['console_card2_restart']; ?></button>
					<button type="button" onclick="rootPass();" id="rootbtn" class="btn  btn-raised btn-primary waves-effect">Reset Root Password</button>
					<button type="button" onclick="reinstallServer();" id="rootbtn" class="btn  btn-raised btn-primary waves-effect">Reinstall</button>
				</div>


		</div>
		</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
	<div class="card">
		<div class="header">
		  <h2 id="status">Memory Graph</h2>
		</div>
        <div class="body">
					<img width="100%" src="http://213.136.66.46:5353<?php echo $vInfo->memorygraph; ?>" alt="Please wait"</img>
        </div>


		</div>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
	<div class="card">
		<div class="header">
		  <h2 id="status">Load Graph</h2>
		</div>
        <div class="body">
					<img width="100%" src="http://213.136.66.46:5353<?php echo $vInfo->loadgraph; ?>" alt="Please wait"</img>
        </div>


		</div>
		</div>

    </div>
 </div>


</section>
<div class="modal fade" id="largeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="largeModalLabel"><?php echo $messages['console_modal_title']; ?></h4>
            </div>
            <div class="modal-body"><input disabled="" class="form-control" id="ftppass" onfocus="this.select();" onClick="this.setSelectionRange(0, this.value.length)" value="<?php echo $ftppass; ?>" id="disabled" type="text" class="validate"></input></div>
            <div class="modal-footer">
                <button type="button" onclick="resetFTP();" class="btn btn-link waves-effect"><?php echo $messages['console_modal_reset']; ?></button>
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal"><?php echo $messages['console_modal_close']; ?></button>
            </div>
        </div>
    </div>
</div>
<!-- Jquery Core Js -->
<script src="assets/bundles/libscripts.bundle.js"></script> <!-- Lib Scripts Plugin Js -->
<script src="assets/bundles/vendorscripts.bundle.js"></script> <!-- Lib Scripts Plugin Js -->

<script src="assets/plugins/jquery-sparkline/jquery.sparkline.js"></script> <!-- Sparkline Plugin Js -->

<script src="https://nolag.r.worldssl.net/panel/assets/bundles/mainscripts.bundle.js"></script><!-- Custom Js -->
<script src="https://nolag.r.worldssl.net/panel/assets/js/pages/charts/sparkline.js"></script>
<script src="https://nolag.r.worldssl.net/panel/assets/plugins/jquery-knob/jquery.knob.min.js"></script> <!-- Jquery Knob Plugin Js -->
<script src="https://nolag.r.worldssl.net/panel/assets/js/pages/charts/jquery-knob.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<script src="https://nolag.r.worldssl.net/panel/assets/js/pages/ui/modals.js"></script>
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

		function copyTextToClipboard(text) {
  var textArea = document.createElement("textarea");

  //
  // *** This styling is an extra step which is likely not required. ***
  //
  // Why is it here? To ensure:
  // 1. the element is able to have focus and selection.
  // 2. if element was to flash render it has minimal visual impact.
  // 3. less flakyness with selection and copying which **might** occur if
  //    the textarea element is not visible.
  //
  // The likelihood is the element won't even render, not even a flash,
  // so some of these are just precautions. However in IE the element
  // is visible whilst the popup box asking the user for permission for
  // the web page to copy to the clipboard.
  //

  // Place in top-left corner of screen regardless of scroll position.
  textArea.style.position = 'fixed';
  textArea.style.top = 0;
  textArea.style.left = 0;

  // Ensure it has a small width and height. Setting to 1px / 1em
  // doesn't work as this gives a negative w/h on some browsers.
  textArea.style.width = '2em';
  textArea.style.height = '2em';

  // We don't need padding, reducing the size if it does flash render.
  textArea.style.padding = 0;

  // Clean up any borders.
  textArea.style.border = 'none';
  textArea.style.outline = 'none';
  textArea.style.boxShadow = 'none';

  // Avoid flash of white box if rendered for any reason.
  textArea.style.background = 'transparent';


  textArea.value = text;

  document.body.appendChild(textArea);

  textArea.select();

  try {
    var successful = document.execCommand('copy');
    var msg = successful ? 'successful' : 'unsuccessful';
    console.log('Copying text command was ' + msg);
  } catch (err) {
    console.log('Oops, unable to copy');
	window.prompt("Copy to clipboard: Ctrl+C, Enter", text);
  }

  document.body.removeChild(textArea);
}
		var jsVersion = <?php echo $jsVersion; ?>;
		<?php $id = $_GET["id"];
		$id = htmlspecialchars($id, ENT_QUOTES, 'UTF-8');?>
		var token = localStorage.token;
		var id = "<?php echo($id); ?>";

		function rootPass(){
	ga('send', 'event', 'vps', 'action', 'stop');
		$.ajax({
    type: "GET",
    url: "api/vRoot?id=" + id + "&csrf=<?php echo($csrfToken); ?>",
    success: function (data) {
	var obj = JSON.parse(data);
	if(obj.success == 1){
		swal("New Root Password", "Your new root password is " + JSON.parse(data).password + " . Please note it down.", "info");
		showNotification("bg-green", JSON.parse(data).msg, "top", "right", "animated bounceInRight", "animated bounceOutRight");
	} else {
		showNotification("bg-red", JSON.parse(data).msg, "top", "right", "animated bounceInRight", "animated bounceOutRight");
	}
    }
});
	}
	function restartServer(){
	ga('send', 'event', 'vps', 'action', 'stop');
		$.ajax({
    type: "GET",
    url: "api/restart?id=" + id + "&csrf=<?php echo($csrfToken); ?>",
    success: function (data) {
	var obj = JSON.parse(data);
	if(obj.success == 1){
		showNotification("bg-green", JSON.parse(data).msg, "top", "right", "animated bounceInRight", "animated bounceOutRight");
	} else {
		showNotification("bg-red", JSON.parse(data).msg, "top", "right", "animated bounceInRight", "animated bounceOutRight");
	}
    }
});
	}
	function reinstallServer(){
		window.location = "https://nolag.host/panel/vInstall?id="+id;
	}
				function stopServer(){
	ga('send', 'event', 'vps', 'action', 'stop');
		$.ajax({
    type: "GET",
    url: "api/vStop?id=" + id + "&csrf=<?php echo($csrfToken); ?>",
    success: function (data) {
	var obj = JSON.parse(data);
	if(obj.success == 1){
		showNotification("bg-green", JSON.parse(data).msg, "top", "right", "animated bounceInRight", "animated bounceOutRight");
	} else {
		showNotification("bg-red", JSON.parse(data).msg, "top", "right", "animated bounceInRight", "animated bounceOutRight");
	}
    }
});
	}
		function startServer(){
	ga('send', 'event', 'vps', 'action', 'start');
		$.ajax({
    type: "GET",
    url: "api/vStart?id=" + id + "&csrf=<?php echo($csrfToken); ?>",
    success: function (data) {
	var obj = JSON.parse(data);
	if(obj.success == 1){
		showNotification("bg-green", JSON.parse(data).msg, "top", "right", "animated bounceInRight", "animated bounceOutRight");
	} else {
		showNotification("bg-red", JSON.parse(data).msg, "top", "right", "animated bounceInRight", "animated bounceOutRight");
	}
    }
});
	}
function resetFTP(){
			ga('send', 'event', 'Servers', 'action', 'ftppassword');
		$.ajax({
    type: "GET",
    url: "api/resetFTP?id="+id + "&csrf=<?php echo($csrfToken); ?>",
    success: function (data) {
		var obj = JSON.parse(data);
		if(obj.success == 1){
			document.getElementById("ftppass").value= obj.password;
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