<?php
include("../class/layout.work.class");
include("work.lib.php");


$db = new Database;
$mdb = new Database;

if($mode == "work_user"){
	$mdb->query("SELECT dp_name , dp_ix FROM shop_company_department where disp=1 and company_id ='".$admininfo["company_id"]."' order by dp_level asc ");
		
		
	$datas = $mdb->fetchall2("object");
	
	for($i=0;$i < count($datas);$i++){
		
		$sql = "select AES_DECRYPT(UNHEX(cmd.name),'".$db->ase_encrypt_key."')  as charger, cmd.code as charger_ix 
				from common_member_detail cmd , common_user cu , service_ing si
				where cu.code = cmd.code and authorized = 'Y' 
				and cmd.code = si.mem_ix and si.service_div = 'APP' and si.solution_div = 'WORK' and si.si_status = 'SI'
				and company_id ='".$admininfo["company_id"]."'  
				and department = '".$datas[$i]["dp_ix"]."' ";
		$mdb->query($sql);
		
		
		
		for($j=0;$j < $mdb->total;$j++){
			$mdb->fetch($j);
			$sub_trees[$j] = array("title"=>$mdb->dt["charger"],"tooltip"=>$mdb->dt["charger"],"key"=>$mdb->dt["charger_ix"]);
			
		}

		$trees[$i] = array("title"=>$datas[$i]["dp_name"],"tooltip"=>$datas[$i]["dp_name"],"key"=>$datas[$i]["dp_ix"],"isFolder"=>'true',"children"=>$sub_trees);
		unset($sub_trees);
	}
	//exit;
		
	//print_r($trees);
	$datas = json_encode($trees);
	
	echo $datas;
}

if($mode == "work_group"){
	$sql = "select abg.* , case when group_depth = 1 then group_ix  else parent_group_ix end as group_order 
				from work_group abg where disp=1 
				and group_depth = 1 
				and company_id ='".$admininfo["company_id"]."' 
				and disp = 1
				order by  group_order asc , vieworder asc ";
	$db->query($sql);

	$mdb->query($sql);
		
		
	$datas = $mdb->fetchall2("object");
	
	for($i=0;$i < count($datas);$i++){
		$sql = "select abg.* , case when group_depth = 1 then group_ix  else parent_group_ix end as group_order 
				from work_group abg where disp=1 and  group_depth = 2 and parent_group_ix = '".$datas[$i]["group_ix"]."'
				order by  group_order asc , vieworder asc ";
		$mdb->query($sql);
		
		
		
		for($j=0;$j < $mdb->total;$j++){
			$mdb->fetch($j);
			$sub_trees[$j] = array("title"=>"<span style='color:black;'>■ ".$mdb->dt["group_name"]."</span>","tooltip"=>$mdb->dt["group_name"],"key"=>$mdb->dt["group_ix"]);
			
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