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
$randompass = generatePassword();
$password = Security::encrypt($randompass, $key);
$query = "UPDATE mcservers SET ftppass=:ftppass WHERE id=".$id; 
	$query_params = array( ':ftppass' => $password); 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
		$data = [ 'success' => 1, 'msg' => 'New password has been successfully generated!','password' => $randompass];
	die(json_encode($data));
	
	
	
	
	function generatePassword($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $count = mb_strlen($chars);

    for ($i = 0, $result = ''; $i < $length; $i++) {
        $index = rand(0, $count - 1);
        $result .= mb_substr($chars, $index, 1);
    }

    return $result;
}
?>