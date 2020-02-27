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

function contains($haystack, $needle)
{
    return strpos($haystack, $needle) !== false;
}

if(!isset($_GET["id"])){
	$data = [ 'success' => 0, 'msg' => 'Some parameters are missing' ];
	die(json_encode($data));
}
$id = $_GET["id"];
$server = getServer($db, $id);
if($server == null){
	$data = [ 'success' => 0, 'msg' => 'Could not find this server!' ];
	die(json_encode($data));
}
if(getRank() < 2){
if($server["username"] !== getUsername()){
	if(!isSubuser($db, $server['id'])){
		$data = [ 'success' => 0, 'msg' => 'You do not have access to this server!' ];
	    die(json_encode($data));
	}
}
}
$ftphost = $server['ip'];
$ftpusr = $server['ftpusername'];
$ftppass = Security::decrypt($server['ftppass'], $key);

$filename = "ftp://" . $ftpusr . ":" . $ftppass . "@" . $ftphost . ":1234/nolagcp/output.log";
//$handle = fopen($filename, "r");
if($node == "US1"){
	die("Logs are not available due to heavy server load.");
} else {
$contents = @readfile($filename);
}
foreach(preg_split("/((\r?\n)|(\r\n?))/", $contents) as $line){
	/*
   $color = "";
   if($line == null || $line === ""){
	  // continue;
   }
   if(contains($line,"ERROR]:")){
	   $color = "red";
   }
   if(contains($line,"]: Done (")){
	   $color = "lime";
   }
   if(contains($line,"WARN]:")){
	   $color = "yellow";
   }
   $line = <<<line
   <font color="$color">$line</font>
line;
*/
  // echo ($line . "\n");
} 
//fclose($handle);
?>