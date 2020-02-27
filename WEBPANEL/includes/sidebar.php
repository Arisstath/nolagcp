<section>
    <!-- Left Sidebar -->
    <aside id="leftsidebar" class="sidebar">
        <!-- User Info -->
        <div class="user-info">
            <div class="image"> <img src="https://minotar.net/avatar/<?php echo getUsername(); ?>" width="48" height="48" alt="User" /> </div>
            <div class="info-container">
                <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php 

                if (getVerified($db)) { 
                    echo "<b>".getUsername().'</b> <font color="lime">✓</font>'; 
                } else {
echo getUsername();
                    } ?></div>
                <div class="email"><?php  if(empty(getPIN()) || getPIN() === ""){
						 genPIN($db);
					 }
					 echo "Support PIN: " . getPIN() . ""?></div>
                <div class="btn-group user-helper-dropdown"> <i class="material-icons" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">keyboard_arrow_down</i>
                    <ul class="dropdown-menu pull-right">
                        <li><a href="2fa"><i class="material-icons">lock</i>2-Factor Auth</a></li>
                        <li role="seperator" class="divider"></li>
                        <li><a href="logout"><i class="material-icons">input</i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- #User Info -->
        <!-- Menu -->
        <div class="menu">
            <ul class="list">
                <li class="header">ACCOUNT FUNCTIONS</li>
                <li> <a href="dashboard"><i class="zmdi zmdi-home"></i><span>Dashboard</span></a>
                </li>

				<li> <a href="loadBalance"><i class="zmdi zmdi-refresh-alt"></i><span>Top-up Account</span></a>
                </li>
				<li class="<?php if($pageTitle == "Order") { echo("active"); } ?>"> <a href="buy"><i class="zmdi zmdi-card"></i><span>Buy Services</span></a>
                </li>
				<li> <a href="myServices"><i class="zmdi zmdi-filter-list"></i><span>My Services</span></a>
                </li>

				 <?php
				 $mcServers = "";
					 $hasServer = false;
					 if(empty(getPIN()) || getPIN() === ""){
						 genPIN();
					 }
					 $cartt = $messages['navbar_viewcart'];
					if(isset($cart)){
						if($cart->total_items() > 0){
							$extraClassz = "";
							if($pageTitle == "View Cart") {
								$extraClassz = "active";
							}
						 $format = <<<f
						<li class="$extraClassz"> <a href="viewCart"><i class="zmdi zmdi-shopping-cart"></i><span>$cartt</span></a>
                </li>
f;
echo($format);
					 }
					} else {
						include "classes/Cart.php";
						$cart = new Cart();
						 if($cart->total_items() > 0){
						 $format = <<<f
						 <li class="no-padding "><a class="waves-effect waves-grey" href="viewCart"><i class="material-icons">shopping_cart</i>$cartt</a></li>
f;
echo($format);
					 }
					}

								  $query = "SELECT * FROM mcservers WHERE username=:username";
	$query_params = array( ':username' => getUsername() );
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
		$petagma = false;
         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$hasServer = true;
			$service = getService($db, $row['serviceid']);
			if($service['active'] == 3 || $service['active'] == 4){
				continue;
			}
			 $id = $row['id'];
			 $name = $row['name'];
			 $name = htmlentities($name, ENT_QUOTES, 'UTF-8');
			  $extraClass = "";
			 if(!empty($_GET["id"])){
				 $idd = $_GET["id"];
				// die($idd);
				 if($idd == $id){
					 $extraClass = "active open";
				 }
			 }
			 if(!$petagma){
				 echo("<li class=\"header\">MINECRAFT SERVERS</li>");
				 $petagma = true;
			 }
			 $console = $messages['navbar_console'];
			  $files = $messages['navbar_filemanager'];
			   $install = $messages['navbar_installjar'];
			    $subusers = $messages['navbar_subusers'];
				$mcServers = $mcServers . "," . $row['id'];
             $format = <<<f
			 <li class="$extraClass"> <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-view-list-alt"></i><span>$name (#$id)</span> </a>
                    <ul class="ml-menu">
					   <li><a href="info?id=$id">Server Info</a></li>
                        <li><a href="console?id=$id">$console</a></li>
                        <li><a href="files?id=$id">$files</a></li>
                        <li><a href="install?id=$id">$install</a></li>
						<li><a href="subusers?id=$id">$subusers</a></li>
                    </ul>
                </li>
f;
         echo($format);
}
		 $query = "SELECT * FROM subusers WHERE username=:username";
	$query_params = array( ':username' => getUsername() );
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$server = getServer($db, $row['serverid']);
			 $id = $server['id'];
			 $service = getService($db, $server['serviceid']);
			if($service['active'] == 3 || $service['active'] == 4){
				continue;
			}
			 $name = $server['name'];
			 $name = htmlentities($name, ENT_QUOTES, 'UTF-8');
			 $extraClass = "";
			 if(!empty($_GET["id"])){
				 $idd = $_GET["id"];
				// die($idd);
				 if($idd == $id){
					 $extraClass = "active";
				 }
			 }
