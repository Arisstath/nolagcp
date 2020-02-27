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
$deleteItem = $cart->remove($_REQUEST['id']);
if($deleteItem){
	$data = [ 'success' => 1, 'msg' => 'Product has been removed.' ];
	die(json_encode($data));
} else {
	$data = [ 'success' => 0, 'msg' => 'Product has not been removed.' ];
	die(json_encode($data));
}
?>