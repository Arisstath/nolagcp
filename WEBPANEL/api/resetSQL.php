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
if(getSQLPass() == null){
	$data = [ 'success' => 0, 'msg' => 'You don\'t own any MySQL database!' ];
	die(json_encode($data));
}
if(getActiveServices($db) <= 0){
	$data = [ 'success' => 0, 'msg' => 'You do not have any active services with us, please order one!' ];
	die(json_encode($data));
}
$randompass = generatePassword();
$password = Security::encrypt($randompass, $key);
//SET PASSWORD FOR 'jeffrey'@'localhost' = PASSWORD('cleartext password');
try{
    $db->query("SET PASSWORD FOR 'nolagcp_" . getID() . "'@'%' = PASSWORD('" . $randompass . "');");
	} catch( PDOException $e){
		die("Failed to run query: " . $ex->getMessage());
}
setSQLPass($db, $randompass);
		$data = [ 'success' => 1, 'msg' => 'MySQL password has been successfully changed.','password' => $randompass];
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