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

if(getSecret() != null){
	//disable 2fa
	delSecret($db);
	$data = [ 'success' => 1, 'msg' => '2FA has been deactivated.' ];
	die(json_encode($data));
}
genSecret($db, $ga);
$_SESSION['2fa'] = true;
$data = [ 'success' => 1, 'msg' => '2FA has been activated, do not forget to configure it.' ];
	die(json_encode($data));
?>