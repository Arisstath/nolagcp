<?php
require_once("../includes/config.php");
include("../includes/userutils.php");
include '../includes/security.php';

$key = "u>M&3gPCUMnc['7S";

function postToDiscord($message)
{
    $data = array("content" => $message, "username" => "NoLagCP Admin");
    $curl = curl_init("https://discordapp.com/api/webhooks/341311116191858688/y2oNj1wdcxTcW9nXwkZajyBJcQKfW13vD3DSX8Rq2GKmKKQ7ur6lJe4SqE_rIzcT3kOg");
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    return curl_exec($curl);
}

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
$USER_AGENT = "NoLagCP";// Change this!
$REQUEST_URL = "https://api.spiget.org/v2/resources/" . $_GET['id'];

$ch = curl_init($REQUEST_URL);
curl_setopt($ch, CURLOPT_USERAGENT, $USER_AGENT); // Set User-Agent
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($ch);
$code = curl_getinfo($ch)["http_code"];
if ($code !== 200) {
    $data = [ 'success' => 0, 'msg' => 'Request was not valid.' ];
	die(json_encode($data));
}
curl_close($ch);

die($result);
?>