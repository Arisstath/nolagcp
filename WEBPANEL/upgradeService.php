<?php
require_once("includes/config.php");
include("includes/userutils.php");


$id = $_GET["id"];

$query = "SELECT * FROM mcservers WHERE id=:id"; 
	$query_params = array( ':id' => $id ); 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
        $row = $stmt->fetch(); 
		$service = getService($db,$row["serviceid"]);
if(getRank() < 2){
if($row["username"] != getUsername()){
	if(!isSubuser($db, $id)){
	header("Location: dashboard");
	die("no access here");
	}
}

if($service['active'] == 3 || $service['active'] == 4){
	header("Location: suspended");
	die("Your service is suspended.");
}
if($service['active'] == 0){
	header("Location: dashboard");
	die("You do not have access here.");
}
if($service['active'] == 1){
	header("Location: install?id=".$id);
	die("Your service needs configuration.");
}
		}else {
	if($row["username"] != getUsername()){
	logAdmin($db, "ACCESS_PAGE", "Accessed Upgrade page of server #" . $id);
}
	
}

$plan = getPlan($db, $service['planid']);
$pageTitle = "Upgrade Service";
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
                    <h2>Upgrade Plan</h2>
                    <small class="text-muted">From here you can upgrade your service. NoLagCP will calculate the upgrade cost for you.</small>
                    <ol class="breadcrumb breadcrumb-col-pink p-l-0">
                        <li><a href="javascript:void(0);">Account</a></li>
                        <li class="active">Buy</li>
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
		  <h2 id="status">Minecraft plans</h2>
		</div>
        <div class="body">
		
                 
                       <select id="minecrafti" class="form-control show-tick">
											<?php
											//$api = file_get_contents("http://arisstath.me:8080/versions/spigot");
											$query = "SELECT * FROM plans WHERE type='MINECRAFT'"; 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$ram = $row['ram'];
			 $id = $row['id'];
			// echo($ram . "<=" . $plan['ram']);
			 if($ram <= $plan['ram']){
				 continue;
			 }
			 $desc = $row['description'];
             $format = <<<f
			 <option value="$id">$desc</option>
f;
         echo($format);
}

											?>
									</select>
					
           
					<button type="button" onclick="calculateCost();" class="btn  btn-raised btn-primary waves-effect">Calculate Cost</button>
               
				
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
		var csrf = "<?php echo $csrfToken; ?>";
			function calculateCost(){
				
					var e = document.getElementById("minecrafti");
            var strUser = e.options[e.selectedIndex].value;
		$.ajax({
    type: "GET",
    url: "api/upgrade?buy=0&id=" + "<?php echo htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8'); ?>" +"&plan=" + strUser + "&csrf=" + csrf,
    success: function (data) {
	var obj = JSON.parse(data);
	if(obj.success == 3){
			swal({
  title: "Upgrade",
  text: obj.msg,
  type: "warning",
  showCancelButton: true,
  confirmButtonColor: "#DD6B55",
  confirmButtonText: "Yes, upgrade it!",
  closeOnConfirm: false
},
function(){
  		$.ajax({
    type: "GET",
    url: "api/upgrade?buy=1&id=" + "<?php echo htmlspecialchars($_GET['id'], ENT_QUOTES, 'UTF-8'); ?>" +"&plan=" + strUser + "&csrf=" + csrf,
    success: function (data) {
	var obj = JSON.parse(data);
	if(obj.success == 1){
		swal("Upgraded!", obj.msg, "success"); 
	}
	if(obj.success == 0){
		swal("Failed!", obj.msg, "error"); 
	}

    },
	complete: function(xhr, textStatus) {
        if(xhr.status != 200){
			swal("Failed!", "Node is offline", "error"); 
		}
    } 
});
});
	}
	if(obj.success == 1){
		swal("Upgraded!", obj.msg, "success"); 
	}
	if(obj.success == 0){
		swal("Failed!", obj.msg, "error"); 
	}

    },
	complete: function(xhr, textStatus) {
        if(xhr.status != 200){
			swal("Failed!", "Node is offline", "error"); 
		}
    } 
});
	}
		</script>
</body>
</html>