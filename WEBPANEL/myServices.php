<?php
require_once("includes/config.php");
include("includes/userutils.php");
$pageTitle = "My Services";
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
                    <h2>My Services</h2>
                    <small class="text-muted">From here you can list your active servers and be able to view the services that will expire soon.</small>
                    <ol class="breadcrumb breadcrumb-col-pink p-l-0">
                        <li><a href="javascript:void(0);">User</a></li>
                        <li class="active">My Services</li>
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
                        <h2>My Services</h2>
                    </div>
                    <div class="body table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
								 <th>Service ID</th>
                                  <th>Type</th>
                                  <th>IP</th>
								<th>Node</th>
								<th>Expiration</th>
									<th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                           <?php
										$msg = $messages['subusers_card_2_remove'];
										  $query = "SELECT * FROM services WHERE username=:username AND (active=2 OR active=1)"; 
	$query_params = array( ':username' => getUsername() ); 
        try { 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); } 
         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			 $plan = getPlan($db, $row['planid']);
			 $username = $row['username'];
			 $type = $plan['type'];
			 $server = getServerByServiceId($db, $row['id']);
			 $node = $server['node'];
			 $ip = $server['ip'] . ':' . $server['port'];
			 $serverid = $server['id'];
			 $expiration = $row['expiration'];
			 $id = $row['id'];
			 if($type == "MINECRAFT"){
				    $format = <<<f
			 <tr class="">
			 <td>#$id</td>
			<td>$type</td>
			<td>$ip</td>
			<td>$node</td>
			<td>$expiration</td>
			<td><a href="upgradeService?id=$serverid" class="btn  btn-raised btn-success waves-effect">Upgrade</a></td>
             </tr>     
f;
			 } else {
				  $format = <<<f
			 <tr class="">
			 <td>#$id</td>
			<td>$type</td>
			<td>$ip</td>
			<td>VPS1</td>
			<td>$expiration</td>
			<td><a href="" class="btn  btn-raised btn-success waves-effect">Not Available</a></td>
             </tr>     
f;
			 }
          
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
<script src="assets/bundles/libscripts.bundle.js"></script> <!-- Lib Scripts Plugin Js --> 
<script src="assets/bundles/vendorscripts.bundle.js"></script> <!-- Lib Scripts Plugin Js --> 

<script src="assets/plugins/jquery-sparkline/jquery.sparkline.js"></script> <!-- Sparkline Plugin Js --> 

<script src="assets/bundles/mainscripts.bundle.js"></script><!-- Custom Js --> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
</body>
</html>