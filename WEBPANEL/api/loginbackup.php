<?php 
    require("../includes/config.php"); 
	require_once "../includes/recaptchalib.php";
	

$secret = "6LfHVRoUAAAAAEl1ta643Z2uGbzGEU9yQ_zFYdTy";
$response = null;
$reCaptcha = new ReCaptcha($secret);
if ($_POST["g-recaptcha-response"]) {
    $response = $reCaptcha->verifyResponse(
        $_SERVER["REMOTE_ADDR"],
        $_POST["g-recaptcha-response"]
    );
}
if ($response != null && $response->success) {
}else{
	$data = [ 'success' => 0, 'msg' => 'Captcha is not valid.' ];
		die(json_encode($data));
}
    $submitted_username = ''; 
    if(!empty($_POST)){ 
	if(!isset($_POST['tos'])) 
        { $data = [ 'success' => 0, 'msg' => 'You must agree with our Terms of Service.' ];
		die(json_encode($data)); } 
        $query = " 
            SELECT 
                id, 
                username, 
                password, 
                salt, 
                username,
                balance				
            FROM users 
            WHERE 
                email = :email 
        "; 
        $query_params = array( 
            ':email' => $_POST['email'] 
        ); 
          
        try{ 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
        $login_ok = false; 
        $row = $stmt->fetch(); 
        if($row){ 
            $check_password = hash('sha256', $_POST['password'] . $row['salt']); 
            for($round = 0; $round < 65536; $round++){
                $check_password = hash('sha256', $check_password . $row['salt']);
            } 
            if($check_password === $row['password']){
                $login_ok = true;
            } 
        } 
 
        if($login_ok){ 
            unset($row['salt']); 
            unset($row['password']); 
			
			//set user's info
            $_SESSION['user'] = $row;
			
            //header("Location: secret.php"); 
			$token = bin2hex(openssl_random_pseudo_bytes(20));
			$submitted_email = htmlentities($_POST['email'], ENT_QUOTES, 'UTF-8');
			$pin = bin2hex(openssl_random_pseudo_bytes(4));
		$query = "UPDATE users SET spin=:token WHERE username=:username";
		$query_params = array( ':token' => $pin,':username' => $_SESSION['user']['username'] );
			try {
				$stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
			}
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
			$db->query("UPDATE users SET token='" . $token . "' WHERE email='" . $submitted_email . "'");
            $data = [ 'success' => 1, 'token' => $token, 'msg' => 'Welcome to NoLag Control Panel Alpha!' ];
		    die(json_encode($data));
        } 
        else{ 
            $data = [ 'success' => 0, 'msg' => 'This is not a valid combination of email and password.' ];
            //$submitted_email = htmlentities($_POST['email'], ENT_QUOTES, 'UTF-8');
            die(json_encode($data));			
        } 
    } else {
		$data = [ 'success' => 0, 'msg' => 'There are blank fields.' ];
		die(json_encode($data));
	}
?> 