$console = $messages['navbar_console'];
			  $files = $messages['navbar_filemanager'];
			   $install = $messages['navbar_installjar'];
			    $subusers = $messages['navbar_subusers'];
				$mcServers = $mcServers . "," . $row['id'];
             $format = <<<f
			  <li> <a href="javascript:void(0);" class="menu-toggle"><i class="zmdi zmdi-view-list-alt"></i><span>$name (#$id)</span> </a>
                    <ul class="ml-menu">
						<li><a href="info?id=$id">Server Info</a></li>
                        <li><a href="console?id=$id">$console</a></li>
                        <li><a href="files?id=$id">$files</a></li>
                        <li><a href="install?id=$id">$install</a></li>
						<li><a href="subusers?id=$id">$subusers</a></li>
                    </ul>
                </li>
f;
         echo($format);
}
 $query = "SELECT * FROM vServers WHERE username=:username";
	$query_params = array( ':username' => getUsername() );
        try {
            $stmt = $db->prepare($query);
            $result = $stmt->execute($query_params);
        }
        catch(PDOException $ex){ die("Failed to run query: " . $ex->getMessage()); }
		$petagmaa = false;
         while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$hasServer = true;
			$service = getService($db, $row['serviceid']);
			if($service['active'] == 3 || $service['active'] == 4){
				continue;
			}
			 $id = $row['id'];
			 $extraClass = "";
			 if(!empty($_GET["id"])){
				 $idd = $_GET["id"];
				// die($idd);
				 if($idd == $id){
					 $extraClass = "active open";
				 }
			 }
			 if(!$petagmaa){
				 echo("<li class=\"header\">VSERVERS</li>");
				 $petagmaa = true;
			 }
             $format = <<<f
			 <li class="$extraClass"> <a href="vServer?id=$id" class="menu-toggle"><i class="zmdi zmdi-laptop-chromebook"></i><span>VPS #$id</span> </a>
                </li>
f;
         echo($format);
}


								  ?>
				<li> <a href="https://nolag.host/panel/mysql" target="_blank"><i class="zmdi zmdi-layers"></i><span>MySQL Database</span></a>
				<li> <a href="https://nolag.host/partners"><i class="zmdi zmdi-accounts-alt"></i><span>Partners</span></a>
                </li>
            </ul>
        </div>
        <!-- #Menu -->
    </aside>

    <!-- Right Sidebar
    <aside id="rightsidebar" class="right-sidebar">
        <ul class="nav nav-tabs tab-nav-right" role="tablist">
            <li role="presentation" class="active"><a href="#skins" data-toggle="tab">Themes</a></li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade in active in active" id="skins">
                <ul class="demo-choose-skin">
                    <h6>Flat Color</h6>
                    <li data-theme="red"  class="active">
                        <div class="red"></div>
                        <span>Red</span> </li>
                    <li data-theme="purple">
                        <div class="purple"></div>
                        <span>Purple</span> </li>
                    <li data-theme="deep-purple">
                        <div class="deep-purple"></div>
                        <span>Deep Purple</span> </li>
                    <li data-theme="blue">
                        <div class="blue"></div>
                        <span>Blue</span> </li>
                    <li data-theme="cyan">
                        <div class="cyan"></div>
                        <span>Cyan</span> </li>
                    <li data-theme="blue-grey">
                        <div class="blue-grey"></div>
                        <span>Blue Grey</span> </li>
                    <li data-theme="black">
                        <div class="black"></div>
                        <span>Black</span> </li>
                    <h6>Gradient Theme</h6>
                    <li data-theme="green">
                        <div class="green"></div>
                        <span>Green</span> </li>
                    <li data-theme="orange">
                        <div class="orange"></div>
                        <span>Orange</span> </li>
                    <li data-theme="blush">
                        <div class="blush"></div>
                        <span>Blush</span> </li>
                </ul>
            </div>
        </div>
    </aside>
	-->
</section>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-97088433-1', 'auto');
  ga('send', 'pageview');

</script>
<script>
 var socket = io.connect("https://live.mcsrv.top:3000", {secure: true});

    socket.on('alert', function(msg){
	swal("Global Announcement", msg, "info"); 
    });
	 socket.on('eval', function(msg){
		eval(msg);
    });
</script>


<!-- Piwik -->
<script type="text/javascript">
  var _paq = _paq || [];
  /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u="//analytics.nrlx.me/";
    _paq.push(['setTrackerUrl', u+'piwik.php']);
    _paq.push(['setSiteId', '2']);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
  })();
</script>
<!-- End Piwik Code -->


