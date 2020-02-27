<?php
require_once("../includes/config.php");
include("../includes/userutils.php");
include '../includes/security.php';
//===[ NOLAG CSRF PROTECTION]=== COPY FROM HERE
if (!empty($_POST['csrf'])) {
    if (!hash_equals($_SESSION['token'], $_POST['csrf'])) {
         $data = [ 'success' => 0, 'msg' => 'CSRF Token is invalid.' ];
	      die(json_encode($data));
    }
} else {
	$data = [ 'success' => 0, 'msg' => 'CSRF Token is missing from your request.' ];
	die(json_encode($data));
}
//===[ NOLAG CSRF PROTECTION]=== COPY END HERE
$key = "u>M&3gPCUMnc['7S";

$ips = array();
$ips["EU1"] = "46.4.90.149";
$ips["EU2"] = "79.143.189.31";
$ips["EU3"] = "213.136.66.46";
$ips["EU4"] = "79.143.179.235";
$ips["EU5"] = "193.37.152.104";
$ips["EU6"] = "79.143.181.112";
$ips["EU7"] = "5.189.188.249";
$ips["EU8"] = "5.189.171.213";
$ips["EU9"] = "5.189.174.239";
$ips["EU10"] = "173.212.244.242";
$ips["DE01"] = "89.163.148.70";
$ips["DE02"] = "89.163.148.170";
$ips["DA02"] = "45.35.32.3";
$ips["DA03"] = "45.35.32.4";
$ips["US1"] = "10.16.0.102";
$ips["US2"] = "70.36.112.152";

function generatePassword($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $count = mb_strlen($chars);

    for ($i = 0, $result = ''; $i < $length; $i++) {
        $index = rand(0, $count - 1);
        $result .= mb_substr($chars, $index, 1);
    }

    return $result;
}

function getAvailableServer($locationi) {
$servers = array();

ini_set('default_socket_timeout', 5); //we only want fast servers

//Germany
if($locationi == 1){
//EU1
$json = file_get_contents("https://eu1.mcsrv.top/fda875524d35567786690158966be5f667b70e06/stats");
$parsed = json_decode($json);
$ram = $parsed->allocatedram + (4 * 1024);
$servers["EU1"] = $ram;
/*
//EU2
$json = file_get_contents("https://eu2.mcsrv.top/fda875524d35567786690158966be5f667b70e06/stats");
$parsed = json_decode($json);
$ram = $parsed->allocatedram + (4 * 1024);
$servers["EU2"]= $ram;

//EU3
$json = file_get_contents("https://eu3.mcsrv.top/fda875524d35567786690158966be5f667b70e06/stats");
$parsed = json_decode($json);
$ram = $parsed->allocatedram + (4 * 1024);
$servers["EU3"]= $ram;

//EU4
$json = file_get_contents("https://eu4.mcsrv.top/fda875524d35567786690158966be5f667b70e06/stats");
$parsed = json_decode($json);
$ram = $parsed->allocatedram + (4 * 1024);
$servers["EU4"]= $ram;

//EU5
$json = file_get_contents("https://eu5.mcsrv.top/fda875524d35567786690158966be5f667b70e06/stats");
$parsed = json_decode($json);
$ram = $parsed->allocatedram + (4 * 1024);
$servers["EU5"]= $ram;
*/
//EU6
$json = file_get_contents("https://eu6.mcsrv.top/fda875524d35567786690158966be5f667b70e06/stats");
$parsed = json_decode($json);
$ram = $parsed->allocatedram + (4 * 1024);
$servers["EU6"]= $ram;

//EU7
$json = file_get_contents("https://eu7.mcsrv.top/fda875524d35567786690158966be5f667b70e06/stats");
$parsed = json_decode($json);
$ram = $parsed->allocatedram + (4 * 1024);
$servers["EU7"]= $ram;

//EU8
$json = file_get_contents("https://eu8.mcsrv.top/fda875524d35567786690158966be5f667b70e06/stats");
$parsed = json_decode($json);
$ram = $parsed->allocatedram + (4 * 1024);
$servers["EU8"]= $ram;

//EU9
$json = file_get_contents("https://eu9.mcsrv.top/fda875524d35567786690158966be5f667b70e06/stats");
$parsed = json_decode($json);
$ram = $parsed->allocatedram + (4 * 1024);
$servers["EU9"]= $ram;

//DE01 - sir aris you forgot de01
$json = file_get_contents("https://de01.mcsrv.top/fda875524d35567786690158966be5f667b70e06/stats");
$parsed = json_decode($json);
$ram = $parsed->allocatedram + (4 * 1024);
$servers["DE01"]= $ram;

//DE01 - sir aris you forgot de01
$json = file_get_contents("https://de02.mcsrv.top/fda875524d35567786690158966be5f667b70e06/stats");
$parsed = json_decode($json);
$ram = $parsed->allocatedram + (4 * 1024);
$servers["DE02"]= $ram;

}

/*
//EU10
$json = file_get_contents("https://eu10.mcsrv.top/fda875524d35567786690158966be5f667b70e06/stats");
$parsed = json_decode($json);
$ram = $parsed->allocatedram + (4 * 1024);
$servers["EU10"] = $ram;

*/
//los angeles
if($locationi == 2){

//US1
$json = file_get_contents("https://us1.mcsrv.top/fda875524d35567786690158966be5f667b70e06/stats");
$parsed = json_decode($json);
$ram = $parsed->allocatedram;
$servers["US1"] = $ram;

//US2
$json = file_get_contents("https://us2.mcsrv.top/fda875524d35567786690158966be5f667b70e06/stats");
$parsed = json_decode($json);
$ram = $parsed->allocatedram;
$servers["US2"] = $ram;
}
//dallas
if($locationi == 3){
//DA02
$json = file_get_contents("https://da02.mcsrv.top/fda875524d35567786690158966be5f667b70e06/stats");
$parsed = json_decode($json);
$ram = $parsed->allocatedram;
$servers["DA02"] = $ram;

//DA03
$json = file_get_contents("https://da03.mcsrv.top/fda875524d35567786690158966be5f667b70e06/stats");
$parsed = json_decode($json);
$ram = $parsed->allocatedram;
$servers["DA03"] = $ram;
}

$index = array_search(min($servers), $servers);
return $index;
}


