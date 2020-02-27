<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("includes/config.php");
include("includes/userutils.php");
include 'includes/security.php';

$key = "u>M&3gPCUMnc['7S";

$error_message = null;
if(getActiveServices($db) <= 0){
	$error_message = "You do not have any active services with us, please order one!";
}

if(getNegafinity() == null){
	$shit = <<<f
	<button class="btn  btn-raised btn-primary btn-xs waves-effect" onclick="getNegafinity();">Claim 1-month trial</button>
f;
	$error_message = "Servers run into issues all the time, especially gaming servers. Negafinity's technology allows you to easily track the status of your server from the web. Track <b>player count</b>, <b>lag</b>, and even see what <b>plugins</b> are causing <b>issues</b>.<br><b>Remember</b>, only NoLag customers get that deal.<br><br>" . $shit;
}

$pageTitle = "Negafinity";
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
				  <h2 style="position: relative;left: 18px;top: -30px;">RAM Usage</h2>
             </div>
			 <div class="col-md-2">
                 <input id="cpuusage" type="text" class="knob" value="35" data-width="125" data-height="125" data-thickness="0.25" data-angleArc="250" data-angleoffset="-125" data-fgColor="#f67a82" readonly>
				 <h2 style="position: relative;left: 18px;top: -30px;">CPU Usage</h2>
             </div>
    </div>
	!-->
	<!-- Console !-->
	
	<!-- Controls !-->
	

	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	<div class="card">
		<div class="header">
		  <h2 id="status">NEGAFINITY SERVER PERFORMANCE</h2>
		</div>
        <div class="body">
		<?php
						if($error_message == null){
						?>
					<div class="body table-responsive">
                        <table class="table table-hover">
                            
                            <tbody>
							<tr>
                             <td>Negafinity Code:</td>
							<td><code><?php echo getNegafinity(); ?></code></td>
							</tr>
							<tr>
                             <td>Negafinity</td>
							<td><code><a href="https://negafinity.com/signup/register" class="btn  btn-raised btn-primary btn-xs waves-effect">Redeem code</a></code></td>
							</tr>
                            </tbody>
                        </table>
                    </div>
					<?php
						} else {
					?>
					<?php echo($error_message); ?>
					<?php
						}
					?>
        </div>
					
			  
		</div>
		</div>
		</div>
	<div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <h2> PANEL SCREENSHOTS <small>Screenshots from negafinity</small> </h2>
                    </div>
                    <div class="body">
                        <div class="row">
                            <div class="col-sm-6 col-md-3">
                                <div class="thumbnail"> <img src="https://nolag.r.worldssl.net/panel/negafinityr/scr1n.png" alt="" />
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="thumbnail"> <img src="https://nolag.r.worldssl.net/panel/negafinityr/scr2n.png" alt=" /">
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="thumbnail"> <img src="https://nolag.r.worldssl.net/panel/negafinityr/scr3n.png" alt="" />
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3">
                                <div class="thumbnail"> <img src="https://nolag.r.worldssl.net/panel/negafinityr/scr4n.png" alt="" />
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

function getNegafinity(){
		$.ajax({
    type: "GET",
    url: "api/negafinity" + "?csrf=<?php echo($csrfToken); ?>",
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
	
		
	
	 
		//Make console box
		//Hackish code
		
		//CodeMirror.defineExtension("centerOnLine", function(line) { 
	//	var h = this.getScrollInfo().clientHeight; 
		//var coords = this.charCoords({line: line, ch: 0}, "local"); 
		//this.scrollTo(null, (coords.top + coords.bottom - h) / 2); 
	//	}); 


	//	var editor_one = CodeMirror.fromTextArea(document.getElementById("code1"), {
      //  lineNumbers: false,
      //  matchBrackets: true,
      //  styleActiveLine: true,
		//lineWrapping: true
  //  });
	
	


		
		</script>
</body>
</html>