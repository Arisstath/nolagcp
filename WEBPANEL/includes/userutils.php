<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
//error_reporting(E_ALL);
require(__DIR__ . "/../vendor/autoload.php");

use GeoIp2\Database\Reader;

if(empty($_SESSION['user']))
    {
		//check if api key presents
		if(!empty($_GET['apikey'])){
			$apiKey = $_GET['apikey'];
			//Check
			$apiUser = getApiUser($db, $apiKey);
			if($apiUser){
				$_SESSION['user'] = $apiUser;
			} else {
				die("Invalid api key");
			}
		} else {
			header("Location: login?location=" . urlencode($_SERVER['REQUEST_URI']));
        die("Redirecting to login");
		}
        
    }
	if (empty($_SESSION['token'])) {
    if (function_exists('mcrypt_create_iv')) {
        $_SESSION['token'] = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
    } else {
        $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
    }
}


$reader = new Reader(__DIR__ . '/../maxmind/GeoLite2-City.mmdb');
$sessionID = session_id();
	$query = "REPLACE INTO activesessions(id,username,ip,country,useragent) VALUES (:id, :username, :ip, :country, :useragent)";
	$ip = $_SERVER['REMOTE_ADDR'];
	    $query_params = array( ':id' => $sessionID, ':username' => getUsername(), ':ip' => $ip, ':country' => 'DO-IT-YOURSELF', ':useragent' => $_SERVER['HTTP_USER_AGENT']);
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ }
		$sessiondb = getActiveSession($db, $sessionID);
		if($sessiondb['active'] == 0){
			$_SESSION = array();

           if (ini_get("session.use_cookies")) {
                 $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                   $params["path"], $params["domain"],
                       $params["secure"], $params["httponly"]
    );
}

session_destroy();
		}

$csrfToken = $_SESSION['token'];
	function getUsername() {
	return (htmlentities($_SESSION['user']['username'], ENT_QUOTES, 'UTF-8'));
}


	function getEmail() {
	return $_SESSION['user']['email'];
}
	$query = "SELECT * FROM users WHERE username=:username";
	$query_params = array( ':username' => getUsername() );
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
        $row = $stmt->fetch();

		//security reasons
		unset($row['salt']);
        unset($row['password']);

	    //set user's info
        $_SESSION['user'] = $row;

if(empty($_SESSION['2fa'])){
	if(getSecret() != null){
		if($pageTitle != "2FA Login"){
			if(!$bypass2fa){
			header("Location: 2falogin");
        die("Redirecting to 2falogin");
			}
		}
	}
}

function getVerified($db) {
    $username = (htmlentities($_SESSION['user']['username'], ENT_QUOTES, 'UTF-8'));
    $query = "SELECT * FROM users WHERE username=:username";
    $query_params = array( ':username' => $username );
    try {
        $stmt = $db->prepare($query);
        $result = $stmt->execute($query_params);
    }
    catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
    $row = $stmt->fetch();
    return (bool)$row['verified'];
}

function getBalance() {
	return($_SESSION['user']['balance']);
}
function getTokenCache() {
        return($_SESSION['user']['token']);
}

