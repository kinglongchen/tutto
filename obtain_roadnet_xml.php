<?php
function  obtain_roadnet_xml_by_ids($roadid_arr) {
	$sql = "select * from node_info where ROAD_ID=$roadid_arr[0]";
	while (count($roadid_arr)!=0) {
		$sql.=" or ROAD_ID=".array_pop($roadid_arr);
		}//for $i
		$sql.=" order by ROAD_ID,NODE_INDEX";
		$r=mysql_query($sql) or die("Invalid query: " . mysql_error());
		echo get_Roadnet_xml($r);
		//return get_Roadnet_xml($r);
	}

function obtain_roadnet_xml_all() {
	$sql = "select * from node_info";
	$r=mysql_query($sql) or die("Invalid query: " . mysql_error());
	return get_Roadnet_xml($r);
	}	
function get_Roadnet_xml($r) {
$xml= '<?xml version="1.0" encoding="utf-8"?>
<roads>';
$roadid=null;
$roadname=null;

while($row = mysql_fetch_array($r))
 {

	 if($roadid==null||$roadid!=$row['ROAD_ID'])
	 {
		 if($roadid!=null)
		 {
			 $xml.= "</road>\n";
			 }
 $xml.= "<road>\n";
 $xml.= "<roadid>".$row['ROAD_ID']."</roadid>\n";
	 }
 $xml.= "<node>\n";
 $xml.= "<nodeid>" . $row['NODE_ID'] . "</nodeid>\n";
 $xml.= "<nodx>" . $row['NODX'] . "</nodx>\n";
 $xml.= "<nody>" . $row['NODY'] . "</nody>\n";
 $xml.= "<nodeindex>".$row['NODE_INDEX']."</nodeindex>\n";
 $xml.= "</node>\n";
 if($roadid!=$row['ROAD_ID'])
 $roadid=$row['ROAD_ID'];
 }
 
$xml.= "</road>\n";
$xml.= "</roads>";
return $xml;
	
	}
?>