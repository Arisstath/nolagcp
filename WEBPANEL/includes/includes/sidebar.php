            <aside id="slide-out" class="side-nav white fixed">
                <div class="side-nav-wrapper" id="sidebaradmin">
                    <div class="sidebar-profile">
                        <div class="sidebar-profile-image">
                            <img src="assets/images/profile-image.png" class="circle" alt="">
                        </div>
                        <div class="sidebar-profile-info">
                            <a href="javascript:void(0);" class="account-settings-link">
                                <p><?php  if(empty(getPIN()) || getPIN() === ""){
						 genPIN($db);
					 }
					 echo getUsername() . " (" . getPIN() . ")"?></p>
                                <span><?php echo getActiveServices($db); ?> Services Active<i class="material-icons right">arrow_drop_down</i></span>
                            </a>
                        </div>
                    </div>
                    <div class="sidebar-account-settings">
                        <ul>
                            <li class="no-padding">
							 <a class="waves-effect waves-grey" href="sessions"><i class="material-icons">settings</i>Active Sessions</a>
                             <a class="waves-effect waves-grey" href="logout"><i class="material-icons">exit_to_app</i>Sign Out</a>
                            </li>
                        </ul>
                    </div>
                <ul class="sidebar-menu collapsible collapsible-accordion" data-collapsible="accordion">
                    <li class="no-padding "><a class="waves-effect waves-grey" href="dashboard"><i class="material-icons">settings_input_svideo</i><?php echo $messages['navbar_dashboard']; ?></a></li>
					 <li class="no-padding "><a class="waves-effect waves-grey" href="myServices"><i class="material-icons">settings_input_svideo</i>My Services</a></li>
					<li class="no-padding "><a class="waves-effect waves-grey" href="buy"><i class="material-icons">payment</i><?php echo $messages['navbar_orderservices']; ?></a></li>
					<li class="no-padding "><a class="waves-effect waves-grey" href="loadBalance"><i class="material-icons">payment</i><?php echo $messages['navbar_loadbalance']; ?></a></li>
					 <?php
					 $hasServer = false;
					 if(empty(getPIN()) || getPIN() === ""){
						 genPIN();
					 }
					 $cartt = $messages['navbar_viewcart'];
					if(isset($cart)){
						if($cart->total_items() > 0){
						 $format = <<<f
						 <li class="no-padding "><a class="waves-effect waves-grey" href="viewCart"><i class="material-icons">shopping_cart</i>$cartt</a></li>
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
					 $extraClass = "active";
				 }
			 }
			 $console = $messages['navbar_console'];
			  $files = $messages['navbar_filemanager'];
			   $install = $messages['navbar_installjar'];
			    $subusers = $messages['navbar_subusers'];
             $format = <<<f
			  <li class="no-padding">
                        <a class="collapsible-header waves-effect waves-grey $extraClass"><i class="material-icons">dns</i>$name (#$id)<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                        <div class="collapsible-body">
                            <ul>
                                <li><a href="console?id=$id">$console</a></li>
                                <li><a href="files?id=$id">$files</a></li>
                                <li><a href="install?id=$id">$install</a></li>
								<li><a href="subusers?id=$id">$subusers</a></li>
								<li><a href="plugins?id=$id">Plugins Installer</a></li>
                            </ul>
                        </div>
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
             $format = <<<f
			  <li class="no-padding">
                        <a class="collapsible-header waves-effect waves-grey $extraClass"><i class="material-icons">supervisor_account</i>$name (#$id)<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                        <div class="collapsible-body">
                            <ul>
                                <li><a href="console?id=$id">Console</a></li>
                                <li><a href="files?id=$id">File Manager</a></li>
                                <li><a href="install?id=$id">Install Jar</a></li>
                            </ul>
                        </div>
                    </li>
f;
         echo($format);
}
								  ?>
								  <?php
								  if(getRank() == 2){
									  $url = 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

$extraClass = "";
if (strpos($url,'admin') !== false) {
   $extraClass="active";
}
									  echo('
									   <li class="no-padding">
                        <a class="collapsible-header waves-effect waves-grey $extraClass"><i class="material-icons">perm_identity</i>Admin<i class="nav-drop-icon material-icons">keyboard_arrow_right</i></a>
                        <div class="collapsible-body">
                            <ul>
                                <li><a href="admin_users">Manage Users</a></li>
                                <li><a href="admin_servers">Manage Servers</a></li>
                                <li><a href="admin_invoices">Manage Invoices</a></li>
                            </ul>
                        </div>
                    </li>
									  ');		
								  									  }
								  ?>
								  
                   
					
			 <?php
			 if(getActiveServices($db) > 1){
			 ?>
			 <li class="no-padding "><a class="waves-effect waves-grey" href="mysql"><i class="material-icons">work</i>MySQL Database</a></li>
			 <?php
			 }
			 ?>
			 <li class="no-padding "><a class="waves-effect waves-grey" href="https://discord.gg/6ehjHM3"><i class="material-icons">chat_bubble_outline</i>Discord Server</a></li>
				
                </ul>
                <div class="footer">
                    <p class="copyright">© 2017 NoLagCP</p>
					<p>Made with ❤ in Greece</p>
                    <a href="https://nolag.host/tos.html">Privacy</a> &amp; <a href="https://nolag.host/tos.html">Terms</a>
                </div>
                </div>
            </aside>
			<script type="text/javascript"> window.$crisp=[];window.CRISP_WEBSITE_ID="df2c1904-daa4-4661-9c98-bed30f4549b6";(function(){ d=document;s=d.createElement("script"); s.src="https://client.crisp.im/l.js"; s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})(); </script>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-97088433-1', 'auto');
  ga('send', 'pageview');

</script>