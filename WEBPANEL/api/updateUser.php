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
if(!isset($_GET["id"]) || !isset($_GET["email"]) || !isset($_GET["balance"])){
	$data = [ 'success' => 0, 'msg' => 'Some parameters are missing' ];
	die(json_encode($data));
}
//ehm gather name and id
$id = $_GET["id"];
$newEmail = $_GET["email"];
$newBalance = $_GET["balance"];
$targetUser = getUser($db,$id);
if(!$targetUser){
	$data = [ 'success' => 0, 'msg' => 'User could not be found.' ];
	die(json_encode($data));
}
//some security checks, not allowed to give more than 10

// Try to convert the string to a float
$floatVal = floatval($newBalance);
// If the parsing succeeded and the value is not equivalent to an int
if(!$floatVal && intval($floatVal) == $floatVal)
{
   $data = [ 'success' => 0, 'msg' => 'This is not a valid balance.' ];
	die(json_encode($data));
}

if($newBalance - $targetUser["balance"] >= 100){
	logAdmin($db, "ACTION_DEMOTE", "Tried to set balance of user " . $targetUser["username"] . " from " . $targetUser["balance"] . " to " . $newBalance);
	$query = "UPDATE users SET rank=0 WHERE username=:username"; 
	$query_params = array( ':username' => getUsername()); 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
	 $data = [ 'success' => 0, 'msg' => 'Abuse has been detected. You have been demoted.' ];
	die(json_encode($data));
}
if($newBalance - $targetUser["balance"] >= 10){
	logAdmin($db, "ACTION_BLOCK", "Tried to set balance of user " . $targetUser["username"] . " from " . $targetUser["balance"] . " to " . $newBalance);
	 $data = [ 'success' => 0, 'msg' => 'You are not allowed to give that much of money. You have been logged.' ];
	die(json_encode($data));
}

//calculate differences
if($newEmail !== $targetUser["email"]){
	logAdmin($db, "UPDATE_USER", "Updated e-mail of user " . $targetUser["username"] . " from " . $targetUser["email"] . " to " . $newEmail);
}
if($newBalance != $targetUser["balance"]){
	logAdmin($db, "UPDATE_USER", "Updated balance of user " . $targetUser["username"] . " from " . $targetUser["balance"] . " to " . $newBalance);
}

$query = "UPDATE users SET email=:email,balance=:balance WHERE id=:id"; 
	$query_params = array( ':id' => $id,':email' => $newEmail, ':balance' => $newBalance); 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
		$data = [ 'success' => 1, 'msg' => 'User has been successfully updated.' ];
	die(json_encode($data));
?>