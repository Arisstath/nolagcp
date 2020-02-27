<?php
require_once("includes/config.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//require __DIR__ . '/vendor/autoload.php';
$invoices_generated = 0;
function postToDiscord($message)
{
    $data = array("content" => $message, "username" => "NoLagCP Cronjob");
    $curl = curl_init("https://discordapp.com/api/webhooks/337501874087395330/f_toRWWxtiDBIs7p2OVTHSy9LND_P26r5yh4mqu8ebMrughLY5CnlQXxfnfCD6mqYs1Z");
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    return curl_exec($curl);
}


postToDiscord("Beep boop i am a cronjob");
//handle servers in the queue
//clear php sessions
/*
$query = "SELECT * FROM activesessions"; 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute(); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	        //check if it's still active
			$nSessions = $db->query("SELECT count(*) FROM sessions WHERE id='" . $row['id'] . "'")->fetchColumn(); 
			if($nSessions <= 0){
				$query = "DELETE FROM activesessions WHERE id = :id LIMIT 1"; 
	    $query_params = array( ':id' => $row['id'] ); 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex){  } 
			}
		 }
		 */
		 postToDiscord("active sessions have been handled *beep*");
//Fetch daemon token
$token = getToken($db);
     //FIRST PHASE, JUST GENERATE INVOICES
	$query = "SELECT * FROM services WHERE expiration < NOW() + INTERVAL 3 DAY"; 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute(); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	     $id = $row['id'];
         //generate invoice for this, if it hasn't been generated already lel
		 $username = $row["username"];
		 if($row["planid"] == -1) {
			 continue;
		 }
		 $plan = getPlan($db, $row["planid"]);
		 $nInvoices = $db->query("SELECT count(*) FROM invoices WHERE username='" . $username . "' AND products='" . $id . "' AND paid=0")->fetchColumn(); 
		 if($nInvoices == 0){
			  //die("INSERT INTO invoices(username,products,amount,discount,paid,date,due) VALUES ('" . $username . "','" . $id . "'," . $plan["price"] . ",0.00,0,NOW(),NOW())");
			 $db->query("INSERT INTO invoices(username,products,amount,discount,paid,date,due) VALUES ('" . $username . "','" . $id . "'," . $plan["price"] . ",0.00,0,NOW(),NOW() + INTERVAL 7 DAY)"); //create invoice
			 $invoices_generated++;
		 } 
}
postToDiscord($invoices_generated . " invoices were generated *boop*");
echo("Invoices created: " . $invoices_generated);
$suspended = 0;
//SECOND PHASE, SUSPEND SERVERS
	$query = "SELECT * FROM services WHERE expiration < NOW() AND active != 3"; 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute(); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	     $id = $row['id'];
		 if($row["planid"] == -1) {
			 continue;
		 }
		 setServiceStatus($db, $id, 3); //ripperino better pay
		 //we need to actually shutdown the server
		 
		     if($row['category'] == "MINECRAFT"){
				 $server = getServerByService($db, $id);
				 file_get_contents("https://" . $server['node'] . ".mcsrv.top/" . $token . "/servers/" . $server["id"] . "/stop");
			 
		 }
		 $suspended++;
}
postToDiscord($suspended . " services suspended *boop*");
echo("<br>Services suspended: " . $suspended);
//THIRD PHASE, EXPIRE INVOICES
$invoicesExpired = 0;
$query = "SELECT * FROM invoices WHERE due < NOW() AND paid=0"; 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute(); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	     $id = $row['id'];
		 markExpired($db, $id);
		 $invoicesExpired++;
		 }
		
		 $mysqlf = 0;
		 echo("<br>Invoices expired: " . $invoicesExpired);
//FOURTH PHASE, GIBBERIFY MYSQL DATABASE
$query = "SELECT * FROM users WHERE mysqlpass != ''"; 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute(); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
	     $user = getUser($db, $row['id']);
	     if(getActiveServices($db, $user['username']) <= 0){
			 //check if they have sql
			 if($user['mysqlpass'] != null){
				 $randompass = generatePassword();
				
				try{
            $db->query("SET PASSWORD FOR 'nolagcp_" . $user['id'] . "'@'%' = PASSWORD('" . $randompass . "');");
			$mysqlf++;
	      } catch( PDOException $e){
	      	//die("Failed to run query: " . $ex->getMessage());
           }
			 }
			
		 }
		 }
postToDiscord($mysqlf . " mysql users expired(change password to random) *boop boop*");
 echo("<br>MySQL passwords changed: " . $mysqlf);
postToDiscord("am a cronjob, goodbye");


function getUnpaid($db, $id) {
	$query = "SELECT * FROM invoices WHERE paid=0"; 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
        $row = $stmt->fetch(); 

		return $row;
}
function getActiveServices($db, $username) {
	
	$nRows = $db->query("SELECT count(*) FROM services WHERE username='" . $username . "' AND active=1 OR active=2 AND username='" . $username . "'")->fetchColumn(); 
    return ($nRows);
}
function getUser($db, $username) {
	$query = "SELECT * FROM users WHERE username=:username"; 
	$query_params = array( ':username' => $username ); 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
        $row = $stmt->fetch(); 

		return $row; 
}

function getToken($db) {
	$query = "SELECT * FROM users WHERE username=:username"; 
	$query_params = array( ':username' => "test" ); 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
        $row = $stmt->fetch(); 

		return $row["token"]; 
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

function createServer($db, $name, $port, $ip, $serviceid, $node, $ram, $username, $ftpuser, $ftppass) {
	$query = "INSERT INTO mcservers(name,port,ip,serviceid,node,ram,username, ftpusername, ftppass) VALUES(:name,:port,:ip,:serviceid,:node,:ram,:username,:ftpuser,:ftppass)";
	
	$query_params = array( ':name' => $name, ':port' => $port, ':ip' => $ip, ':serviceid' => $serviceid, ':node' => $node, ':ram' => $ram, ':username' => $username, ':ftpuser' => $ftpuser, ':ftppass' => $ftppass); 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
}
function markExpired($db, $invoice) {
	$query = "UPDATE invoices SET paid=3 WHERE id=:id"; 
	$query_params = array( ':id' => $invoice); 
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
function getServerByService($db, $id) {
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