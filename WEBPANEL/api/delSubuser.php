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
	$data = [ 'success' => 0, 'msg' => 'You can\'t remove yourself as subuser!' ];
	die(json_encode($data));
}
$query = "SELECT count(*) FROM subusers WHERE username=:username AND serverid=:serverid";
	$query_params = array( ':username' => $subuser, ':serverid' => $id); 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
        $row = $stmt->fetchColumn();
if($row == 0){
	//doesnt exist
	$data = [ 'success' => 0, 'msg' => 'This user does not have access to your server!' ];
	die(json_encode($data));
}

//delete
$query = "DELETE FROM subusers WHERE username=:username AND serverid=:serverid";
	$query_params = array( ':username' => $subuser, ':serverid' => $id); 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
		$data = [ 'success' => 1, 'msg' => 'Subuser has been successfully deleted!' ];
	die(json_encode($data));
?>