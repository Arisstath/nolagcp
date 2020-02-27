<?php
require_once("../includes/config.php");
include("../includes/userutils.php");
include '../includes/security.php';
$key = "u>M&3gPCUMnc['7S";
//===[ NOLAG CSRF PROTECTION]=== COPY FROM HERE
if (!empty($_GET['csrf'])) {
    if (!hash_equals($_SESSION['token'], $_GET['csrf'])) {
         $data = [ 'success' => 0, 'msg' => 'CSRF Token is invalid.' ];
	      die(json_encode($data));
    }
} else {
	$data = [ 'success' => 0, 'msg' => 'CSRF Token is missing from your request.' ];
	die(json_encode($data));
}
//===[ NOLAG CSRF PROTECTION]=== COPY END HERE

function contains($haystack, $needle)
{
    return strpos($haystack, $needle) !== false;
}

if(!isset($_GET["id"]) || !isset($_GET["template"])){
	$data = [ 'success' => 0, 'msg' => 'Some parameters are missing' ];
	die(json_encode($data));
}
$id = $_GET["id"];
$vServer = getvServer($db,$id);
$service = getService($db,$vServer["serviceid"]);
$plan = getPlan($db,$service['planid']);
if($vServer['solusvm'] != -1){
  $vResponse = solusRebuild($vServer['solusvm'], $_GET['template']);
	$data = [ 'success' => 3, 'msg' =>  $vResponse->statusmsg ];
	die(json_encode($data));
}
if($vServer == null){
	$data = [ 'success' => 0, 'msg' => 'Could not find this server!' ];
	die(json_encode($data));
}
if(getRank() < 2){
if($vServer["username"] !== getUsername()){
	$data = [ 'success' => 0, 'msg' =>  $vResponse->statusmsg];
	die(json_encode($data));
}
}
$vResponse = solusCreate($plan['name'], $_GET['template']);
$status = $vResponse->status;
$statusCode = 0;
if($status == "success"){
	$statusCode = 1;
}
if($statusCode == 1){
	updateSolusId($db, $vServer['id'], $vResponse->vserverid);
	setServiceStatus($db, $service['id'], 2);
}
$data = [ 'success' => $statusCode, 'password' => $vResponse->rootpassword, 'msg' => $vResponse->statusmsg ];
die(json_encode($data));
?>
