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
        if(empty($_POST['username'])) 
        { $data = [ 'success' => 0, 'msg' => 'There are blank fields.' ];
		die(json_encode($data)); } 
        if(empty($_POST['password'])) 
        { $data = [ 'success' => 0, 'msg' => 'There are blank fields.' ];
		die(json_encode($data)); } 
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) 
        { $data = [ 'success' => 0, 'msg' => 'Whoops! This is not a valid e-mail address!' ];
		die(json_encode($data)); } 
          //check username
		  if ( preg_match('/\s/',$_POST['username']) ){
			  $data = [ 'success' => 0, 'msg' => 'Sorry, but you can\'t use whitespaces in your username.' ];
		      die(json_encode($data));
		  }
		  if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $_POST['username']))
{
    $data = [ 'success' => 0, 'msg' => 'Your username can not contain special characters.' ];
		      die(json_encode($data));
}
		  if(!isset($_POST['tos'])) 
        { $data = [ 'success' => 0, 'msg' => 'You must agree with our Terms of Service.' ];
		die(json_encode($data)); } 
        // Check if the username is already taken
        $query = " 
            SELECT 
                1 
            FROM users 
            WHERE 
                username = :username 
        "; 
        $query_params = array( ':username' => $_POST['username'] ); 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
        $row = $stmt->fetch(); 
        if($row){ 
		$data = [ 'success' => 0, 'msg' => 'Oh no! Someone is already using this username :( Can you think another?' ];
		die(json_encode($data));
		} 
        $query = " 
            SELECT 
                1 
            FROM users 
            WHERE 
                email = :email 
        "; 
        $query_params = array( 
            ':email' => $_POST['email'] 
        ); 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage());} 
        $row = $stmt->fetch(); 
        if($row){ $data = [ 'success' => 0, 'msg' => 'Whoops! This e-mail address is already registered!' ];
		die(json_encode($data));
		} 
          
        // Add row to database 
        $query = " 
            INSERT INTO users ( 
                username, 
                password, 
                salt, 
                email 
            ) VALUES ( 
                :username, 
                :password, 
                :salt, 
                :email 
            ) 
        "; 
          
        // Security measures
        $salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647)); 
        $password = hash('sha256', $_POST['password'] . $salt); 
        for($round = 0; $round < 65536; $round++){ $password = hash('sha256', $password . $salt); } 
        $query_params = array( 
            ':username' => $_POST['username'], 
            ':password' => $password, 
            ':salt' => $salt, 
            ':email' => $_POST['email'] 
        ); 
        try {  
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
        $data = [ 'success' => 1, 'msg' => 'Welcome to NoLag! You have successfully registered!' ];
		die(json_encode($data));
    } else {
		$data = [ 'success' => 0, 'msg' => 'There are blank fields.' ];
		die(json_encode($data));
	}
?>