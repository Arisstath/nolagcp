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

//ehm gather name and id
$id = $_GET["id"];
$server = getServer($db, $id);
if(!isset($_GET["id"]) || !isset($_GET["name"])){
	$data = [ 'success' => 0, 'msg' => 'Some parameters are missing' ];
	die(json_encode($data));
}
if($server == null){
	$data = [ 'success' => 0, 'msg' => 'Could not find this server!' ];
	die(json_encode($data));
}
if($server["username"] !== getUsername()){
	$data = [ 'success' => 0, 'msg' => 'You do not have access to this server!' ];
	die(json_encode($data));
}
$name = $_GET["name"];
$query = "UPDATE mcservers SET name=:name WHERE id=:id";
	$query_params = array(':id' => $id, ':name' => $name); 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
		$data = [ 'success' => 1, 'msg' => 'Server name has been successfully updated!' ];
	die(json_encode($data));
?>