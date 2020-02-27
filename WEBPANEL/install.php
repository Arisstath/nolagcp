<?php
require_once("includes/config.php");
include("includes/userutils.php");
include 'includes/security.php';

$key = "u>M&3gPCUMnc['7S";

$id = $_GET["id"];

$query = "SELECT * FROM mcservers WHERE id=:id";
	$query_params = array(':id' => $id );
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
        $row = $stmt->fetch();
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
		}
$baseurl = "https://". $row["node"] . ".mcsrv.top";
//die($baseurl);
$id = $_GET["id"];
$pageTitle = "JAR Installer"
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
                    <h2>Install JAR</h2>
                    <small class="text-muted">From here you can install a .jar file for your server. If you want to upload a custom one, make sure to rename it as <b>server.jar</b></small>
                    <ol class="breadcrumb breadcrumb-col-pink p-l-0">
                        <li><a href="javascript:void(0);">Servers</a></li>
                        <li class="active">JAR Installer</li>
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
                                    <li role="presentation" class="active"><a href="#vanilla" data-toggle="tab">Vanilla</a></li>
                                    <li role="presentation"><a href="#craftbukkit" data-toggle="tab">Craftbukkit</a></li>
                                    <li role="presentation"><a href="#spigot" data-toggle="tab">Spigot</a></li>
									<li role="presentation"><a href="#bungeecord" data-toggle="tab">Bungeecord</a></li>
									<li role="presentation"><a href="#waterfall" data-toggle="tab">Waterfall</a></li>
                                </ul>
                                
                                <!-- Tab panes -->
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane animated flipInX active" id="vanilla"> 
									<select id="vanillai" class="form-control show-tick">
									 <option value="" disabled selected><?php echo $messages['install_choose']; ?></option>
											<?php
											$api = file_get_contents("http://46.4.90.149:8080/versions/vanilla");
$json = json_decode($api, true);
$count = 0;
foreach ($json as $item) {
	$count++;
	$name = $item["name"];
	$url = $item["url"];
  $echo = <<<f
  <option value="$url">$name</option>
f;
echo ($echo);
}
											?>
									</select>
                                        <button onclick="installVanilla();" type="button" onclick="exec();" class="btn  btn-raised btn-primary waves-effect"><?php echo $messages['install_button']; ?></button>
                                    </div>
                                    <div role="tabpanel" class="tab-pane animated flipInX" id="craftbukkit"> 
<select id="bukkiti" class="form-control show-tick">
									 <option value="" disabled selected><?php echo $messages['install_choose']; ?></option>
											<?php
											$api = file_get_contents("http://46.4.90.149:8080/versions/bukkit");
$json = json_decode($api, true);
$count = 0;
foreach ($json as $item) {
	$count++;
	$name = $item["name"];
	$url = $item["url"];
  $echo = <<<f
  <option value="$url">$name</option>
f;
echo ($echo);
}
											?>
									</select>
                                        <button onclick="installBukkit();" type="button" onclick="exec();" class="btn  btn-raised btn-primary waves-effect"><?php echo $messages['install_button']; ?></button>
                                    </div>
									<div role="tabpanel" class="tab-pane animated flipInX" id="spigot"> 
<select id="spigoti" class="form-control show-tick">
									 <option value="" disabled selected><?php echo $messages['install_choose']; ?></option>
											<?php
											$api = file_get_contents("http://46.4.90.149:8080/versions/spigot");
$json = json_decode($api, true);
$count = 0;
foreach ($json as $item) {
	$count++;
	$name = $item["name"];
	$url = $item["url"];
  $echo = <<<f
  <option value="$url">$name</option>
f;
echo ($echo);
}
											?>
									</select>
                                        <button onclick="installSpigot();" type="button" onclick="exec();" class="btn  btn-raised btn-primary waves-effect"><?php echo $messages['install_button']; ?></button>
                                    </div>
									<div role="tabpanel" class="tab-pane animated flipInX" id="bungeecord"> 
 <select id="bungeei" class="form-control show-tick">
									 <option value="" disabled selected><?php echo $messages['install_choose']; ?></option>
											<?php
											$api = file_get_contents("http://46.4.90.149:8080/versions/bungeecord");
$json = json_decode($api, true);
$count = 0;
foreach ($json as $item) {
	$count++;
	$name = $item["name"];
	$url = $item["url"];
  $echo = <<<f
  <option value="$url">$name</option>
f;
echo ($echo);
}
											?>
									</select>
                                        <button onclick="installBungee();" type="button" onclick="exec();" class="btn  btn-raised btn-primary waves-effect"><?php echo $messages['install_button']; ?></button>
                                    </div>
									<div role="tabpanel" class="tab-pane animated flipInX" id="waterfall"> 
