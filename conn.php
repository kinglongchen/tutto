<?php
    $mysql_server_name = "localhost";
    $mysql_username = "root";
    $mysql_password = "";
    $mysql_database = "its";
	$conn = mysql_connect($mysql_server_name,$mysql_username,$mysql_password)or die("error"); // 建立连接
    mysql_select_db($mysql_database, $conn);
    mysql_query("set names 'GBK'");
    ?>
