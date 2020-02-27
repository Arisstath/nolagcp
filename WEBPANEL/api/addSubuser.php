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


if(!isset($_GET["id"]) || !isset($_GET["username"])){
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
$subuser = $_GET["username"];
if($subuser === getUsername()){
	$data = [ 'success' => 0, 'msg' => 'You can\'t add yourself as subuser!' ];
	die(json_encode($data));
}
$query = "SELECT count(*) FROM users WHERE username=:username";
	$query_params = array( ':username' => $subuser); 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
        $row = $stmt->fetchColumn();
if($row == 0){
	//doesnt exist
	$data = [ 'success' => 0, 'msg' => 'This user is not registered in NoLagCP!' ];
	die(json_encode($data));
}
//check if user is registered on nolag
//check if already a subuser exists with same username and for same server id
$query = "SELECT count(*) FROM subusers WHERE serverid=:serverid AND username=:username";
	$query_params = array( ':username' => $subuser, ':serverid' => $id ); 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
        $row = $stmt->fetchColumn();
if($row != 0){
	//exists already
	$data = [ 'success' => 0, 'msg' => 'This user has already access in your server!' ];
	die(json_encode($data));
}
$query = "INSERT INTO subusers(username,serverid) VALUES (:username,:serverid)";
	$query_params = array( ':username' => $subuser, ':serverid' => $id); 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
		$data = [ 'success' => 1, 'msg' => 'Subuser has been successfully added!' ];
	die(json_encode($data));
?>