<?php
require_once("../includes/config.php");
include("../includes/userutils.php");
include '../includes/security.php';

//ehm gather name and id

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


if(!isset($_GET["id"]) || !isset($_GET["plan"]) || !isset($_GET['buy'])){
	$data = [ 'success' => 0, 'msg' => 'Some parameters are missing' ];
	die(json_encode($data));
}
$id = $_GET["id"];
$server = getServer($db, $id);
if($server == null){
	$data = [ 'success' => 0, 'msg' => 'Could not find this server!' ];
	die(json_encode($data));
}
if($server["username"] !== getUsername()){
	$data = [ 'success' => 0, 'msg' => 'You do not have access to this server!' ];
	die(json_encode($data));
}
$buy = $_GET["buy"];
$plan = $_GET["plan"];
$currentService = getService($db, $server['serviceid']);

if($currentService['active'] != 1 && $currentService['active'] != 2){
	$data = [ 'success' => 0, 'msg' => 'This service is suspended or expired.'];
    die(json_encode($data));
}

$targetPlan = getPlan($db, $plan);
if(!$targetPlan){
	$data = [ 'success' => 0, 'msg' => 'The target plan could not be found.'];
    die(json_encode($data));
}
$currentPlan = getPlan($db, $currentService['planid']);

//Calculate total paid days
$now = new DateTime();
$expiration = new DateTime($currentService['expiration']);
$boughtTime = new DateTime($currentService['expiration']);
date_sub($boughtTime, new DateInterval("P30D"));


//echo("BoughtTime: " . $boughtTime->format("d-m-Y"));
//echo("<br>");
//echo("expiration: " . $expiration->format("d-m-Y"));
//echo("<br>");
//echo("now: " . $now->format("d-m-Y"));

$interval = $now->diff($boughtTime);
$usedDays = $interval->format('%a');
//echo("usedDays: " . $usedDays);
$perDay = $currentPlan['price'] / 30;
$paidAmount = $usedDays * $perDay;

$otherPlanDays = 30 - $usedDays;
$newPlanPerDay = $targetPlan['price'] /30;
$upgradeCost = $newPlanPerDay * $otherPlanDays;
$upgradeCost = floor($upgradeCost * 100) / 100;
if($upgradeCost < 0){ //you never know, maths can be bitch
	$data = [ 'success' => 0, 'msg' => 'There was a problem, report it to our discord. Problem identifier is: UPGRADE_NEGATIVE.'];
    die(json_encode($data));
}
if($buy == 0){
	$data = [ 'success' => 3, 'usedDays' => $usedDays, 'msg' => 'Upgrade cost is $' . $upgradeCost . "."];
	die(json_encode($data));
} else {
if(hasBalance($upgradeCost)){
	debuctBalance($db,$upgradeCost);
	$db->query("UPDATE mcservers SET ram=" . $targetPlan['ram'] . " WHERE id=" . $server['id']);
	$db->query("UPDATE services SET planid=" . $targetPlan['id'] . " WHERE id=" . $currentService['id']);
	$data = [ 'success' => 1, 'msg' => 'Thank you for upgrading. Please restart your server for RAM changes to be applied.'];
	die(json_encode($data));
} else {
	$data = [ 'success' => 0, 'msg' => 'You do not have enough balanace to perform this upgrade.'];
	die(json_encode($data));
}
}


?>