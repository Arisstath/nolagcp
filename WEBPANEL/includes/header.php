<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=Edge">
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<title><?php echo $pageTitle . " | NoLagCP" ?></title>
<link rel="icon" href="favicon.ico" type="image/x-icon">
<!-- Favicon-->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">
<!-- Custom Css -->
<link href="https://nolag.r.worldssl.net/panel/assets/css/main.css" rel="stylesheet">
<!-- AdminCC You can choose a theme from css/themes instead of get all themes -->
<link href="https://nolag.r.worldssl.net/panel/assets/css/themes/all-themes.css" rel="stylesheet" />
<!-- JQuery DataTable Css -->
<link href="https://nolag.r.worldssl.net/panel/assets/plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css" rel="stylesheet">
<!-- Bootstrap Select Css -->
<link href="https://nolag.r.worldssl.net/panel/assets/plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<script src="../socket.io.js"></script>

<?php
if($pageTitle == "Login" || $pageTitle == "Reset Password"){
?>
<link href="https://nolag.r.worldssl.net/panel/assets/css/login.css" rel="stylesheet">
<?php
}
?>
<script src="https://cdn.ravenjs.com/3.17.0/raven.min.js" crossorigin="anonymous"></script>
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
</head>