//$solusId = "u9p2ohTwOfuk1oTj8NS0g1Stus86Cs5Sji14GE9L";
//$solusKey = "YjBd4La1nT1uJIO52M1t5Uce438yw7alfBo9dpZr";
function solusCreate($plan,$template) {
        $url = "http://213.136.66.46:5353/api/admin";
        $postfields["id"] = "u9p2ohTwOfuk1oTj8NS0g1Stus86Cs5Sji14GE9L";
        $postfields["key"] = "YjBd4La1nT1uJIO52M1t5Uce438yw7alfBo9dpZr";
        $postfields["action"] = "vserver-create";
        $postfields["type"] = "openvz";
		$postfields["node"] = "EU8";
		$postfields["hostname"] = getUsername() . ".nolag.host";
		$postfields["password"] = generatePasswordd(20);
		$postfields["username"] = "nolagcp";
		$postfields["plan"] = $plan;
		$postfields["template"] = $template;
		$postfields["ips"] = "1";
		$postfields["rdtype"] = "json";
        // Send the query to the solusvm master
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . "/command.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Expect:"));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        $data = curl_exec($ch);
        curl_close($ch);

       $json = json_decode($data);
	   return $json;
}
function solusRebuild($id, $template) {
        $url = "http://213.136.66.46:5353/api/admin";
        $postfields["id"] = "u9p2ohTwOfuk1oTj8NS0g1Stus86Cs5Sji14GE9L";
        $postfields["key"] = "YjBd4La1nT1uJIO52M1t5Uce438yw7alfBo9dpZr";
        $postfields["action"] = "vserver-rebuild";
        $postfields["vserverid"] = $id;
        $postfields["template"] = $template;
		$postfields["rdtype"] = "json";
        // Send the query to the solusvm master
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . "/command.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Expect:"));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        $data = curl_exec($ch);
        curl_close($ch);

       $json = json_decode($data);
	   return $json;
}
function solusStop($id) {
        $url = "http://213.136.66.46:5353/api/admin";
        $postfields["id"] = "u9p2ohTwOfuk1oTj8NS0g1Stus86Cs5Sji14GE9L";
        $postfields["key"] = "YjBd4La1nT1uJIO52M1t5Uce438yw7alfBo9dpZr";
        $postfields["action"] = "vserver-shutdown";
        $postfields["vserverid"] = $id;
		$postfields["rdtype"] = "json";
        // Send the query to the solusvm master
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . "/command.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Expect:"));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        $data = curl_exec($ch);
        curl_close($ch);

       $json = json_decode($data);
	   return $json;
}
function solusRoot($id) {
        $url = "http://213.136.66.46:5353/api/admin";
        $postfields["id"] = "u9p2ohTwOfuk1oTj8NS0g1Stus86Cs5Sji14GE9L";
        $postfields["key"] = "YjBd4La1nT1uJIO52M1t5Uce438yw7alfBo9dpZr";
        $postfields["action"] = "vserver-rootpassword";
        $postfields["vserverid"] = $id;
		$postfields["rdtype"] = "json";
		$postfields["rootpassword"] = generatePasswordd(20);
        // Send the query to the solusvm master
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . "/command.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Expect:"));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        $data = curl_exec($ch);
        curl_close($ch);

       $json = json_decode($data);
	   return $json;
}
function solusRestart($id) {
        $url = "http://213.136.66.46:5353/api/admin";
        $postfields["id"] = "u9p2ohTwOfuk1oTj8NS0g1Stus86Cs5Sji14GE9L";
        $postfields["key"] = "YjBd4La1nT1uJIO52M1t5Uce438yw7alfBo9dpZr";
        $postfields["action"] = "vserver-reboot";
        $postfields["vserverid"] = $id;
		$postfields["rdtype"] = "json";
        // Send the query to the solusvm master
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . "/command.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Expect:"));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        $data = curl_exec($ch);
        curl_close($ch);

       $json = json_decode($data);
	   return $json;
}
function solusStart($id) {
        $url = "http://213.136.66.46:5353/api/admin";
        $postfields["id"] = "u9p2ohTwOfuk1oTj8NS0g1Stus86Cs5Sji14GE9L";
        $postfields["key"] = "YjBd4La1nT1uJIO52M1t5Uce438yw7alfBo9dpZr";
        $postfields["action"] = "vserver-boot";
        $postfields["vserverid"] = $id;
		$postfields["rdtype"] = "json";
        // Send the query to the solusvm master
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . "/command.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Expect:"));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        $data = curl_exec($ch);
        curl_close($ch);

       $json = json_decode($data);
	   return $json;
}
function solusInfo($id) {
        $url = "http://213.136.66.46:5353/api/admin";
        $postfields["id"] = "u9p2ohTwOfuk1oTj8NS0g1Stus86Cs5Sji14GE9L";
        $postfields["key"] = "YjBd4La1nT1uJIO52M1t5Uce438yw7alfBo9dpZr";
        $postfields["action"] = "vserver-infoall";
        $postfields["vserverid"] = $id;
		$postfields["rdtype"] = "json";
        // Send the query to the solusvm master
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . "/command.php");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Expect:"));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        $data = curl_exec($ch);
        curl_close($ch);

       $json = json_decode($data);
	   return $json;
}
function genPIN($db) {
	$pin = bin2hex(openssl_random_pseudo_bytes(4));
	$query = "UPDATE users SET spin=:token WHERE username=:username";
	$query_params = array( ':token' => $pin,':username' => getUsername() );
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
}

