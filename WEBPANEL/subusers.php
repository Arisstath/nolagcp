<?php
require_once("includes/config.php");
include("includes/userutils.php");
include 'includes/security.php';

$key = "u>M&3gPCUMnc['7S";

$id = $_GET["id"];

$query = "SELECT * FROM mcservers WHERE id=:id";
	$query_params = array(':id' => $id );
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
        $row = $stmt->fetch();
		if(getRank() != 2){
if($row["username"] != getUsername()){
	if(!isSubuser($db, $id)){
	header("Location: dashboard");
	die("no access here");
	}
}
$service = getService($db,$row["serviceid"]);
if($service['active'] == 3 || $service['active'] == 4){
	header("Location: suspended");
	die("Your service is suspended.");
}
if($service['active'] == 0){
	header("Location: dashboard");
	die("You do not have access here.");
}
		}
$baseurl = "https://". $row["node"] . ".mcsrv.top";
//die($baseurl);
$id = $_GET["id"];
$pageTitle = "Subusers";
?>
<!DOCTYPE html>
<html>
<?php include_once("includes/header.php"); ?>
<body class="theme-red">
<!-- Page Loader -->
<?php include_once("includes/loader.php"); ?>

<!-- Overlay For Sidebars -->
<div class="overlay"></div>

<!-- Top Bar -->
<?php include_once("includes/topbar.php"); ?>

<!-- Left & Right bar menu -->
<?php include_once("includes/sidebar.php"); ?>

<section class="content">
  <div class="block-header">
        <div class="row">
            <div class="col-md-8 col-sm-7 col-xs-12">
                <div class="h-left clearfix">
                    <h2>Subusers</h2>
                    <small class="text-muted">From here you can grant server management permission to other NoLagCP members. Please note, that these account will have <b>full</b> access to your server, except from the billing part.</small>
                    <ol class="breadcrumb breadcrumb-col-pink p-l-0">
                        <li><a href="javascript:void(0);">Servers</a></li>
                        <li class="active">Subusers</li>
                    </ol>
					
                </div>
            </div>
            
        </div>
    </div>
	
<div class="container-fluid">
   <div class="row clearfix">
   <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	<div class="card">
		<div class="header">
		  <h2 id="status">Add subuser</h2>
		</div>
        <div class="body">
		<div class="form-group">
                   <div class="form-line">
                        <input id="param" class="form-control" type="text" placeholder="<?php echo $messages['subusers_card_1_placeholder']; ?>"></input>
					
                    </div>
					<button type="button" onclick="addSubuser();" class="btn  btn-raised btn-primary waves-effect"><?php echo $messages['subusers_card_1_add']; ?></button>
                </div>
					
        </div>
					
			  
		</div>
		</div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <h2>Current <?php echo $messages['subusers_card_2_title']; ?></h2>
                    </div>
                    <div class="body table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th><?php echo $messages['subusers_card_2_username']; ?></th>
                                    <th><?php echo $messages['subusers_card_2_action']; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
										$msg = $messages['subusers_card_2_remove'];
										  $query = "SELECT * FROM subusers WHERE serverid=:serverid"; 
	$query_params = array( ':serverid' => $_GET["id"] ); 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			
			 $username = $row['username'];
             $format = <<<f
			 <tr>
			<td>$username</td>
			<td><a onclick="deleteSubuser('$username');" class="btn  btn-raised btn-danger btn-xs waves-effect">$msg</a></td>
             </tr>     
f;
         echo($format);
}
?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
      </div>
</section>
<!-- Jquery Core Js --> 
<script src="https://nolag.r.worldssl.net/panel/assets/bundles/libscripts.bundle.js"></script> <!-- Lib Scripts Plugin Js --> 
<script src="https://nolag.r.worldssl.net/panel/assets/bundles/vendorscripts.bundle.js"></script> <!-- Lib Scripts Plugin Js --> 

<script src="https://nolag.r.worldssl.net/panel/assets/plugins/jquery-sparkline/jquery.sparkline.js"></script> <!-- Sparkline Plugin Js --> 

<script src="https://nolag.r.worldssl.net/panel/assets/bundles/mainscripts.bundle.js"></script><!-- Custom Js --> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
 <script>
		 function addSubuser(){
			 
			  $.get("api/addSubuser?username=" + document.getElementById("param").value + "&id=" + "<?php echo htmlentities($_GET["id"], ENT_QUOTES, 'UTF-8') ?>" + "&csrf=<?php echo($csrfToken); ?>", function(data){
			var response = JSON.parse(data);
            if(response.success == 1){
				swal("Success!", response.msg, "success"); 
                location.reload();
            }else{
              swal("Failed!", response.msg, "error"); 
			   document.getElementById("param").value = "";
            }
        });
		 }
		 function deleteSubuser(subuser){
			 
			  $.get("api/delSubuser?username=" + subuser + "&id=" + "<?php echo htmlentities($_GET["id"], ENT_QUOTES, 'UTF-8') ?>" + "&csrf=<?php echo($csrfToken); ?>", function(data){
			var response = JSON.parse(data);
            if(response.success == 1){
				swal("Success!", response.msg, "success"); 
                location.reload();
            }else{
              swal("Failed!", response.msg, "error"); 
            }
        });
		 }
		</script>
</body>
</html>