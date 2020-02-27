<?php
require_once("includes/config.php");
include("includes/userutils.php");
include 'includes/security.php';
$key = "u>M&3gPCUMnc['7S";

$id = $_GET["id"];

$query = "SELECT * FROM mcservers WHERE id=:id";
	$query_params = array( ':id' => $id );
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
        $row = $stmt->fetch();
		$service = getService($db,$row["serviceid"]);


$expired = false;
if($service['active'] == 3 || $service['active'] == 4){
$expired = true;
}
		if(getRank() < 2){
if($row["username"] != getUsername()){
	if(!isSubuser($db, $id)){
	header("Location: dashboard");
	die("no access here");
	}
}
$service = getService($db,$row["serviceid"]);

if($service['active'] == 3 || $service['active'] == 4){
	header("Location: suspended");
	die("Your service is suspended.");
}
if($service['active'] == 0){
	header("Location: dashboard");
	die("You do not have access here.");
}
if($service['active'] == 1){
	header("Location: install?id=".$id);
	die("Your service needs configuration.");
}
		}else {
	if($row["username"] != getUsername()){
	logAdmin($db, "ACCESS_PAGE", "Accessed Console page of server #" . $id);
}

}

$host = $row["ip"];
$port = $row["port"];
$ftpuser = $row["ftpusername"];
$ftppass = $row["ftppass"];
$ram = $row["ram"];
$ftppass = Security::decrypt($ftppass, $key);
$baseurl = "https://". $row["node"] . ".mcsrv.top";
$sid = $row["id"];
$id = $row["id"];
$node = $row["node"];
$pageTitle = "Overview";
$eligible = $row["eligible"];
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
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	<div class="card">
		<div class="header">
		  <h2 id="status">Console (Stopped)</h2>
		</div>
        <div class="body">

				<textarea id="code1" style="background-color: #000;color: #ffffff;" rows="25" class="form-control no-resize" placeholder="Fetching logs..." readonly=""></textarea>
				<input type="checkbox" id="autoscroll" checked="">
				<label for="autoscroll"><?php echo $messages['console_card1_autoscroll']; ?></label>
				<div class="form-group">
                   <div class="form-line">
                                   <input id="command" class="form-control" onkeydown = "if (event.keyCode == 13)
                        exec();" type="text" placeholder="<?php echo $messages['console_card1_typecmd']; ?>"></input>

                    </div>
					<button type="button" onclick="exec();" class="btn  btn-raised btn-primary waves-effect"><?php echo $messages['console_card1_execute']; ?></button>
                </div>


		</div>
    </div>
	</div>
	<!-- Controls !-->
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	<div class="card">
		<div class="header">
		  <h2 id="status"><?php echo $messages['console_card2_title']; ?></h2>
		</div>
        <div class="body">
					<button type="button" onclick="startServer();" id="startbtn" class="btn  btn-raised btn-success waves-effect"><?php echo $messages['console_card2_start']; ?></button>
					<button type="button" onclick="stopServer();" id="stopbtn" class="btn  btn-raised btn-danger waves-effect"><?php echo $messages['console_card2_stop']; ?></button>
					<button type="button" onclick="restartServer();" id="restartbtn" class="btn  btn-raised btn-warning waves-effect"><?php echo $messages['console_card2_restart']; ?></button>
        </div>


		</div>
		</div>

		<!-- Rename !-->
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	<div class="card">
		<div class="header">
		  <h2 id="status"><?php echo $messages['console_card3_title']; ?></h2>
		</div>
        <div class="body">
		<div class="form-group">
                   <div class="form-line">
                        <input id="renamefield" class="form-control" type="text" placeholder="<?php echo $messages['console_card3_typename']; ?>"></input>

                    </div>
					<button type="button" onclick="renameServer();" class="btn  btn-raised btn-primary waves-effect"><?php echo $messages['console_card3_rename']; ?></button>
                </div>

        </div>


		</div>
		</div>
    </div>
 </div>


</section>
<?php if($eligible == 0) {
?>
<div class="modal fade" id="specialOffer" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="largeModalLabel">Free Minecraft-Market trial</h4>
            </div>
            <div class="modal-body">
			You have been selected for a 2-week free trial for <a target="_blank" href="https://www.minecraftmarket.com/preferral/nolag/">Minecraft-Market</a>'s Premium Plan. Minecraft-market is a donation platform, where you can automate your server donations and auto-assign the donators ranks, give them items and execute customized commands. This offer will not be available again for this server.
			</div>
            <div class="modal-footer">
			     <a type="button" onclick="noThanks(1);" href="https://www.minecraftmarket.com/preferral/nolag/" target="_blank" class="btn btn-link waves-effect">Redeem Offer</a>
                <button type="button" onclick="noThanks(0);" class="btn btn-link waves-effect" data-dismiss="modal">No Thanks</button>
            </div>
        </div>
    </div>
</div>
<?php
}
?>
<!-- Jquery Core Js -->
<script src="https://nolag.r.worldssl.net/panel/assets/bundles/libscripts.bundle.js"></script> <!-- Lib Scripts Plugin Js -->
<script src="https://nolag.r.worldssl.net/panel/assets/bundles/vendorscripts.bundle.js"></script> <!-- Lib Scripts Plugin Js -->

