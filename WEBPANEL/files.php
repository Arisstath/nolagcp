<?php
require_once("includes/config.php");
include("includes/userutils.php");
include 'includes/security.php';

$key = "u>M&3gPCUMnc['7S";

$id = $_GET["id"];
$query = "SELECT * FROM mcservers WHERE id=:id";
	$query_params = array(':id' => $_GET["id"] );
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
        $row = $stmt->fetch();
		$server = getServer($db, $id);
		$host = $row["ip"];
		if($server["node"] == "US1"){
			$host = "103.77.224.18";
		}
		$_SESSION["hostname"] = $host;
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
	logAdmin($db, "ACCESS_PAGE", "Accessed Files page of server #" . $id);
}

}

$_SESSION["ftpusername"] = $row["ftpusername"];

$_SESSION["ftppassword"] = Security::decrypt($row["ftppass"], $key);

$pageTitle = "File Manager";
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
    <iframe id="filemanager" height="100%" width="100%" src="filemanager/"></iframe>
</section>
<!-- Jquery Core Js --> 
<script src="https://nolag.r.worldssl.net/panel/assets/bundles/libscripts.bundle.js"></script> <!-- Lib Scripts Plugin Js --> 
<script src="https://nolag.r.worldssl.net/panel/assets/bundles/vendorscripts.bundle.js"></script> <!-- Lib Scripts Plugin Js --> 

<script src="https://nolag.r.worldssl.net/panel/assets/plugins/jquery-sparkline/jquery.sparkline.js"></script> <!-- Sparkline Plugin Js --> 

<script src="https://nolag.r.worldssl.net/panel/assets/bundles/mainscripts.bundle.js"></script><!-- Custom Js --> 
<script>
//dirty hax
$(window).resize(function() {
    $('#filemanager').height($(window).height());
});

$(window).trigger('resize');
</script>
</body>
</html>