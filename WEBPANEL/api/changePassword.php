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
    if(!empty($_POST)) 
    { 
        // Ensure that the user fills out fields 
        if(empty($_POST['token'])) 
        { $data = [ 'success' => 0, 'msg' => 'There are blank fields.' ];
		die(json_encode($data)); } 
        if(empty($_POST['password'])) 
        { $data = [ 'success' => 0, 'msg' => 'There are blank fields.' ];
		die(json_encode($data)); } 
          
        // Check if the username is already taken
        $query = "SELECT * FROM users WHERE rtoken=:token"; 
        $query_params = array( ':token' => $_POST['token'] ); 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
        $row = $stmt->fetch(); 
        if(!$row){ 
		$data = [ 'success' => 0, 'msg' => 'This Password Reset token is not valid, please make sure that you copied the URL right from the email!' ];
		die(json_encode($data));
		} 
		
         if($stmt->rowCount() > 1){
			 $data = [ 'success' => 0, 'msg' => 'This Password Reset token is expired.' ];
		die(json_encode($data));
		 }
        // Add row to database 
        $query = "UPDATE users SET password=:password,salt=:salt WHERE rtoken=:token"; 
          
        // Security measures
        $salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647)); 
        $password = hash('sha256', $_POST['password'] . $salt); 
        for($round = 0; $round < 65536; $round++){ $password = hash('sha256', $password . $salt); } 
        $query_params = array( 
            ':token' => $_POST['token'], 
            ':password' => $password, 
            ':salt' => $salt
        ); 
        try {  
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
		
		
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
		//invalidate token...
		$query = "UPDATE users SET rtoken='' WHERE username=:username"; 
		$query_params = array( ':username' => $row["username"] ); 
		$stmt = $db->prepare($query); 
        $result = $stmt->execute($query_params); 
        $data = [ 'success' => 1, 'msg' => 'Welcome to NoLag! You have successfully registered!' ];
		die(json_encode($data));
    } else {
		$data = [ 'success' => 0, 'msg' => 'There are blank fields.' ];
		die(json_encode($data));
	}
?>