<script src="https://nolag.r.worldssl.net/panel/assets/plugins/jquery-sparkline/jquery.sparkline.js"></script> <!-- Sparkline Plugin Js -->

<script src="https://nolag.r.worldssl.net/panel/assets/bundles/mainscripts.bundle.js"></script><!-- Custom Js -->
<script src="https://nolag.r.worldssl.net/panel/assets/js/pages/charts/sparkline.js"></script>
<script src="https://nolag.r.worldssl.net/panel/assets/plugins/jquery-knob/jquery.knob.min.js"></script> <!-- Jquery Knob Plugin Js -->
<script src="https://nolag.r.worldssl.net/panel/assets/js/pages/charts/jquery-knob.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

        <script>

		var jsVersion = <?php echo $jsVersion; ?>;
		<?php $id = $_GET["id"]; ?>
		var endpoint = "<?php echo($baseurl); ?>";
		var token = localStorage.token;
		var id = "<?php echo($id); ?>";


					function noThanks(redeem){
				ga('send', 'event', 'Servers', 'action', 'mcmopt');
	$.ajax({
    type: "GET",
    url: "api/optOut.php" +"?csrf=<?php echo($csrfToken); ?>" + "&id=" + id+ "&redeem=" + redeem,
    success: function (data) {
		var obj = JSON.parse(data);
		if(obj.success == 0) {
		swal("Failed!", JSON.parse(data).msg, "error");
	}

	//document.getElementById('code1').value = obj.msg;

    }
});
		}
		function isJsonString(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}


