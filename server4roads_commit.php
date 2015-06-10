<?php
header('Content-Type: text/xml');
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
include "obtain_roadnet_xml.php";
include "conn.php";
$road_style=1;
$new_roadid_arr=array();
$node_str=$_POST["node_str"];
//$node_str='3,解放路(延安路-浣纱路)西向东#120.14772892,30.26638793,120.14772892,30.26638793,120.14772892,30.26638793,120.15431643,30.26705511;4,解放路(延安路-浣纱路)东向西#120.15682679,30.2674443,120.15862942,30.26573968';

$road_info_arr=explode(';',$node_str);
foreach ($road_info_arr as $road_info) {
	$temp_info_arr = explode('#',$road_info);
	$road_name_info = $temp_info_arr[0];
	$road_node_info = $temp_info_arr[1];
	$temp_name_info_arr = explode(',',$road_name_info);	
	$road_id = $temp_name_info_arr[0];
	$road_name = iconv("UTF-8","gbk",$temp_name_info_arr[1]);
	$sql = "INSERT INTO road_info(ROAD_ID,NAME,STYLE) VALUES ($road_id,'$road_name',$road_style)";
	$r=mysql_query($sql) or die("Invalid query: " . mysql_error());
	//$roadid = last_insert_id();
	$nodes_info=explode(',',$road_node_info);
	for ($i=0;$i<count($nodes_info);$i+=2) {
		$nodx = $nodes_info[$i];
		$nody = $nodes_info[$i+1];
		$node_flag=2;
		if($i==0)$node_flag=1;
		if($i==count($nodes_info)-2)$node_flag=0;
		
	$sql = "INSERT INTO node(NODX,NODY,FLAG,ROAD_COUNT) VALUES ($nodx,$nody,$node_flag,1)";
	$r=mysql_query($sql) or die("Invalid query: " . mysql_error());
	$nodeid= last_insert_id();
	$sql = "INSERT INTO node_info(ROAD_ID,NODE_INDEX,NODE_ID,NODX,NODY,FLAG) VALUES ($road_id,$i/2,$nodeid,$nodx,$nody,$node_flag)";
	$r=mysql_query($sql) or die("Invalid query: " . mysql_error());
		}//for()
	$sql = "update rnameinfo set ISEXIT=1 where ID=$road_id";
	mysql_query($sql) or die("Invalid query: " . mysql_error());
	array_push($new_roadid_arr,$road_id);
	}
echo obtain_roadnet_xml_by_ids($new_roadid_arr);
//echo obtain_roadnet_xml_all();
//返回编号的函数
function last_insert_id() {
	$r=mysql_query("SELECT LAST_INSERT_ID()");
	$last_insert_roadid = mysql_fetch_array($r);
	return $last_insert_roadid[0];
	}
mysql_close($conn);
?>