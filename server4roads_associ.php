<?php
header('Content-Type: text/xml');
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
include "obtain_roadnet_xml.php";
include "conn.php";
$associ_road_str=$_POST["associ_road_str"];
$roadid_arr=array();
//$associ_road_str="32,1;33,0";
$associ_road_node_str_arr=explode(';',$associ_road_str);
$refren_nodeid=get_refren_nodeid($associ_road_node_str_arr);
$associ_road_node_str_arr=deal_cross_node($associ_road_node_str_arr);//这一步检查是否有连接过的点

$sql="select * from node where NODE_ID=$refren_nodeid";

$r=mysql_query($sql) or die("Invalid query: " . mysql_error());
$row = mysql_fetch_array($r);
$refren_nodx=$row['NODX'];
$refren_nody=$row['NODY'];
$road_count=count($associ_road_node_str_arr);
$sql="update node set FLAG=4,ROAD_COUNT=$road_count where NODE_ID=$refren_nodeid";
if(mysql_query($sql))//更新成功了才执行其他的修改：修改node_info相应路段的节点信息，删除多余出来的node信息
foreach ($associ_road_node_str_arr as $associ_road_node_str) {
	$temp_roadid=get_update_roadid($associ_road_node_str);
	$temp_nodeid=get_update_nodeid($associ_road_node_str);
	$sql="update node_info set NODE_ID=$refren_nodeid,NODX=$refren_nodx,NODY=$refren_nody,FLAG=4 where NODE_ID=$temp_nodeid and ROAD_ID=$temp_roadid";
	if(!mysql_query($sql))break;
	if($temp_nodeid!=$refren_nodeid) {
		$sql = "delete from node where NODE_ID=$temp_nodeid";
		if(!mysql_query($sql))break;
		}
	array_push($roadid_arr,$temp_roadid);
	}
echo obtain_roadnet_xml_by_ids($roadid_arr);


	//处理一开始就是已经连接的点,并返回适当的参考点
function deal_cross_node($associ_road_node_str_arr) {
	$new_associ_road_nodee_str_arr=array();
	foreach ($associ_road_node_str_arr as $associ_road_node_str) {
		$temp_nodeid=get_update_nodeid($associ_road_node_str);
		$sql="select * from node_info where NODE_ID=$temp_nodeid";
		$r=mysql_query($sql);
		while ($row=mysql_fetch_array($r)) {
			if($row['FLAG']!=4)break;
			$new_road_node_str="$row[ROAD_ID],".$row['NODE_INDEX'];
				if (!in_array($new_road_node_str,$associ_road_node_str_arr)) {
					array_push($new_associ_road_nodee_str_arr,$new_road_node_str);
					}
			}
		}
		return  array_merge($associ_road_node_str_arr,$new_associ_road_nodee_str_arr);
	}
function get_refren_nodeid($associ_road_node_str_arr) {
	$temp_nodeid=get_update_nodeid($associ_road_node_str_arr[0]);
	$row=mysql_fetch_array(mysql_query("select * from node where NODE_ID=$temp_nodeid"));
	$refren_node_id=$row['NODE_ID'];
	$refren_node_road_count=$row['ROAD_COUNT'];
	foreach ($associ_road_node_str_arr as $associ_road_node_str) {
		$temp_nodeid=get_update_nodeid($associ_road_node_str);
		$row=mysql_fetch_array(mysql_query("select * from node where NODE_ID=$temp_nodeid"));
		if ($refren_node_road_count<$row['ROAD_COUNT']) {
			$refren_node_road_count=$row['ROAD_COUNT'];
			$refren_node_id=$temp_nodeid;
			}
		}
		
		return $refren_node_id;	
	
	}




function get_update_roadid($associ_road_node_str) {
	$temp_node=explode(',',$associ_road_node_str);
	return $temp_node[0];
	}
function get_update_nodeid($associ_road_node_str) {
	$temp_node=explode(',',$associ_road_node_str);
	$sql="select * from node_info where ROAD_ID=$temp_node[0] and NODE_INDEX=$temp_node[1]";
		$r=mysql_query($sql);
		$row = mysql_fetch_array($r);
		return $row['NODE_ID'];
	}


mysql_close($conn);
?>