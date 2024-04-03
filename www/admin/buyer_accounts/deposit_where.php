<?
$where = " where history_ix !='' ";
//처리상태 
if(is_array($history_type) && count($history_type)>0){		//노출여부 
	$where.=" AND history_type IN ('".implode("','",$history_type)."')";
}else{
	if($history_type != ""){
		$where .= " and history_type = '".$history_type."'";
	}else{
		$history_type =array();
	}
}

$search_text = trim($search_text);
if($db->dbms_type == "oracle"){
	if($search_type != "" && $search_text != ""){
		if($search_type == "cmd.name"){
			$mem_where .= " and AES_DECRYPT($search_type,'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
		}else{
			$mem_where .= " and $search_type LIKE  '%$search_text%' ";
		}
	}
}else{
	if($search_type != "" && $search_text != ""){
		if($search_type == "cmd.name"){
			$mem_where .= " and AES_DECRYPT(UNHEX($search_type),'".$db->ase_encrypt_key."') LIKE  '%$search_text%' ";
		}else if($search_type == "cu.id"){

			if(strpos($search_text,",") !== false){
				$search_array = explode(",",$search_text);
				$mem_where .= "and ( ";
				for($i=0;$i<count($search_array);$i++){

					if($i == count($search_array) - 1){
						$mem_where .= $search_type." = '".trim($search_array[$i])."'";
					}else{
						$mem_where .= $search_type." = '".trim($search_array[$i])."' or ";
					}
				}
				$mem_where .= ")";
			}else if(strpos($search_text,"\n") !== false){//\n

				$search_array = explode("\n",$search_text);

				$mem_where .= "and ( ";
				for($i=0;$i<count($search_array);$i++){

					if($i == count($search_array) - 1){
						$mem_where .= $search_type." = '".trim($search_array[$i])."'";
					}else{
						$mem_where .= $search_type." = '".trim($search_array[$i])."' or ";
					}
				}
				$mem_where .= ")";
			}else{
				$mem_where .= "and ".$search_type." = '".trim($search_text)."'";
			}
		}else{
			$mem_where .= " and $search_type LIKE  '%$search_text%' ";
		}

		
		$sql = "select cu.code from ".TBL_COMMON_USER." cu left join ".TBL_COMMON_MEMBER_DETAIL." cmd on cu.code = cmd.code where 1 $mem_where ";
		$db->query($sql);
		$code_array = $db->fetchall(0);
		if(is_array($code_array)){
			foreach($code_array as $val){
				$code_where[] = $val[code];
			}
			$code_where = implode("','",$code_where);
			$where .= " and uid in ('".$code_where."')";
		}
		
	}
}


if($search_check == "1" && $sdate != "" && $edate != ""){
	$where .= " and ".$search_history_type." between  '".$sdate." 00:00:00' and '".$edate." 23:59:59' ";
}
?>