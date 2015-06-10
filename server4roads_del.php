<?php
include "obtain_roadnet_xml.php";
include "conn.php";
$new_roadid_arr=array();
$delete_roadids_str=$_POST["delete_roadids_str"];
//$delete_roadids_str="45";
$delete_roadid_arr=explode(';',$delete_roadids_str);
foreach ($delete_roadid_arr as $delete_roadid) {
	$nodeid_arr=get_Road_nodes($delete_roadid);
	mysql_query("delete from node_info where ROAD_ID=$delete_roadid");
	for($i=0;$i<count($nodeid_arr);$i+=2) {
		if ($nodeid_arr[$i+1]==1) {//如果和这个节点相连的路段个数为1的话，则可以删除
			mysql_query("delete from node where NODE_ID=$nodeid_arr[$i]") or die("Invalid query: " . mysql_error());
			}
			else {//如果和这个点相连的路段不为1，则个数减一;
				$road_count=$nodeid_arr[$i+1]-1;
				if($road_count==1) {
					if($i==0) $flag=1;
					else $flag=0;
				$sql="update node set ROAD_COUNT=$road_count,FLAG=$flag where NODE_ID=$nodeid_arr[$i]";
				mysql_query("update node_info set FLAG=$flag where NODE_ID=$nodeid_arr[$i]") or die("Invalid query:".mysql_error());
				}
				else {
					$sql="update node set ROAD_COUNT=$road_count where NODE_ID=$nodeid_arr[$i]";
					}
				
				mysql_query($sql) or die("Invalid query:" . mysql_error());
				
				
				}
		}
	mysql_query("delete from road_info where ROAD_ID=$delete_roadid") or die("Invalid query: " . mysql_error());
	mysql_query("update rnameinfo set ISEXIT=0 where ID=$delete_roadid") or die("Invalid query: " . mysql_error());
	}
	echo "Delete roads has succeed";
function get_Road_nodes($roadid) {
	$nodeid_arr=array();
	$r=mysql_query("select * from node_info,node where ROAD_ID=$roadid and node_info.NODE_ID=node.NODE_ID order by NODE_INDEX") or die ("Invalid query: ". mysql_error());
	while($row=mysql_fetch_array($r)) {
		array_push($nodeid_arr,$row['NODE_ID']);
		array_push($nodeid_arr,$row['ROAD_COUNT']);
		} 
	return $nodeid_arr;
	}
mysql_close($conn);
?>