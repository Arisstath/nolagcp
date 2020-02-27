<?php
require_once("includes/config.php");
include("includes/userutils.php");

include 'classes/Cart.php';

$cart = new Cart;
?>
<!DOCTYPE html>
<html>
<?php

 include_once("includes/header.php"); ?>
<body class="theme-red">
<!-- Page Loader -->
<?php include_once("includes/loader.php"); ?>

<!-- Overlay For Sidebars -->
<div class="overlay"></div>

<!-- Top Bar -->
<?php include_once("includes/topbar.php"); ?>

<!-- Left & Right bar menu -->
<?php include_once("includes/sidebar.php"); ?>
<style>
	textarea {
    background-color: #000;
    border: 1px solid #000;
    color: #ffffff;
    padding: 8px;
    font-family: courier new;
	height: 500px;
}

</style>
<section class="content">

	<div class="container-fluid">
      <div class="row clearfix">
		<!--
            <div class="col-md-8 col-sm-7 col-xs-12">
                <div class="h-left clearfix">
                    <h2>Overview #<?php echo $id; ?></h2>
                    <small class="text-muted">Welcome to your server's overview page. From here you can control and view stats of your Minecraft server</small>

                </div>
            </div>
			!-->
	<!-- Indicators
	<div class="card">
        <div class="header">
            <h2>Server Stats</h2>
			</div>
			<div class="col-md-2">


                 <input id="ramusage" type="text" class="knob" value="35" data-width="125" data-height="125" data-thickness="0.25" data-angleArc="250" data-angleoffset="-125" data-fgColor="#f67a82" readonly>
				  <h4 style="position: relative;left: 18px;top: -30px;">RAM Usage</h4>
             </div>
			 <div class="col-md-2">
                 <input id="cpuusage" type="text" class="knob" value="35" data-width="125" data-height="125" data-thickness="0.25" data-angleArc="250" data-angleoffset="-125" data-fgColor="#f67a82" readonly>
				 <h4 style="position: relative;left: 18px;top: -30px;">CPU Usage</h4>
             </div>
    </div>
	!-->
	<!-- Console !-->

	<!-- Controls !-->

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	<div class="card">
		<div class="header">
		  <h2 id="status"><?php echo $messages['console_card4_title']; ?></h2>
		</div>
        <div class="body">
				 <select class="form-control" id="minecrafti">
                                            <option value="" disabled>Location (Minecraft)</option>
											 <option value="germany" selected>Nuremberg, Germany</option>
                                 <option value="dallas">Dallas, USA</option>
								<option value="kansas">Los Angeles, USA</option>
                                        </select>
        </div>


		</div>
		</div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
	<div class="card">
		<div class="header">
		  <h2 id="status"><?php echo $messages['console_card4_title']; ?></h2>
		</div>
        <div class="body">
					<div class="body table-responsive">
                       <table class="table table-responsive">
    <thead>
        <tr>
            <th><?php echo $messages['dashboard_viewcart_card2_product']; ?></th>
            <th><?php echo $messages['dashboard_viewcart_card2_price']; ?></th>
            <th><?php echo $messages['dashboard_viewcart_card2_quantity']; ?></th>
            <th><?php echo $messages['dashboard_viewcart_card2_subtotal']; ?></th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if($cart->total_items() > 0){
            //get cart items from session
            $cartItems = $cart->contents();
            foreach($cartItems as $item){
        ?>
        <tr>
            <td><?php echo $item["name"]; ?></td>
            <td><?php echo '$'.$item["price"].' USD'; ?></td>
            <td><input type="number" class="form-control" value="<?php echo $item["qty"]; ?>" onchange="updateCartItem(this, '<?php echo $item["rowid"]; ?>')"></td>
            <td><?php echo '$'.$item["subtotal"].' USD'; ?></td>
            <td>
                <!--<a href="cartAction.php?action=updateCartItem&id=" class="btn btn-info"><i class="glyphicon glyphicon-refresh"></i></a>-->
                 <!--<a href="cartAction.php?action=removeCartItem&id=<?php echo $item["rowid"]; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')"><i class="glyphicon glyphicon-trash"></i></a>-->
				<a onClick="removeCartItem('<?php echo $item["rowid"] ?>');" class="btn  btn-raised btn-danger waves-effect btn-xs">Remove</a>
            </td>
        </tr>
        <?php } }else{ ?>
        <tr><td colspan="5"><p>You don't have items in your cart.</p></td>
        <?php } ?>
    </tbody>
    <tfoot>
        <tr>
            <td><a href="buy" class="btn  btn-raised btn-warning waves-effect"> <?php echo $messages['dashboard_viewcart_card2_continue']; ?></a></td>
            <td colspan="2"></td>
            <?php if($cart->total_items() > 0){ ?>
            <td class="text-center"><strong>Total <?php echo '$'.$cart->total().' USD'; ?></strong></td>
            <td><a onclick="checkout();" class="btn  btn-raised btn-success waves-effect"><?php echo $messages['dashboard_viewcart_card2_order']; ?></a></td>
            <?php } ?>
        </tr>
    </tfoot>
    </table>
                    </div>
        </div>


		</div>
		</div>




    </div>
 </div>


