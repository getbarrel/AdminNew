<?php
include("../class/layout.class");



$db = new Database;
$mdb = new Database;


if($mode == "data_gruop"){
	$sql = "select dig.* , case when group_depth = 1 then group_ix  else parent_group_ix end as group_order from cms_data_group dig where group_depth = 1 and company_id ='".$admininfo["company_id"]."' order by  group_order asc , vieworder asc ";
	$db->query($sql);

	$mdb->query($sql);
		
		
	$datas = $mdb->fetchall2("object");
	
	for($i=0;$i < count($datas);$i++){
		$sql = "select dig.* , case when group_depth = 1 then group_ix  else parent_group_ix end as group_order 
				from cms_data_group dig where group_depth = 2 and parent_group_ix = '".$datas[$i]["group_ix"]."'
				order by  group_order asc , vieworder asc ";
		$mdb->query($sql);
		
		
		
		for($j=0;$j < $mdb->total;$j++){
			$mdb->fetch($j);
			$sub_trees[$j] = array("title"=>$mdb->dt["group_name"],"tooltip"=>$mdb->dt["group_name"],"key"=>$mdb->dt["group_ix"]);
			
		}

		$trees[$i] = array("title"=>$datas[$i]["group_name"],"tooltip"=>$datas[$i]["group_name"],"key"=>$datas[$i]["group_ix"],"isFolder"=>'true',"children"=>$sub_trees);
		unset($sub_trees);
	}
	//exit;
		
	//print_r($trees);
	$datas = json_encode($trees);
	//$datas = str_replace("\"true\"","true",json_encode($trees));
	//$datas = str_replace("\"false\"","false",$datas);
	echo $datas;

}
?>