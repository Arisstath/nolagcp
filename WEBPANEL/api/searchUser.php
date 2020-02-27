<?php
require_once("../includes/config.php");
require_once("../includes/userutils.php");

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

if(getRank() != 2){
	$data = [ 'success' => 0, 'msg' => 'You do not have access here.' ];
	die(json_encode($data));
}
if(empty($_GET["parameter"]) || empty($_GET["filter"])){
	$data = [ 'success' => 0, 'msg' => 'There are empty fields!' ];
	die(json_encode($data));
}

$parameter = $_GET["parameter"];
$filter = $_GET["filter"];

$query = "SELECT * FROM users WHERE email=:param"; 
if($filter == "username"){
	$query = "SELECT * FROM users WHERE username=:param"; 
}

	$query_params = array( ':param' => $parameter ); 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
        $row = $stmt->fetch(); 
		if(!$row){
			$data = [ 'success' => 0, 'msg' => 'Could not find any user!' ];
	        die(json_encode($data));
		}
		$data = [ 'success' => 1, 'url' => 'https://nolag.host/panel/admin_users?id='.$row["id"] ];
	        die(json_encode($data));
		
?>