<?php
require_once("includes/config.php");
include("includes/userutils.php");
$pageTitle = "Dashboard";
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

<!-- Main Content -->
<section class="content home">
    <div class="block-header">
        <div class="row">
            <div class="col-md-8 col-sm-7 col-xs-12">
                <div class="h-left clearfix aos-item" data-aos-duration="400" data-aos-delay="300" data-aos="slide-down">
                    <h2>DASHBOARD</h2>
                    <small class="text-muted">Welcome to the most powerful Minecraft control panel, NoLagCP.</small>
                    <ol class="breadcrumb breadcrumb-col-pink p-l-0">
                        <li><a href="javascript:void(0);">NoLagCP</a></li>
                        <li class="active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
<?php if (getBalance() < 0)	{
	?>
	<div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card" >
                    <div class="header ">
                        <h2> Negative Balance </h2>
                    </div>
                    <div class="body bg-red">
					<p>It seems that you have a <b>negative</b> balance. Make sure to deposit money <b>as soon as possible</b>, otherwise we might hand over this manner to a <b>money collection agency</b>.</p>
                    </div>
                </div>
            </div>
        </div>
		<?php
}
?>
        <div class="row clearfix">

           <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="row clearfix top-report">
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="card aos-item" data-aos-duration="400" data-aos-delay="200" data-aos="fade-up">
                            <div class="body">
                                <h3 class="m-t-0"><?php echo getTotalUsers($db); ?></h3>
                                <p class="text-muted">Total Users</p>
                                </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="card aos-item" data-aos-duration="400" data-aos-delay="200" data-aos="fade-up">
                            <div class="body">
                                <h3 class="m-t-0"><?php echo getDeployedServers($db); ?></h3>
                                <p class="text-muted">Deployed Servers</p>
                                </div>
                        </div>
                    </div>
                     <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="card aos-item" data-aos-duration="400" data-aos-delay="200" data-aos="fade-up">
                            <div class="body">
                                <h3 class="m-t-0"><?php echo getActiveServices($db); ?></h3>
                                <p class="text-muted">Active Services</p>
                                </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="card aos-item" data-aos-duration="400" data-aos-delay="200" data-aos="fade-up">
                            <div class="body">
                                <h3 class="m-t-0" id="balance">$<?php echo getBalance(); ?></h3>
                                <p class="text-muted">Account Balance</p>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card" >
                    <div class="header ">
                        <h2> Link your Discord </h2>
                    </div>
                    <div class="body">
					<?php
					if(getDiscordID() == ""){
					?>
					<p>You can link your Discord account by typing in any channel <code>#link <?php echo getPIN(); ?></code>.</p>
					<?php
					} else {
					?>
					Thank you for linking your Discord account!
					<?php
					}
					?>
                    </div>
                </div>
            </div>
        </div>
		<div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card" >
                    <div class="header ">
                        <h2> URGENT ANNOUNCEMENT </h2>
                    </div>
                    <div class="body bg-red">
					<p>Billing has been disabled, your services will not expire until our shutdown.</p>
                    </div>
                </div>
            </div>
        </div>
<div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <h2> DISCORD </h2>
                    </div>
                    <div class="body">
					<iframe id="discord" src="https://discordapp.com/widget?id=295274094562246656&theme=dark" width="100%" height="500" allowtransparency="true" frameborder="0"></iframe>
                    </div>
                </div>
            </div>
        </div>
<div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <h2> <?php echo $messages['dashboard_card2_title']; ?> </h2>
                    </div>
                    <div class="body">
                        <table class="table table-bordered table-striped table-hover js-basic-examplee dataTable">
                            <thead>
                                <tr>
									<th>ID</th>
                                    <th><?php echo $messages['dashboard_card2_amount']; ?></th>
                                    <th><?php echo $messages['dashboard_card2_paymentstatus']; ?></th>
                                    <th><?php echo $messages['dashboard_card2_action']; ?></th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
									<th>ID</th>
                                    <th><?php echo $messages['dashboard_card2_amount']; ?></th>
                                    <th><?php echo $messages['dashboard_card2_paymentstatus']; ?></th>
                                    <th><?php echo $messages['dashboard_card2_action']; ?></th>
                                </tr>
                            </tfoot>
                            <tbody>
							<?php
								  getUnpaidInvoices($db);
								  $viewt = $messages['dashboard_card2_view'];
								  $query = "SELECT * FROM invoices WHERE username=:username ";
	$query_params = array( ':username' => getUsername() );
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			 if($row['paid'] == 1) {
				 $text = "Paid";
			 } else {
				 $text = "<strong>Unpaid</strong>";
			 }
			 $id = $row['id'];
			 $amount = $row['amount'];
             $format = <<<f
			  <tr>
                                            <td>#$id </td>
                                            <td>$$amount </td>
                                            <td>$text</td>
											<td><a href="invoice?id=$id" class="btn  btn-raised btn-primary btn-xs waves-effect">$viewt</a></td>
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
        <div class="row clearfix">
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="card">
                    <div class="body" id="footer">
                        <div class="row">
                            <div class="col-xs-12">
							<p>Made with love in <b>Greece</b>.</p>
                                <p class="copy m-b-0">© Copyright
                                    <time class="year">2017</time>
                                    BOTSTACK PRIVATE HOSTING LTD</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Jquery Core Js -->
