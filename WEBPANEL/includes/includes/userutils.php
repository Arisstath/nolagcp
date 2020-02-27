<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require(__DIR__ . "/../vendor/autoload.php");
use GeoIp2\Database\Reader;

if(empty($_SESSION['user'])) 
    {
        header("Location: login?location=" . urlencode($_SERVER['REQUEST_URI']));
        die("Redirecting to login"); 
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
			

function getBalance() {
	return($_SESSION['user']['balance']);
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
	$randomPass = generatePasswordd();
	
	try{
    $db->query("DROP USER 'nolagcp_" . getID() . "'@'%';");
	} catch( PDOException $e){
}
	try{
    $db->query("CREATE USER 'nolagcp_" . getID() . "'@'%' IDENTIFIED BY '" . $randomPass . "';");
	} catch( PDOException $e){
}
	try{
    $db->query("CREATE DATABASE IF NOT EXISTS `nolagcp_" . getID() . "`;");
	} catch( PDOException $e){
}
	try{
    $db->query("GRANT ALL PRIVILEGES on `nolagcp_" . getID() . "`.* TO 'nolagcp_" . getID() . "'@'%';");
	} catch( PDOException $e){
}
	setSQLPass($db, $randomPass);
}
function getID() {
	return($_SESSION['user']['id']);
}
function getPIN() {
	return($_SESSION['user']['spin']);
}
function getRank() {
	return($_SESSION['user']['rank']);
}
function getSQLPass() {
	//null checkdate
	if($_SESSION['user']['mysqlpass'] == null){
		return null;
	} else {
		return Security::decrypt($_SESSION['user']['mysqlpass'], "u>M&3gPCUMnc['7S");
	}
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