function generatePasswordd($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $count = mb_strlen($chars);

    for ($i = 0, $result = ''; $i < $length; $i++) {
        $index = rand(0, $count - 1);
        $result .= mb_substr($chars, $index, 1);
    }

    return $result;
}
function createSQL($db){
	//CREATE DATABASE IF NOT EXISTS musicDB
	$randomPass = generatePasswordd(15);
	$username = "cp"; 
    $password = "MArbmfg8pwKuUvUp"; 
    $host = "46.4.90.149"; 
    $dbname = "nolagcp"; 
    $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'); 
    try { $dbS = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $username, $password, $options); } 
    catch(PDOException $ex){ die("Failed to connect to the database: " . $ex->getMessage());} 
    $dbS->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
    $dbS->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); 
	
	try{
    $dbS->query("DROP USER 'nolagcp_" . getID() . "'@'%';");
	} catch( PDOException $e){
}
	try{
    $dbS->query("CREATE USER 'nolagcp_" . getID() . "'@'%' IDENTIFIED BY '" . $randomPass . "';");
	} catch( PDOException $e){
}
	try{
    $dbS->query("CREATE DATABASE IF NOT EXISTS `nolagcp_" . getID() . "`;");
	} catch( PDOException $e){
}
	try{
    $dbS->query("GRANT ALL PRIVILEGES on `nolagcp_" . getID() . "`.* TO 'nolagcp_" . getID() . "'@'%';");
	} catch( PDOException $e){
}
	setSQLPass($db, $randomPass);
}
function getID() {
	return($_SESSION['user']['id']);
}
function getDiscordID() {
	return($_SESSION['user']['discordid']);
}
function getPIN() {
	return($_SESSION['user']['spin']);
}
function getRank() {
	return($_SESSION['user']['rank']);
}
function getSecret() {
	return $_SESSION['user']['secret'];
}
function getNegafinity() {
	//null checkdate
	if($_SESSION['user']['negafinity'] == null){
		return null;
	} else {
		return $_SESSION['user']['negafinity'];
	}
}
function createNegafinity($db) {
	    $url = "https://6oa11yxbjb.execute-api.us-west-2.amazonaws.com/latest/referral/nolag/69603f7d-1cb3-40c9-a8c9-c2bf6b3c77cd";
        $ch = curl_init();

curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36");
$data = curl_exec ($ch);
curl_close ($ch);
		die($data);
		
	$query = "UPDATE users SET negafinity=:negafinity WHERE username=:username";
	$query_params = array( ':negafinity' => $pass, ':username' => getUsername());
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
}
function getSQLPass() {
	//null checkdate
	if($_SESSION['user']['mysqlpass'] == null){
		return null;
	} else {
		return Security::decrypt($_SESSION['user']['mysqlpass'], "u>M&3gPCUMnc['7S");
	}
}
function delSecret($db) {
	$query = "UPDATE users SET secret=:secret WHERE username=:username";
	$query_params = array( ':secret' => null, ':username' => getUsername());
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
}
function genSecret($db, $ga) {
	$secret = $ga->createSecret();
	$query = "UPDATE users SET secret=:secret WHERE username=:username";
	$query_params = array( ':secret' => $secret, ':username' => getUsername());
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
}

