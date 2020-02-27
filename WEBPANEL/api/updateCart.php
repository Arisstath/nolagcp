<?php
require_once("../includes/config.php");
include("../includes/userutils.php");
include '../classes/Cart.php';

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

$cart = new Cart;
if(empty($_GET["id"])){
	$data = [ 'success' => 0, 'msg' => 'Some parameters are missing.' ];
	die(json_encode($data));
}
if(empty($_GET["qty"])){
	$data = [ 'success' => 0, 'msg' => 'Some parameters are missing.' ];
	die(json_encode($data));
}
if($_GET["qty"] < 1){
	//suspicious shit
	$data = [ 'success' => 0, 'msg' => '[BotStack FW]: Your request has been blocked because it has been flagged by INVALID_BOUNDS filter.' ];
	die(json_encode($data));
}
if($_GET["qty"] >= 3){
	//suspicious shit
	$data = [ 'success' => 0, 'msg' => 'Maximum quantity per item is 3' ];
	die(json_encode($data));
}
 $itemData = array(
            'rowid' => $_REQUEST['id'],
            'qty' => $_REQUEST['qty']
        );
        $updateItem = $cart->update($itemData);
if($updateItem){
	$data = [ 'success' => 1, 'msg' => 'Product has been updated.' ];
	die(json_encode($data));
} else {
	$data = [ 'success' => 0, 'msg' => 'Product has not been updated.' ];
	die(json_encode($data));
}
?>