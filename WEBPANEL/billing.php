<?php
die("Billing has been disabled");
require_once("includes/config.php");
include("includes/userutils.php");
include 'classes/Cart.php';
$cart = new Cart;

$pageTitle = "Order";
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
                    <h2>Buy Plan</h2>
                    <small class="text-muted">From here you can buy a Minecraft or a VPS plan. Orders are processed instantly and you will have your product in a few seconds.</small>
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
											$query = "SELECT * FROM plans WHERE type='MINECRAFT'";
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

			 $id = $row['id'];
			 $desc = $row['description'];
             $format = <<<f
			 <option value="$id">$desc</option>
f;
         echo($format);
}

											?>
									</select>
					
           
					<button type="button" onclick="buyMC();" class="btn  btn-raised btn-primary waves-effect"><?php echo $messages['orderservices_buy']; ?></button>
               
				
        </div>
					
			  
		</div>
		</div>
		 <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	<div class="card">
		<div class="header">
		  <h2 id="status">VPS plans</h2>
		</div>
        <div class="body">
		
                 
                       <select id="vpsi" class="form-control show-tick">
											<?php
											$query = "SELECT * FROM plans WHERE type='VPS'";
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

			 $id = $row['id'];
			 $desc = $row['description'];
             $format = <<<f
			 <option value="$id">$desc</option>
f;
         echo($format);
}

											?>
									</select>
					
           
					<button type="button" onclick="buyVPS();" class="btn  btn-raised btn-primary waves-effect"><?php echo $messages['orderservices_buy']; ?></button>
               
				
        </div>
					
			  
		</div>
		</div>
		<!--<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	<div class="card">
		<div class="header">
		  <h2 id="status">MySQL Plans</h2>
		</div>
        <div class="body">
		
                 
                       <select id="mysqli" class="form-control show-tick">
											<?php
											$query = "SELECT * FROM plans WHERE type='MYSQL'";
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

			 $id = $row['id'];
			 $desc = $row['description'];
             $format = <<<f
			 <option value="$id">$desc</option>
f;
         echo($format);
}

											?>
									</select>
					
           
					<button type="button" onclick="buyMySQL();" class="btn  btn-raised btn-primary waves-effect"><?php echo $messages['orderservices_buy']; ?></button>
               
				
        </div>
					
			  
		</div>
		</div>
        </div>
      </div>!-->
</section>
<!-- Jquery Core Js --> 
<script src="https://nolag.r.worldssl.net/panel/assets/bundles/libscripts.bundle.js"></script> <!-- Lib Scripts Plugin Js --> 
<script src="https://nolag.r.worldssl.net/panel/assets/bundles/vendorscripts.bundle.js"></script> <!-- Lib Scripts Plugin Js --> 

<script src="https://nolag.r.worldssl.net/panel/assets/plugins/jquery-sparkline/jquery.sparkline.js"></script> <!-- Sparkline Plugin Js --> 

<script src="https://nolag.r.worldssl.net/panel/assets/bundles/mainscripts.bundle.js"></script><!-- Custom Js --> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
  <script>
  			function buyVPS(){
					var e = document.getElementById("vpsi");
            var strUser = e.options[e.selectedIndex].value;
		$.ajax({
    type: "GET",
    url: "api/addCart?id=" + strUser + "&csrf=<?php echo($csrfToken); ?>",
    success: function (data) {
	var obj = JSON.parse(data);
	if(obj.success == 1){
		swal("Success!", JSON.parse(data).msg, "success");
		window.location = "viewCart";
	} else {
		swal("Failed!", JSON.parse(data).msg, "error");
	}
    },
	complete: function(xhr, textStatus) {
        if(xhr.status != 200){
			swal("Failed!", "Node is offline", "error");
		}
    }
});
	}
			function buyMC(){
					var e = document.getElementById("minecrafti");
            var strUser = e.options[e.selectedIndex].value;
		$.ajax({
    type: "GET",
    url: "api/addCart?id=" + strUser + "&csrf=<?php echo($csrfToken); ?>",
    success: function (data) {
	var obj = JSON.parse(data);
	if(obj.success == 1){
		swal("Success!", JSON.parse(data).msg, "success");
		window.location = "viewCart";
	} else {
		swal("Failed!", JSON.parse(data).msg, "error");
	}
    },
	complete: function(xhr, textStatus) {
        if(xhr.status != 200){
			swal("Failed!", "Node is offline", "error");
		}
    }
});
	}

	function buyMySQL(){
					var e = document.getElementById("mysqli");
            var strUser = e.options[e.selectedIndex].value;
		$.ajax({
    type: "GET",
    url: "api/addCart?id=" + strUser + "&csrf=<?php echo($csrfToken); ?>",
    success: function (data) {
	var obj = JSON.parse(data);
	if(obj.success == 1){
		swal("Success!", JSON.parse(data).msg, "success");
		window.location = "viewCart";
	} else {
		swal("Failed!", JSON.parse(data).msg, "error");
	}
    },
	complete: function(xhr, textStatus) {
        if(xhr.status != 200){
			swal("Failed!", "Error processing", "error");
		}
    }
});
	}


		</script>
</body>
</html>