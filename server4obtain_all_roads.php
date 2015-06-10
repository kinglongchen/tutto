<?php
header('Content-Type: text/xml');
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
include "obtain_roadnet_xml.php";
include "conn.php";
echo obtain_roadnet_xml_all();
mysql_close($conn);
?>