<script src="https://nolag.r.worldssl.net/panel/assets/bundles/libscripts.bundle.js"></script> <!-- Lib Scripts Plugin Js -->
<script src="https://nolag.r.worldssl.net/panel/assets/bundles/vendorscripts.bundle.js"></script> <!-- Lib Scripts Plugin Js -->

<script src="https://nolag.r.worldssl.net/panel/assets/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script> <!-- JVectorMap Plugin Js -->
<script src="https://nolag.r.worldssl.net/panel/assets/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script> <!-- JVectorMap Plugin Js -->
<script src="https://nolag.r.worldssl.net/panel/assets/plugins/jquery-sparkline/jquery.sparkline.js"></script> <!-- Sparkline Plugin Js -->
<script src="https://nolag.r.worldssl.net/panel/assets/plugins/jquery-countto/jquery.countTo.js"></script> <!-- Jquery CountTo Plugin Js -->
<script src="https://nolag.r.worldssl.net/panel/assets/bundles/flotscripts.bundle.js"></script><!-- Flot Charts Plugin Js -->
<script src="https://nolag.r.worldssl.net/panel/assets/bundles/morrisscripts.bundle.js"></script><!-- Morris Plugin Js -->
<script src="https://nolag.r.worldssl.net/panel/assets/plugins/jquery-knob/jquery.knob.min.js"></script> <!-- Jquery Knob Plugin Js -->
<script src="https://nolag.r.worldssl.net/panel/assets/plugins/aos-animation/aos.js"></script> <!-- AOS Animation -->
<!-- Jquery DataTable Plugin Js -->
<script src="https://nolag.r.worldssl.net/panel/assets/plugins/jquery-datatable/jquery.dataTables.js"></script>
<script src="https://nolag.r.worldssl.net/panel/assets/plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js"></script>
<script src="https://nolag.r.worldssl.net/panel/assets/plugins/jquery-datatable/extensions/export/dataTables.buttons.min.js"></script>
<script src="https://nolag.r.worldssl.net/panel/assets/plugins/jquery-datatable/extensions/export/buttons.flash.min.js"></script>
<script src="https://nolag.r.worldssl.net/panel/assets/plugins/jquery-datatable/extensions/export/jszip.min.js"></script>
<script src="https://nolag.r.worldssl.net/panel/assets/plugins/jquery-datatable/extensions/export/pdfmake.min.js"></script>
<script src="https://nolag.r.worldssl.net/panel/assets/plugins/jquery-datatable/extensions/export/vfs_fonts.js"></script>
<script src="https://nolag.r.worldssl.net/panel/assets/plugins/jquery-datatable/extensions/export/buttons.html5.min.js"></script>
<script src="https://nolag.r.worldssl.net/panel/assets/plugins/jquery-datatable/extensions/export/buttons.print.min.js"></script>

<script src="https://nolag.r.worldssl.net/panel/assets/bundles/mainscripts.bundle.js"></script>
<script src="https://nolag.r.worldssl.net/panel/assets/js/pages/index.js"></script>
<script src="https://nolag.r.worldssl.net/panel/assets/js/pages/maps/jvectormap.js"></script>
<script src="https://nolag.r.worldssl.net/panel/assets/js/pages/charts/jquery-knob.js"></script>

<script type="text/javascript">
$(document).ready(function() { 
if(localStorage.playVideo == 1){
	console.log("special video");
var videourl = 'special/10.mp4'; 
    	var videocontainer = '#videocontainer'; 
	var parameter = new Date().getMilliseconds(); 
	
	var video = '<video width="1102" height="720" id="intro-video" autoplay loop src="' + videourl + '?t=' + parameter + '"></video>'; // setup the video element

	$(videocontainer).append(video);
	
	videl = $(document).find('#intro-video')[0];
			
	videl.load(); // load the video (it will autoplay because we've set it as a parameter of the video)
	videl.addEventListener('ended',videoHandler,false);
    function videoHandler(e) {
      //  localStorage.playVideo = 0;
		location.reload();
    }
}

});

//dirty hax
$(window).resize(function() {
   // $('#discord').width($(window).width());
   
});

$(window).trigger('resize');

   //Exportable table
    $('.js-basic-examplee').DataTable({
		'pageLength': 5,
		"bFilter":  false,
		"paging":   true,
        "ordering": false,
        "info":     false
    });
    AOS.init({
        easing: 'ease-in-out-sine'
    });
</script>
</body>
</html>