<select id="waterfalli" class="form-control show-tick">
									 <option value="" disabled selected><?php echo $messages['install_choose']; ?></option>
											<?php
											$api = file_get_contents("http://46.4.90.149:8080/versions/waterfall");
$json = json_decode($api, true);
$count = 0;
foreach ($json as $item) {
	$count++;
	$name = $item["name"];
	$url = $item["url"];
  $echo = <<<f
  <option value="$url">$name</option>
f;
echo ($echo);
}
											?>
									</select>
                                        <button onclick="installWaterfall();" type="button" onclick="exec();" class="btn  btn-raised btn-primary waves-effect"><?php echo $messages['install_button']; ?></button>
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
        <script>
		<?php $id = $_GET["id"]; ?>
		var endpoint = "<?php echo($baseurl); ?>";
		var token = localStorage.token;
		var id = "<?php echo($id); ?>";

				function installSpigot(){
					var e = document.getElementById("spigoti");
            var strUser = e.options[e.selectedIndex].value;
			strUser = btoa(strUser);
		$.ajax({
    type: "GET",
    url: endpoint + "/" + localStorage.token + "/servers/" + "<?php echo($id); ?>" + "/download/" + strUser,
    success: function (data) {
	var obj = JSON.parse(data);
	if(obj.success == 1){
		swal("Success!", JSON.parse(data).msg, "success");
		window.location = "console?id="+id;
	} else {
		swal("Failed!", JSON.parse(data).msg, "error");
	}
    },
	complete: function(xhr, textStatus) {
        if(xhr.status != 200){
			swal("Failed!", "Node is offline", "error");
		}
    }
});
	}
		function installWaterfall(){
					var e = document.getElementById("waterfalli");
            var strUser = e.options[e.selectedIndex].value;
			strUser = btoa(strUser);
		$.ajax({
    type: "GET",
    url: endpoint + "/" + localStorage.token + "/servers/" + "<?php echo($id); ?>" + "/download/" + strUser,
    success: function (data) {
	var obj = JSON.parse(data);
	if(obj.success == 1){
		swal("Success!", JSON.parse(data).msg, "success");
		window.location = "console?id="+id;
	} else {
		swal("Failed!", JSON.parse(data).msg, "error");
	}
    },
	complete: function(xhr, textStatus) {
        if(xhr.status != 200){
			swal("Failed!", "Node is offline", "error");
		}
    }
});
	}
	function installBungee(){
					var e = document.getElementById("bungeei");
            var strUser = e.options[e.selectedIndex].value;
			strUser = btoa(strUser);
		$.ajax({
    type: "GET",
    url: endpoint + "/" + localStorage.token + "/servers/" + "<?php echo($id); ?>" + "/download/" + strUser,
    success: function (data) {
	var obj = JSON.parse(data);
	if(obj.success == 1){
		swal("Success!", JSON.parse(data).msg, "success");
		window.location = "console?id="+id;
	} else {
		swal("Failed!", JSON.parse(data).msg, "error");
	}
    },
	complete: function(xhr, textStatus) {
        if(xhr.status != 200){
			swal("Failed!", "Node is offline", "error");
		}
    }
});
	}

					function installBukkit(){
					var e = document.getElementById("bukkiti");
            var strUser = e.options[e.selectedIndex].value;
			strUser = btoa(strUser);
		$.ajax({
    type: "GET",
    url: endpoint + "/" + localStorage.token + "/servers/" + "<?php echo($id); ?>" + "/download/" + strUser,
    success: function (data) {
	var obj = JSON.parse(data);
	if(obj.success == 1){
		swal("Success!", JSON.parse(data).msg, "success");
		window.location = "console?id="+id;
	} else {
		swal("Failed!", JSON.parse(data).msg, "error");
	}
    },
	complete: function(xhr, textStatus) {
        if(xhr.status != 200){
			swal("Failed!", "Node is offline", "error");
		}
    }
});
	}

					function installVanilla(){
					var e = document.getElementById("vanillai");
            var strUser = e.options[e.selectedIndex].value;
			strUser = btoa(strUser);
		$.ajax({
    type: "GET",
    url: endpoint + "/" + localStorage.token + "/servers/" + "<?php echo($id); ?>" + "/download/" + strUser,
    success: function (data) {
	var obj = JSON.parse(data);
	if(obj.success == 1){
		swal("Success!", JSON.parse(data).msg, "success");
		window.location = "console?id="+id;
	} else {
		swal("Failed!", JSON.parse(data).msg, "error");
	}
    },
	complete: function(xhr, textStatus) {
        if(xhr.status != 200){
			swal("Failed!", "Node is offline", "error");
		}
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




		</script>
</body>
</html>