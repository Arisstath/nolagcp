<?php
require_once("includes/config.php");
include("includes/userutils.php");


//gather invoice info
$query = "SELECT * FROM invoices WHERE id=:id";
	$query_params = array( ':id' => $_GET["id"] );
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
        $row = $stmt->fetch();
		$idS = $row['id'];
		$amount = $row['amount'];
		$products = $row['products'];
		$paid = $row['paid'];
		$user = $row["username"];
		$discount = $row["discount"];
		$date = $row["date"];
		$due = $row["due"];
		if($user != getUsername()) {
		   header("Location: dashboard");
		   die("Redirecting to dashboard");
		}
$pageTitle = "Invoice #" . $idS;
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
    
    <div class="container-fluid" id="toprint">
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <h2>Invoices Detail</h2>
                    </div>
                    <div class="body">
                        <div class="clearfix">
                            <div class="pull-left">
                                <h4 class="text-right"><img src="hey" width="70" alt="velonic"></h4>                                                
                            </div>
                            <div class="pull-right">
                                <h4>Invoice #<?php echo $idS; ?><br>
                                </h4>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">                                                
                                <div class="pull-left mt-20">
                                    <address>
                                      <strong>xd am online mailbox address</strong><br>
                                      120 High Road, East Finchley, London, <br>
                                      United Kingdom, N2 9ED <br>
                                      <abbr title="Phone">P:</abbr> (800) 086 9813
                                      </address>
                                </div>
                                <div class="pull-right mt-20">
                                    <p><strong>Order Date: </strong> <?php
												echo(date('F j, Y',strtotime($date)));
												?></p>
                                    <p class="m-t-10"><strong>Order Status: </strong> <span class="badge <?php if ($paid == 1) { echo ("bg-green"); } elseif($paid==0) { echo("bg-red"); } else { echo("bg-orange"); }?>"><?php if ($paid == 1) { echo ("Paid"); } elseif($paid==0) { echo("Unpaid"); } else { echo("Expired"); }?></span></p>
                                    <p class="m-t-10"><strong>Order Due: </strong> <?php
												echo(date('F j, Y',strtotime($due)));
												?></p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-40"></div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table id="mainTable" class="table table-striped" style="cursor: pointer;">
                                        <thead>
                                            <tr><th>Service ID</th>
											<th>Product ID</th>
                                            <th>Item</th>
                                            <th>Cost</th>
                                        </tr></thead>
                                        <tbody>
										 <?php
												$total = 0.00;
												$format = "";
												if (strpos($products, ',') !== false) {
                                                    $cats = explode(",", $products);
                                                foreach($cats as $cat) {
                                                    $rowS = getService($db,$cat);
													$planid = $rowS['planid'];
													$row = getPlan($db, $planid);
													//$planid = $row['planid'];
													if($row['type'] == 'MINECRAFT'){
														$server = getServerByServiceId($db, $rowS['id']);
														$productID = $server['id'];
													}
													if($row['type'] == 'VPS'){
														$server = getvServerByService($db, $rowS['id']);
														$productID = $server['id'];
													}
													$desc = $row['description'];
													$price = $row['price'];
													$total += $price;
													$format = <<<f
													 <tr>
                                                        <td>#$cat</td>
														<td>#$productID</td>
                                                        <td>$desc</td>
                                                        <td class="right-align">$$price</td>
                                                    </tr>
f;
													echo($format);
												}


                                            }
                                                 else {
													 $rowS = getService($db,$products);
													$planid = $rowS['planid'];
													$row = getPlan($db, $planid);
													//$planid = $row['planid'];
													if($row['type'] == 'MINECRAFT'){
														$server = getServerByServiceId($db, $rowS['id']);
														$productID = $server['id'];
													}
													if($row['type'] == 'VPS'){
														$server = getvServerByService($db, $rowS['id']);
														$productID = $server['id'];
													}
													$desc = $row['description'];
													$price = $row['price'];
													$total += $price;
													$format = <<<f
													 <tr>
                                                        <td>#$products</td>
														<td>#$productID</td>
                                                        <td>$desc</td>
                                                        <td class="right-align">$$price</td>
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
                        <hr>
                        <div class="row" style="border-radius: 0px;">
                            <div class="col-md-3 col-md-offset-9">
							<?php
												if($discount != 0.00) {
													$format = <<<f
													 <p class="text-right">Discount: $discount</p>
f;
echo($format);
												}
												?>
                               
								<!-- We are not that big yet to add vat
                                <p class="text-right">VAT: 12.9%</p>
								!-->
                                <hr>
                                <h3 class="text-right">USD <?php echo($amount); ?></h3>
                            </div>
                        </div>
                        <hr>
                        <div class="hidden-print">
                            <div class="pull-right">        
							<?php
												if($paid == 1 || $paid == 2){
													$goback = $messages['invoice_total'];

													$btn = <<<f
													<a href="dashboard" class="btn btn-raised btn-primary">$goback</a>
f;
												} else {
													$pay = $messages['orderservices_buy'];
													$btn = <<<f
													<a onclick="pay();" class="btn btn-raised btn-success">$pay</a>
f;
												}
												echo($btn);
												?>
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

<script src="https://nolag.r.worldssl.net/panel/assets/plugins/jquery-sparkline/jquery.sparkline.js"></script> <!-- Sparkline Plugin Js --> 

<script src="https://nolag.r.worldssl.net/panel/assets/bundles/mainscripts.bundle.js"></script><!-- Custom Js --> 
<script src="https://nolag.r.worldssl.net/panel/assets/js/pages/charts/sparkline.js"></script> 
<script src="https://nolag.r.worldssl.net/panel/assets/plugins/bootstrap-notify/bootstrap-notify.js"></script> <!-- Bootstrap Notify Plugin Js -->
<script type = "text/javascript" language = "javascript">
function showNotification(colorName, text, placementFrom, placementAlign, animateEnter, animateExit) {
    if (colorName === null || colorName === '') { colorName = 'bg-black'; }
    if (text === null || text === '') { text = 'Turning standard Bootstrap alerts'; }
    if (animateEnter === null || animateEnter === '') { animateEnter = 'animated fadeInDown'; }
    if (animateExit === null || animateExit === '') { animateExit = 'animated fadeOutUp'; }
    var allowDismiss = true;

    $.notify({
        message: text
    },
        {
            type: colorName,
            allow_dismiss: allowDismiss,
            newest_on_top: true,
            timer: 1000,
            placement: {
                from: placementFrom,
                align: placementAlign
            },
            animate: {
                enter: animateEnter,
                exit: animateExit
            },
            template: '<div data-notify="container" class="bootstrap-notify-container alert alert-dismissible {0} ' + (allowDismiss ? "p-r-35" : "") + '" role="alert">' +
            '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
            '<span data-notify="icon"></span> ' +
            '<span data-notify="title">{1}</span> ' +
            '<span data-notify="message">{2}</span>' +
            '<div class="progress" data-notify="progressbar">' +
            '<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
            '</div>' +
            '<a href="{3}" target="{4}" data-notify="url"></a>' +
            '</div>'
        });
}

function pay() {
	  $.ajax({
            url : 'api/pay',
            type: "POST",
            data: 'id=<?php echo ($idS); ?>' + "&csrf=<?php echo($csrfToken); ?>",
            success: function (data) {
				var obj = JSON.parse(data);
				if(obj.success == 1) {
					document.getElementById("paybtn").innerHTML="Successfully Paid";
					window.location = "dashboard";
				}
				showNotification("bg-black", obj.msg, "bottom", "left", "animated bounceInRight", "animated bounceOutRight");

                //$("#loginfrm").html(data);
            },
            error: function (jXHR, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
}
</script>
</body>
</html>