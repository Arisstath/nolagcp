<?php
   $cdnURL = "https://nolag.r.worldssl.net/";
   require __DIR__.'/../classes/NoLagSession.php';  
   require_once __DIR__.'/database.php';  
   require(__DIR__ . "/GoogleAuthenticator.php");
   $ga = new PHPGangsta_GoogleAuthenticator();
    // These variables define the connection information for your MySQL database 
    $assetsURL = "";
	//if you should start session or no, used to check if run from NoLagSession.php
    $whm_secret = '43d3d273d1094bff1f6ae7173daa0b24
58d0823aaaa4c0a3b5bfb9f35006ffe3
2122a21e165cbda4f8bd40a14ca46ed1
087bb8ebd8a511ba28f0249180bc8557
fcbfe8accb51df2140231febcfc76bad
98195150975b0e9cecdcb6c65a56147f
6cb52b31129c57b91ccc13e5c4de6925
06029549fe8fc7585109e66879ba84c5
b68c7e7a8c6af070bbc4bc0a2737fee9
6b6868273cbd6a84f6cf8d77fb8e10d3
22f50705fca0a12372f1b069634383b5
9916b8334512c9fc6c10f246e985034a
8c5b0579c0fe04d463abc78a12b4ae94
9d64bddac8cff524d6bb3782caa319ce
6a44a4f491c0e9d2b6ac07f84fc1a423
6b196dcad6c26c8ae6ec4b85811ea6d8
1bd3a4f7e6584b33d2a7d374d7377ecb
0966c263c67f02bc7247fa38ce6c77c9
ad0794b4a5a0827d683cc7b3158b42c4
af84f27049d572b552022ba9ea9404a7
d9a6bcf56dfaf3b3f3b36c341a099d8f
bb36c74bfca559bc834d0fbbf9f67ff2
26f2bdbbd52ca9af26488042230da54c
eeff14aeb0a6e6dac81ad7056c27f3fb
b628a58b714a274dd8f603428837c85e
c203738b38352c9fd76a4835c094e0f0
d23bcd062fe1342b8636b688c9316fd3
257ab3843519ea0712eaa5c1c85a05c3
23eb798623940773f7796fa2a55b7790
ca5bac1ec3e1150a3c17cb40b1910c6f
';
   
    header('Content-Type: text/html; charset=utf-8');
	include("/var/www/nolag.host/panel/languages/english.php");
	$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
	$lang = "ENGLISHDEFAULT";
switch ($lang){
    case "el":
        include("/var/www/nolag.host/panel/languages/greek.php");
        break;  
    case "zh":
        include("/var/www/nolag.host/panel/languages/chinese.php");
        break;  
    case "da":
        include("/var/www/nolag.host/panel/languages/danish.php");
        break;  		
    case "nl":
        include("/var/www/nolag.host/panel/languages/dutch.php");
        break;  
    case "he":
        include("/var/www/nolag.host/panel/languages/hebrew.php");
        break;  
    case "ja":
        include("/var/www/nolag.host/panel/languages/japanese.php");
        break;  
    case "ro":
        include("/var/www/nolag.host/panel/languages/romanian.php");
        break;  
    case "ru":
        include("/var/www/nolag.host/panel/languages/russian.php");
        break;  
    case "es":
        include("/var/www/nolag.host/panel/languages/spanish.php");
        break;  
    case "tr":
        include("/var/www/nolag.host/panel/languages/turkish.php");
        break;
    case "de":
        include("/var/www/nolag.host/panel/languages/german.php");
        break; 
}

	session_set_cookie_params(86400);
	$jsVersion = 1;
	new NoLagSession($db);
   // session_start(); 
?>