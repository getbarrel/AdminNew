<?



function getDeepZoomGroupInfoSelect($obj_id, $obj_txt, $parent_group_ix, $selected, $return_type="select", $depth=1, $property=""){

	$mdb = new Database;
	
	if($depth == 1){
		$sql = 	"SELECT dzg.*
				FROM deepzoom_image_group dzg 
				where group_depth = '$depth'
				group by group_ix ";
	}else if($depth == 2){
		$sql = 	"SELECT dzg.*
				FROM deepzoom_image_group dzg 
				where group_depth = '$depth' and parent_group_ix = '$parent_group_ix'
				group by group_ix ";
	}
	//echo $sql;
	$mdb->query($sql);
	
	if($return_type == "select"){
		$mstring = "<select name='$obj_id' id='$obj_id' $property>";
		$mstring .= "<option value=''>$obj_txt</option>";
		if($mdb->total){
			
			
			for($i=0;$i < $mdb->total;$i++){
				$mdb->fetch($i);
				
				if($mdb->dt[group_ix] == $selected){
					$mstring .= "<option value='".$mdb->dt[group_ix]."' selected>".$mdb->dt[group_name]."</option>";
				}else{
					$mstring .= "<option value='".$mdb->dt[group_ix]."'>".$mdb->dt[group_name]."</option>";
				}
			}
			
		}	
		$mstring .= "</select>";
		return $mstring;
	}else{
		$datas = $mdb->fetchall();
		return $datas;
	}
	
	
}


function workCompanyUserList($company_id, $object_id = "charger_ix", $department="", $charger_ix="",$property=""){
	$mdb = new Database;
	if($department){
		$sql = "SELECT ci.company_id, cmd.code, cmd.name , cmd.mail , cmd.pcs 
				FROM ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd, ".TBL_COMMON_COMPANY_DETAIL." ccd, service_ing si
				WHERE cu.code = cmd.code and cu.company_id = cu.company_id 
				and cu.code = si.mem_ix
				and cmd.department = TRIM('".$department."')  ";

		$mdb->query($sql);

	}else{
		$sql = "SELECT ci.company_id, cmd.code, cmd.name , cmd.mail , cmd.pcs 
				FROM ".TBL_COMMON_USER." cu, ".TBL_COMMON_MEMBER_DETAIL." cmd, ".TBL_COMMON_COMPANY_DETAIL." ccd, service_ing si
				WHERE cu.code = cmd.code and cu.company_id = cu.company_id 
				and cu.code = si.mem_ix ";

		$mdb->query($sql);
	}
	if ($mdb->total){
			$SelectString = "<Select name='".$object_id."' id='".str_replace("[]","",$object_id)."' $property>";
			$SelectString = $SelectString."<option value=''>담당자 선택</option>";
		for($i=0; $i < $mdb->total; $i++){
			$mdb->fetch($i);
			
			if(is_array($charger_ix)){
				if(in_array($mdb->dt[code],$charger_ix)){
					$SelectString = $SelectString."<option value='".$mdb->dt[code]."' selected>".$mdb->dt[name]." </option>";
				}else{
					//$SelectString = $SelectString."<option value='".$mdb->dt[code]."'>".$mdb->dt[name]."</option>";
				}
			}else{
				if($charger_ix == $mdb->dt[code]){
					$SelectString = $SelectString."<option value='".$mdb->dt[code]."' selected>".$mdb->dt[name]." </option>";
				}else{
					$SelectString = $SelectString."<option value='".$mdb->dt[code]."'>".$mdb->dt[name]." </option>";
				}
			}
		}
		$SelectString = $SelectString."</Select>";
	}
	return $SelectString;
}
?>