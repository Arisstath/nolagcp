<?php
require_once("../includes/config.php");
include("../includes/userutils.php");
include '../includes/security.php';

$key = "u>M&3gPCUMnc['7S";

function postToDiscord($message)
{
    $data = array("content" => $message, "username" => "NoLagCP Admin");
    $curl = curl_init("https://discordapp.com/api/webhooks/341311116191858688/y2oNj1wdcxTcW9nXwkZajyBJcQKfW13vD3DSX8Rq2GKmKKQ7ur6lJe4SqE_rIzcT3kOg");
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    return curl_exec($curl);
}

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
//ehm gather name and id
$id = $_GET["id"];
$server = getServer($db, $id);
if(!isset($_GET["id"])){
	$data = [ 'success' => 0, 'msg' => 'Some parameters are missing' ];
	die(json_encode($data));
}
if($server == null){
	$data = [ 'success' => 0, 'msg' => 'Could not find this server!' ];
	die(json_encode($data));
}
if($server["username"] !== getUsername()){
	if(getRank() < 2){
	$data = [ 'success' => 0, 'msg' => 'You do not have access to this server!' ];
	die(json_encode($data));
	}
}
if($server["eligible"] == 1){
	$data = [ 'success' => 0, 'msg' => 'You are not eligible for this offer.'];
	die(json_encode($data));
}
$query = "UPDATE mcservers SET eligible=:eligible WHERE id=:id";
	$query_params = array( ':eligible' => 1, ':id' => $_GET['id']); 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
		if($_GET['redeem'] == 1){
			postToDiscord(getUsername() . " accepted for server #" . $server['id']);
		} else {
			postToDiscord(getUsername() . " declined for server #" . $server['id']);
		}
		
		$data = [ 'success' => 1, 'msg' => 'You have successfully opted out from this offer.'];
		die(json_encode($data));
?>