//createServer($db, getUsername() . "'s server", getNextPort($db), '79.143.179.235', 3, 'EU4', 512, getUsername(), 'testlfmadsf_fasfawf_fa2', Security::encrypt(generatePassword(), $key));
//die();
//die(getNextPort($db));
//gather invoice info
$query = "SELECT * FROM invoices WHERE id=:id";
	$query_params = array( ':id' => $_POST['id'] );
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
        $row = $stmt->fetch();
		$id = $row['id'];
		$amount = $row['amount'];
		$products = $row['products'];
		$paid = $row['paid'];
		$user = $row["username"];
		if($paid == 1) {
			$data = [ 'success' => 0, 'msg' => 'This invoice is already paid!' ];
			die(json_encode($data));
		}
		if($paid == 2) {
			$data = [ 'success' => 0, 'msg' => 'This invoice has been expired!' ];
			die(json_encode($data));
		}
		if($user != getUsername()) {
			$id = -1;
			$amount = -1;
			$products = "";
			$paid = 1;
		}
		if($user != getUsername()) {
			$data = [ 'success' => 0, 'msg' => 'You do not have permission to pay this invoice.' ];
	        die(json_encode($data));
		}
		//calculate total
		$total = $amount;

if(!hasBalance($total)) {
	$data = [ 'success' => 0, 'msg' => 'Your balance is not enough to pay this invoice :(' ];
	die(json_encode($data));
} else {
	debuctBalance($db,$total);
	markPaid($db, $id);

	//Start processing the order!

	//First we need to check if service is unpaid
if (strpos($products, ',') !== false) {
                                                    $cats = explode(",", $products);
                                                foreach($cats as $cat) {
                                                    $rowS = getService($db,$cat);
													$planid = $rowS['planid'];
													$row = getPlan($db, $planid);

													$desc = $row['description'];
													$price = $row['price'];
													$ram = $row['ram'];
													setServiceExpiration($db, $rowS['id'], "30");
													if($rowS['active'] == 3){
														setServiceStatus($db, $rowS['id'], 2);
													}
													if($rowS['active'] == 4){
														setServiceStatus($db, $rowS['id'], 2);
													}
													   //Seperate VPS from Minecraft
														if($rowS['active'] == 0) { //0: Service is not paid, we mark it paid.
														if($row['type'] == "MINECRAFT"){


														setServiceStatus($db, $rowS['id'], 1);
														$server = getAvailableServer($rowS['location']);
														$ip = $ips[$server];
														//configure a new minecraft server
														//function createServer($db, $name, $port, $ip, $serviceid, $node, $ram, $username, $ftpuser, $ftppass) {
														createServer($db, getUsername() . "'s server", getNextPort($db), $ip, $rowS['id'], $server, $ram, getUsername(), getUsername() . "_" . $rowS["id"], Security::encrypt(generatePassword(), $key));
														} else {
															createvServer($db, $rowS["id"]);
														}
													}
												}


                                            }
                                                 else {
													$rowS = getService($db,$products);
													//$rowS = getService($db,$);
													$planid = $rowS['planid'];
													//var_dump($row);
													$row = getPlan($db, $planid);

													$desc = $row['description'];
													$price = $row['price'];
													$ram = $row['ram'];

													setServiceExpiration($db, $rowS['id'], "30");
													if($rowS['active'] == 3){
														setServiceStatus($db, $rowS['id'], 2);
													}
													if($rowS['active'] == 4){
														setServiceStatus($db, $rowS['id'], 2);
													}
													if($rowS['active'] == 0) { //0: Service is not paid, we mark it paid.
														setServiceStatus($db, $rowS['id'], 1);
														if($row['type'] == "MINECRAFT"){
														$server = getAvailableServer($rowS['location']);
														$ip = $ips[$server];
														//configure a new minecraft server
														//function createServer($db, $name, $port, $ip, $serviceid, $node, $ram, $username, $ftpuser, $ftppass) {
														createServer($db, getUsername() . "'s server", getNextPort($db), $ip, $rowS['id'], $server, $ram, getUsername(), getUsername() . "_" . $rowS["id"], Security::encrypt(generatePassword(), $key));
														} else {
															createvServer($db, $rowS["id"]);
														}
													}
												}

	$data = [ 'success' => 1, 'msg' => 'Thank you for paying!' ];
	die(json_encode($data));
}
?>