function renameServer(){
	ga('send', 'event', 'Servers', 'action', 'rename');
	$.ajax({
    type: "GET",
    url: "api/updateName.php?id=" + id + "&name=" + document.getElementById("renamefield").value + "&csrf=<?php echo($csrfToken); ?>",
    success: function (data) {
		var obj = JSON.parse(data);
		if(obj.success == 1){
		swal("Success!", JSON.parse(data).msg, "success");
        location.reload();
	} else {
		swal("Failed!", JSON.parse(data).msg, "error");
	}

	//document.getElementById('code1').value = obj.msg;

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
		}
		Materialize.toast(obj.msg, 2500);

	//document.getElementById('code1').value = obj.msg;

    }
});
	}

		function exec(){
			ga('send', 'event', 'Servers', 'action', 'command');
		$.ajax({
    type: "GET",
    url: endpoint + "/" + localStorage.token + "/servers/" + "<?php echo($id); ?>" + "/command/" + encodeURIComponent(document.getElementById('command').value),
    success: function (data) {
		var obj = JSON.parse(data);
		document.getElementById("command").value="";

		//for some reason people think that we lag because console is updated every 4s, just update it instantly after cmd executation
		fetch(id,token);
	//document.getElementById('code1').value = obj.msg;

    }
});
	}
			function startServer(){
	ga('send', 'event', 'Servers', 'action', 'start');
		$.ajax({
    type: "GET",
    url: endpoint + "/" + localStorage.token + "/servers/" + "<?php echo($id); ?>" + "/start",
    success: function (data) {
	document.getElementById('code1').innerHTML = 'Starting server...';
	var obj = JSON.parse(data);
	if(obj.success == 1){
		swal("Success!", JSON.parse(data).msg, "success");
	} else {
		swal("Failed!", JSON.parse(data).msg, "error");
	}
    }
});
	}
	function restartServer() {
		ga('send', 'event', 'Servers', 'action', 'restart');
	$.ajax({
    type: "GET",
    url: endpoint + "/" + localStorage.token + "/servers/" + "<?php echo($id); ?>" + "/stop",
    success: function (data) {
    }
});

		 setTimeout(function () {
       $.ajax({
    type: "GET",
    url: endpoint + "/" + localStorage.token + "/servers/" + "<?php echo($id); ?>" + "/start",
    success: function (data) {
    }
});
    }, 2000);
	}
		function stopServer(){
			//send /stop and /end cmd
				$.ajax({
    type: "GET",
    url: endpoint + "/" + localStorage.token + "/servers/" + "<?php echo($id); ?>" + "/command/" + "stop",
    success: function (data) {
		var obj = JSON.parse(data);
		document.getElementById("command").value="";
	//document.getElementById('code1').value = obj.msg;

    }
});
	$.ajax({
    type: "GET",
    url: endpoint + "/" + localStorage.token + "/servers/" + "<?php echo($id); ?>" + "/command/" + "end",
    success: function (data) {
		var obj = JSON.parse(data);

	//document.getElementById('code1').value = obj.msg;

    }
});

			setTimeout(function () {
      		$.ajax({
    type: "GET",
    url: endpoint + "/" + localStorage.token + "/servers/" + "<?php echo($id); ?>" + "/stop",
    success: function (data) {
		var obj = JSON.parse(data);
		document.getElementById("command").value="";
	//document.getElementById('code1').value = obj.msg;

    }
});
    }, 4000);
			ga('send', 'event', 'Servers', 'action', 'stop');
		$.ajax({
    type: "GET",
    url: endpoint + "/" + localStorage.token + "/servers/" + "<?php echo($id); ?>" + "/stop",
    success: function (data) {
	var obj = JSON.parse(data);
	if(obj.success == 1){
		swal("Success!", JSON.parse(data).msg, "success");
	} else {
		swal("Failed!", JSON.parse(data).msg, "error");
	}
    }
});
	}


		$(document).ready(function() {
			<?php if($eligible == 0) {
?>
			//$('#specialOffer').modal('toggle');
			//$('#specialOffer').modal('show');
			<?php
			}
			?>
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


function escapeSpecialChars(jsonString) {

    return jsonString.replace(/\n/g, "\\n")
        .replace(/\r/g, "\\r")
        .replace(/\t/g, "\\t")
        .replace(/\f/g, "\\f");

}


	function fetch(serverID, token) {
	$.ajax({
    type: "GET",
    url: endpoint + "/" + token + "/servers/" + serverID + "/stats",
    success: function (data) {

    data = data.replace(/\\n/g, "\\n")
                                      .replace(/\\'/g, "\\'")
                                      .replace(/\\"/g, '\\"')
                                      .replace(/\\&/g, "\\&")
                                      .replace(/\\r/g, "\\r")
                                      .replace(/\\t/g, "\\t")
                                      .replace(/\\b/g, "\\b")
                                      .replace(/\\f/g, "\\f");


	 //var logs = data.logs + "";
	 //logs = logs.replace(/\\n/g, '&#10;');
	 //logs =  logs;
	// logs = logs.replace("\\n", '&#10;');
	// logs = logs.replace(/(?:\r\n|\r|\n)/g, '&#10;');
	// alert(logs);
	//CONSOLE LOGS
	/*
		$.ajax({
    type: "GET",
    url: "api/parseLog?id=" + serverID + "&csrf=<?php echo($csrfToken); ?>",
    success: function (data) {
		console.log(data);

	 }
	 });
*/
	var textarea = document.getElementById('code1');
	 if (document.getElementById('autoscroll').checked) {
		 textarea.scrollTop = textarea.scrollHeight;
	 }
	 var logs = JSON.parse(data).logs;
	 if(logs != null) {
		 var logs = JSON.parse(data).logs;
	 }else{
		 var logs = JSON.parse(data).msg;
	 }
	  document.getElementById('code1').innerHTML = logs.replace(/\\n/g, "&#10;").replace(/\\t/g, "").replace(/\\"/g, '"').replace(/\\'/g, "'").replace(/\\\//g, "\/");
     //SERVER STATUS
	 var status = JSON.parse(data).status;
	 if(status == null){
		 status = "STOPPED";
	 }
	 document.getElementById("status").innerHTML="<?php echo $messages['console_card1_title']; ?> (" + status + ")";
	 console.log(status);
	 if(status == "STOPPED"){
		$("#stopbtn").hide();
		$("#restartbtn").hide();
		$("#startbtn").show();
	 } else {
		$("#startbtn").hide();
		$("#stopbtn").show();
		$("#restartbtn").show();
	 }



/*
	if(data.status=="STOPPED"){
		$("#stopbtn").hide();
		$("#restartbtn").hide();
		$("#startbtn").show();
	}
	*/
	/*
	//RAM usage
	document.getElementById('ram').innerHTML = data.ram;

	//TPS
	document.getElementById('tps').innerHTML = data.tps;

	//ONLINE PLAYERS
	document.getElementById('ram').innerHTML = data.tps;
	*/

	}

});
}


fetch(id, token);
	setInterval(function(){
		 fetch(id, token);

       }, 4000);


		});

		</script>
</body>
</html>