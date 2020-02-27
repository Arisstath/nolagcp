<?php 
    require("../includes/config.php"); 
	require_once "../includes/recaptchalib.php";
	
//Composer's autoload file loads all necessary files
require '../vendor/autoload.php';

$mail = new PHPMailer;

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
        $query = "SELECT * FROM users WHERE username=:username AND email=:email AND password='XenoMigration'"; 
        $query_params = array( 
            ':email' => $_POST['email'],
            ':username' => $_POST['username']		
        ); 
          $token = bin2hex(openssl_random_pseudo_bytes(20));
        try{ 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        }  catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
		$num = $stmt->rowCount();
		if($num == 0){
			$data = [ 'success' => 0, 'msg' => 'Could not find that user, please contact with support.' ];
		    die(json_encode($data));
		}
		  $row = $stmt->fetch(); 
       $message = file_get_contents('../emails/migrated.html'); 
$message = str_replace('%username%', $row["username"], $message); 
$message = str_replace('%token%', $token, $message); 
$mail->isSMTP();  // Set mailer to use SMTP
$mail->Host = 'smtp.mailgun.org';  // Specify mailgun SMTP servers
$mail->SMTPAuth = true; // Enable SMTP authentication
$mail->Username = 'cp@mailgun.nolag.host'; // SMTP username from https://mailgun.com/cp/domains
$mail->Password = 'Z-PvZ8%jeD>^Dj<j'; // SMTP password from https://mailgun.com/cp/domains
$mail->SMTPSecure = 'tls';   // Enable encryption, 'ssl'

$mail->From = 'no-reply@nolag.host'; // The FROM field, the address sending the email 
$mail->FromName = 'NoLagCP'; // The NAME field which will be displayed on arrival by the email client
$mail->addAddress($row["email"], $row["username"]);     // Recipient's email address and optionally a name to identify him
$mail->isHTML(true);   // Set email to be sent as HTML, if you are planning on sending plain text email just set it to false

// The following is self explanatory
$mail->Subject = 'Welcome to NoLagCP!';
$mail->Body    = $message;
$mail->AltBody = strip_tags($message); 

if(!$mail->send()) {  
   // echo "Message hasn't been sent.";
   // echo 'Mailer Error: ' . $mail->ErrorInfo . "\n";
} else {
    //echo "Message has been sent :) \n";

}
$query = "UPDATE users SET password=:token WHERE username=:username"; 
        $query_params = array( 
            ':token' => $token,
            ':username' => $_POST['username']		
        ); 
		 $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
       $data = [ 'success' => 1, 'msg' => 'Please check your emails for your new credentials!' ];
	    die(json_encode($data));
      
    } else {
		$data = [ 'success' => 0, 'msg' => 'There are blank fields.' ];
		die(json_encode($data));
	}
?> 