</section>
<div class="modal fade" id="largeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="largeModalLabel"><?php echo $messages['console_modal_title']; ?></h4>
            </div>
            <div class="modal-body"><input disabled="" class="form-control" id="ftppass" onfocus="this.select();" onClick="this.setSelectionRange(0, this.value.length)" value="<?php echo $ftppass; ?>" id="disabled" type="text" class="validate"></input></div>
            <div class="modal-footer">
                <button type="button" onclick="resetFTP();" class="btn btn-link waves-effect"><?php echo $messages['console_modal_reset']; ?></button>
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal"><?php echo $messages['console_modal_close']; ?></button>
            </div>
        </div>
    </div>
</div>
<!-- Jquery Core Js -->
<script src="https://nolag.r.worldssl.net/panel/assets/bundles/libscripts.bundle.js"></script> <!-- Lib Scripts Plugin Js -->
<script src="https://nolag.r.worldssl.net/panel/assets/bundles/vendorscripts.bundle.js"></script> <!-- Lib Scripts Plugin Js -->

<script src="https://nolag.r.worldssl.net/panel/assets/plugins/jquery-sparkline/jquery.sparkline.js"></script> <!-- Sparkline Plugin Js -->

<script src="https://nolag.r.worldssl.net/panel/assets/bundles/mainscripts.bundle.js"></script><!-- Custom Js -->
<script src="https://nolag.r.worldssl.net/panel/assets/js/pages/charts/sparkline.js"></script>
<script src="https://nolag.r.worldssl.net/panel/assets/plugins/jquery-knob/jquery.knob.min.js"></script> <!-- Jquery Knob Plugin Js -->
<script src="https://nolag.r.worldssl.net/panel/assets/js/pages/charts/jquery-knob.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<script src="https://nolag.r.worldssl.net/panel/assets/js/pages/ui/modals.js"></script>
<script src="https://nolag.r.worldssl.net/panel/assets/plugins/bootstrap-notify/bootstrap-notify.js"></script>
               <script>

    function updateCartItem(obj,id){
        $.get("api/updateCart" + "?csrf=<?php echo($csrfToken); ?>", {id:id, qty:obj.value}, function(data){
			var response = JSON.parse(data);
            if(response.success == 1){
                location.reload();
            }else{
              swal("Failed!", response.msg, "error");
            }
        });
    }

	function checkout(){
		//Verify first user's choice
		var e = document.getElementById("minecrafti");
		var locationw = e.options[e.selectedIndex].value;

		swal({
  title: "Is the location correct?",
  text: "Your server(s) will be provisioned in <b>" + e.options[e.selectedIndex].text + "</b>.",
  type: "info",
  html: true,
  showCancelButton: true,
  closeOnConfirm: false,
  showLoaderOnConfirm: true,
},
function(){
   $.get("api/checkout?location=" + locationw + "&csrf=<?php echo($csrfToken); ?>", function(data){
			var response = JSON.parse(data);
            if(response.success == 1){
                window.location = response.url;
            }else{
              swal("Failed!", response.msg, "error");
            }
        });
});

    }
	function removeCartItem(id){
        $.get("api/delCart" + "?csrf=<?php echo($csrfToken); ?>", {id:id}, function(data){
			var response = JSON.parse(data);
            if(response.success == 1){
                location.reload();
            }else{
              swal("Failed!", response.msg, "error");
            }
        });
    }
		</script>
</body>
</html>