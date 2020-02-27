<?php
require_once("../includes/config.php");
$bypass2fa = true;
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

if(getSecret() == null){
	$data = [ 'success' => 0, 'msg' => 'You do not have 2FA enabled.' ];
	die(json_encode($data));
}
$checkResult = $ga->verifyCode(getSecret(), $_GET['code'], 2);
if($checkResult){
	$_SESSION['2fa'] = true;
	$data = [ 'success' => 1, 'msg' => 'Thank you for confirming your identity.' ];
	die(json_encode($data));
} else {
	$data = [ 'success' => 0, 'msg' => 'That is not a valid code.' ];
	die(json_encode($data));
}

?>