function setSQLPass($db, $pass) {
	$pass = Security::encrypt($pass, "u>M&3gPCUMnc['7S");
	$query = "UPDATE users SET mysqlpass=:mysqlpass WHERE username=:username";
	$query_params = array( ':mysqlpass' => $pass, ':username' => getUsername());
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
}
function hasVouched() {
	return($_SESSION['user']['vouched'] == 1);
}
function isSubuser($db, $serverid) {
	$query = "SELECT count(*) FROM subusers WHERE serverid=:serverid AND username=:username";
	$query_params = array( ':username' => getUsername(), ':serverid' => $serverid );
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
        $row = $stmt->fetchColumn();

	return $row != 0;
}
function getUnpaidInvoices($db) {

	$nRows = $db->query("SELECT count(*) FROM invoices WHERE username='" . getUsername() . "' AND paid=0")->fetchColumn();
    return ($nRows);
}
function getActiveServicesT($db) {

	$nRows = $db->query("SELECT count(*) FROM services WHERE active=1 OR active=2")->fetchColumn();
    return ($nRows);
}
function getDeployedServers($db) {

	$nRows = $db->query("SELECT count(*) FROM mcservers")->fetchColumn();
    return ($nRows);
}
function getTotalUsers($db) {

	$nRows = $db->query("SELECT count(*) FROM users")->fetchColumn();
    return ($nRows);
}
function getActiveServices($db) {

	$nRows = $db->query("SELECT count(*) FROM services WHERE username='" . getUsername() . "' AND active=1 OR active=2 AND username='" . getUsername() . "'")->fetchColumn();
    return ($nRows);
}
function debuctBalance($db, $money) {
	$newbalance = getBalance() - $money;
	setBalance($db, $newbalance);
}
function addBalance($db, $money) {
	$newbalance = getBalance() + $money;
	setBalance($db, $newbalance);
}
function hasBalance($money) {
	return getBalance() >= $money;
}
function setBalance($db, $money) {
	$query = "UPDATE users SET balance=:balance WHERE username=:username";
	$query_params = array( ':balance' => $money, ':username' => getUsername());
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
}
function getNextPort($db){
	$nRows = $db->query("
	SELECT MIN(t1.port + 1) AS nextID
FROM mcservers t1
   LEFT JOIN mcservers t2
       ON t1.port + 1 = t2.port
WHERE t2.port IS NULL")->fetchColumn();
	return ($nRows);
}
function queueServer($db, $name, $port, $ip, $serviceid, $node, $ram, $username, $ftpuser, $ftppass) {
	$query = "INSERT INTO mcserversqueue(name,port,ip,serviceid,node,ram,username, ftpusername, ftppass) VALUES(:name,:port,:ip,:serviceid,:node,:ram,:username,:ftpuser,:ftppass)";

	$query_params = array( ':name' => $name, ':port' => $port, ':ip' => $ip, ':serviceid' => $serviceid, ':node' => $node, ':ram' => $ram, ':username' => $username, ':ftpuser' => $ftpuser, ':ftppass' => $ftppass);
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
}
function createvServer($db,$serviceid) {
	$query = "INSERT INTO vServers(solusvm,username,serviceid) VALUES(:solusvm,:username,:serviceid)";

	$query_params = array(':serviceid' => $serviceid, ':username' => getUsername(), ':solusvm' => -1);
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
}
function createServer($db, $name, $port, $ip, $serviceid, $node, $ram, $username, $ftpuser, $ftppass) {
	$query = "INSERT INTO mcservers(name,port,ip,serviceid,node,ram,username, ftpusername, ftppass) VALUES(:name,:port,:ip,:serviceid,:node,:ram,:username,:ftpuser,:ftppass)";

	$query_params = array( ':name' => $name, ':port' => $port, ':ip' => $ip, ':serviceid' => $serviceid, ':node' => $node, ':ram' => $ram, ':username' => $username, ':ftpuser' => $ftpuser, ':ftppass' => $ftppass);
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
}
function markPaid($db, $invoice) {
	$query = "UPDATE invoices SET paid=1 WHERE id=:id";
	$query_params = array( ':id' => $invoice);
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
}
function setServiceExpiration($db, $id, $days) {
	$query = "UPDATE services SET expiration= NOW() + INTERVAL " . $days . " DAY WHERE id=:id";
	$query_params = array( ':id' => $id);
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
}
function updateSolusId($db, $sid, $solusid) {
	$query = "UPDATE vServers SET solusvm=:solusid WHERE id=:sid";
	$query_params = array( ':sid' => $sid, ':solusid' => $solusid);
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
}
function setServiceStatus($db, $id, $status) {
	$query = "UPDATE services SET active=:paid WHERE id=:id";
	$query_params = array( ':id' => $id, ':paid' => $status);
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
}
function logAdmin($db, $action, $description){
	$query = "INSERT INTO adminlogs (username,action,description) VALUES (:username,:action,:description)";
	$query_params = array( ':username' => getUsername(), ':action' => $action, 'description' => $description );
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }

}
function getActiveSession($db, $id) {
	$query = "SELECT * FROM activesessions WHERE id=:id";
	$query_params = array( ':id' => $id );
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
        $row = $stmt->fetch();
			return $row;
}
function getToken($db, $username) {
	$query = "SELECT * FROM users WHERE username=:username";
	$query_params = array( ':username' => $username );
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
        $row = $stmt->fetch();
		//security is number one priority
		return $row['token'];
}
function getInvoice($db, $id) {
	$query = "SELECT * FROM invoices WHERE id=:id";
	$query_params = array( ':id' => $id );
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
        $row = $stmt->fetch();
		return $row;
}
function getUser($db, $id) {
	$query = "SELECT * FROM users WHERE id=:id";
	$query_params = array( ':id' => $id );
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
        $row = $stmt->fetch();
		//security is number one priority
		unset($row['salt']);
        unset($row['password']);
		return $row;
}
function getServerByServiceId($db, $id) {
	$query = "SELECT * FROM mcservers WHERE serviceid=:id";
	$query_params = array( ':id' => $id );
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
        $row = $stmt->fetch();

		return $row;
}
function getvServerByService($db, $id) {
	$query = "SELECT * FROM vServers WHERE serviceid=:id";
	$query_params = array( ':id' => $id );
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
        $row = $stmt->fetch();

		return $row;
}
function getvServer($db, $id) {
	$query = "SELECT * FROM vServers WHERE id=:id";
	$query_params = array( ':id' => $id );
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
        $row = $stmt->fetch();

		return $row;
}
function getApiUser($db, $key) {
	$query = "SELECT * FROM users WHERE apikey=:key";
	$query_params = array( ':key' => $key );
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
        $row = $stmt->fetch();
		if($row['apikey'] == null || $row['apikey'] == ""){
			return null;
		}
		return $row;
}
function getServer($db, $id) {
	$query = "SELECT * FROM mcservers WHERE id=:id";
	$query_params = array( ':id' => $id );
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
        $row = $stmt->fetch();

		return $row;
}
function getService($db, $id) {
	$query = "SELECT * FROM services WHERE id=:id";
	$query_params = array( ':id' => $id );
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
        $row = $stmt->fetch();

		return $row;
}
function getPlan($db, $id) {
	$query = "SELECT * FROM plans WHERE id=:id";
	$query_params = array( ':id' => $id );
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
        $row = $stmt->fetch();

		return $row;
}
?>
