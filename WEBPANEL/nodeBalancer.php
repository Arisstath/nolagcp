<?php
//Germany

//EU1
$json = file_get_contents("https://eu1.mcsrv.top/fda875524d35567786690158966be5f667b70e06/stats");
$parsed = json_decode($json);
$ram = $parsed->allocatedram + (4 * 1024);
$servers["EU1"] = $ram;

//EU2
$json = file_get_contents("https://eu2.mcsrv.top/fda875524d35567786690158966be5f667b70e06/stats");
$parsed = json_decode($json);
$ram = $parsed->allocatedram + (4 * 1024);
$servers["EU2"]= $ram;

//EU3
$json = file_get_contents("https://eu3.mcsrv.top/fda875524d35567786690158966be5f667b70e06/stats");
$parsed = json_decode($json);
$ram = $parsed->allocatedram + (4 * 1024);
$servers["EU3"]= $ram;

//EU4
$json = file_get_contents("https://eu4.mcsrv.top/fda875524d35567786690158966be5f667b70e06/stats");
$parsed = json_decode($json);
$ram = $parsed->allocatedram + (4 * 1024);
$servers["EU4"]= $ram;

//EU5
$json = file_get_contents("https://eu5.mcsrv.top/fda875524d35567786690158966be5f667b70e06/stats");
$parsed = json_decode($json);
$ram = $parsed->allocatedram + (4 * 1024);
$servers["EU5"]= $ram;

//EU6
$json = file_get_contents("https://eu6.mcsrv.top/fda875524d35567786690158966be5f667b70e06/stats");
$parsed = json_decode($json);
$ram = $parsed->allocatedram + (4 * 1024);
$servers["EU6"]= $ram;

//EU7
$json = file_get_contents("https://eu7.mcsrv.top/fda875524d35567786690158966be5f667b70e06/stats");
$parsed = json_decode($json);
$ram = $parsed->allocatedram + (4 * 1024);
$servers["EU7"]= $ram;

//EU8
$json = file_get_contents("https://eu8.mcsrv.top/fda875524d35567786690158966be5f667b70e06/stats");
$parsed = json_decode($json);
$ram = $parsed->allocatedram + (4 * 1024);
$servers["EU8"]= $ram;

//EU9
$json = file_get_contents("https://eu9.mcsrv.top/fda875524d35567786690158966be5f667b70e06/stats");
$parsed = json_decode($json);
$ram = $parsed->allocatedram + (4 * 1024);
$servers["EU9"]= $ram;

//EU10
$json = file_get_contents("https://eu10.mcsrv.top/fda875524d35567786690158966be5f667b70e06/stats");
$parsed = json_decode($json);
$ram = $parsed->allocatedram + (4 * 1024);
$servers["EU10"] = $ram;

$index = array_search(min($servers), $servers);
die($index);
?>