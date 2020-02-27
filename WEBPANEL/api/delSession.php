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

if(!isset($_GET["id"])){
	$data = [ 'success' => 0, 'msg' => 'Some parameters are missing.' ];
	die(json_encode($data));
}
$server = getActiveSession($db, $id);
if($server == null){
	$data = [ 'success' => 0, 'msg' => 'This session ID could not be found.' ];
	die(json_encode($data));
}
if($server["username"] !== getUsername()){
	$data = [ 'success' => 0, 'msg' => 'You do not have access to this session.' ];
	die(json_encode($data));
}
//just drop the session
$query = "DELETE FROM sessions WHERE id = :id LIMIT 1"; 
	    $query_params = array( ':id' => $id ); 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex){  } 
$query = "DELETE FROM activesessions WHERE id = :id LIMIT 1"; 
	    $query_params = array( ':id' => $id ); 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex){  } 
$data = [ 'success' => 1, 'msg' => 'This session has been successfully destroyed.' ];
	die(json_encode($data));
?>