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

$planID = $_GET["id"];
$row = getPlan($db, $planID);
if($row == null){
	$data = [ 'success' => 0, 'msg' => 'Product could not be found.' ];
die(json_encode($data));
}
 $itemData = array(
            'id' => $row['id'],
            'name' => $row['description'],
            'price' => $row['price'],
            'qty' => 1
        );
$insertItem = $cart->insert($itemData);
$data = [ 'success' => 1, 'msg' => 'Product has been added to the cart.' ];
die(json_encode($data));
?>