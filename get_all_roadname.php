<?php
include "conn.php";
$sql = "select * from rnameinfo";
//mysql_query("set names 'gbk'");
$r=mysql_query($sql) or die("Invalid query: " . mysql_error());
$roadname="";
while($row = mysql_fetch_array($r))
 {
 $roadname.=$row['ID'].",";
 $roadname.=$row['NAME'].",";
 $roadname.=$row['DIRECTION'].",";
 $roadname.=$row['ISEXIT'].";";
	}
$rname=substr($roadname,0,strlen($roadname)-1);
echo iconv("gbk","UTF-8",$rname);
?>