
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
$products = "";
$location = $_GET["location"];

$purchaseEnabled = file_get_contents("https://www.nolag.host/panel/api/enabled.txt");
if (!($purchaseEnabled == "1")) {
	$data = [ 'success' => 0, 'msg' => 'Purchases are currently disabled, check back later!' ];
	      die(json_encode($data));
}
if($cart->total_items() > 0){
	$locationi = 1;
	if($location === "kansas"){
		$locationi = 2;
	}
	if($location === "dallas"){
		$locationi = 3;
	}
	$cartItems = $cart->contents();
	$totalQuantity = 0;
	foreach($cartItems as $item){
		$totalQuantity++;
	}
	if($totalQuantity > 3){
		$data = [ 'success' => 0, 'msg' => 'You can\'t have more than 3 items in your cart.' ];
	die(json_encode($data));
		}
	foreach($cartItems as $item){
		$plan = getPlan($db,$item["id"]);
		
		for ($i=0; $i<(int)$item["qty"]; $i++) {
			//create service
			$query = "INSERT INTO services(username,planid,expiration,active,location) VALUES(:username,:planid,'2020-04-06 00:00:00', 0, :location)";
	$query_params = array( ':username' => getUsername(), ':planid' => $plan["id"], ':location' => $locationi); 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
			$lastid = $db->lastInsertId();
           $products .= $lastid . ",";
          }
		
		//echo($item["id"] . "<br>");
	}
	$price = $cart->total();
	$products = substr_replace($products, "", -1);
	
	$db->query("INSERT INTO invoices(username,products,amount,discount,paid,date,due) VALUES('" . getUsername() . "','" . $products . "'," . $price . ",0.00,0,NOW(),NOW() + INTERVAL 7 DAY)");
	$lastid = $db->lastInsertId();
	$cart->destroy();
	$data = [ 'success' => 1, 'msg' => 'Invoice has been generated!', 'url' => 'invoice?id=' . $lastid ];
	die(json_encode($data));
}
?>
