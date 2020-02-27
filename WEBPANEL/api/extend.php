<?php
require_once("../includes/config.php");
include("../includes/userutils.php");
include '../includes/security.php';

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

if(getRank() < 2){
	$data = [ 'success' => 0, 'msg' => 'You do not have access here.' ];
	die(json_encode($data));
}
if(!isset($_GET["id"])){
	$data = [ 'success' => 0, 'msg' => 'Some parameters are missing' ];
	die(json_encode($data));
}
//ehm gather name and id
$id = $_GET["id"];
$targetServer = getServer($db,$id);
if(!$targetServer){
	$data = [ 'success' => 0, 'msg' => 'Server could not be found.' ];
	die(json_encode($data));
}
$serviceid = getService($targetServer["serviceid"]);

$query = "UPDATE services SET "; 
	$query_params = array( ':id' => $id,':email' => $newEmail, ':balance' => $newBalance); 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
		$data = [ 'success' => 1, 'msg' => 'Service has been successfully extended.' ];
	die(json_encode($data));
?>