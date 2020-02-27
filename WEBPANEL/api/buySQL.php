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

if(getSQLPass() != null){
	$data = [ 'success' => 0, 'msg' => 'You already own a MySQL database!' ];
	die(json_encode($data));
}
if(getActiveServices($db) <= 0){
	$data = [ 'success' => 0, 'msg' => 'You do not have any active services with us, please order one!' ];
	die(json_encode($data));
}
if(!hasBalance(1.00)){
	$data = [ 'success' => 0, 'msg' => 'Your balance is not enough to buy a MySQL Database.' ];
	die(json_encode($data));
}
debuctBalance($db, 1.00);
//generate
createSQL($db);
$data = [ 'success' => 1, 'msg' => 'A new MySQL Database has been created!' ];
	die(json_encode($data));
?>