<?php

/**
 * 2014-02-21
 * khy
 * 전자계약관리 function.lip
*/
 
$today = date("Y-m-d");
$vyesterday = date("Y-m-d", strtotime('-1 day'));
$v1weekago = date("Y-m-d", strtotime('-1 week'));
$v15dayago = date("Y-m-d", strtotime('-15 day'));
$v1monthago = date("Y-m-d", strtotime('-1 month'));
$v2monthago = date("Y-m-d", strtotime('-2 month'));
$v3monthago = date("Y-m-d", strtotime('-3 month'));
 
/**
     * 계약서 분류 
     * @param string $selected 분류코드
     * @return string
    */	

function getContractGroup($selected="" , $property=""){
	global $agent_type;
	$mdb = new Database;
	
	$sql = 	"SELECT * FROM econtract_group
				where disp=1  ";
	
	$mdb->query($sql);
	
	$mstring = "<select name='contract_group' id='contract_group' ".$property.">";
	$mstring .= "<option value=''>1차분류</option>";
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
}
 

function getContract($contract_group="", $et_ix="" , $property=""){
	global $agent_type;
	$mdb = new Database;
	
	$sql = 	"SELECT * FROM econtract_tmp
				where is_use =1 and contract_group = '".$contract_group."'  ";
	
	$mdb->query($sql);
	
	$mstring = "<select name='et_ix' id='et_ix' ".$property.">";
	$mstring .= "<option value=''>계약서 선택</option>";
	if($mdb->total && $contract_group){
		for($i=0;$i < $mdb->total;$i++){
			$mdb->fetch($i);
			if($mdb->dt[et_ix] == $et_ix){
				$mstring .= "<option value='".$mdb->dt[et_ix]."' selected>".$mdb->dt[contract_title]."</option>";
			}else{
				$mstring .= "<option value='".$mdb->dt[et_ix]."'>".$mdb->dt[contract_title]."</option>";
			}
		}
		
	}	
	$mstring .= "</select>";
	
	return $mstring;
}
 
 
function set_econtract_status($mdb, $vars){


	$mdb->sequences = "ECONTRACT_INFO_STATUS_SEQ";
	$ei_ix = $vars["ei_ix"];
	$status = $vars["status"];
	$status_message = $vars["status_message"];
	$admin_message = $vars["admin_message"];
	$company_id = $vars["company_id"];
	$charger_ix = $vars["charger_ix"];

	$sql = "insert into econtract_info_status
				(eis_ix,ei_ix,status,status_message,admin_message,company_id,charger_ix,regdate) 
				values
				('','$ei_ix','$status','$status_message','$admin_message','$company_id','$charger_ix',NOW())";
	
	//echo nl2br($sql);
	$mdb->query($sql);
}

//$company_id : 본사  $contractor_id : 입점업체
function regContract($db,$company_id, $contractor_id, $et_ix){

	$db->query("Select * from econtract_tmp where et_ix ='".$et_ix."'");
	$db->fetch();

	$contract_type = $db->dt[contract_type];
	$contract_title = $db->dt[contract_title];
	$contract_group = $db->dt[contract_group];
	$contract_detail = $db->dt[contract_detail]; // 치환코드 치환필요
	
	$contract_detail = generateContract($db, $company_id, $contractor_id, $contract_detail);


	$db->query("Select date_format(authorized_date,'%Y-%m-%d') as authorized_date from common_seller_detail where company_id ='".$contractor_id."'");
	$db->fetch();
	$authorized_date = $db->dt[authorized_date];

	$contract_date = date("Y-m-d");
	$contract_sdate = $authorized_date; //date("Y-m-d"); // 계약일자 확인필요 
	$contract_edate = date("Y-m-d", strtotime("+1 year", strtotime($authorized_date))); // 계약 승인된 날짜부터 -- 셀러 승인날짜
	$priod_type = $db->dt[priod_type];
	$extension_year = $db->dt[extension_year];
	$use_relation_file = $db->dt[use_relation_file];
	$sign_type = $db->dt[sign_type];
	$charger_ix = $_SESSION["admininfo"]["charger_ix"];
	$use_com_reg_no = 1; // 무조건 사업자 등록번호를 사용해야 할듯.

	$db->query("Select * from common_company_detail where company_id ='".$company_id."'");
	$db->fetch();
	$company_id = $db->dt[company_id];
	$com_name = $db->dt[com_name];
	$com_ceo = $db->dt[com_ceo];
	$com_zip = $db->dt[com_zip];
	$com_addr1 = $db->dt[com_addr1];
	$com_addr2 = $db->dt[com_addr2];
	$com_reg_no = $db->dt[com_number];

	$db->query("Select * from common_company_detail where company_id ='".$contractor_id."'");
	$db->fetch();
	$contractor_id = $db->dt[company_id];
	$contractor_name = $db->dt[com_name];
	$contractor_ceo = $db->dt[com_ceo];
	$contractor_zip = $db->dt[com_zip];
	$contractor_addr1 = $db->dt[com_addr1];
	$contractor_addr2 = $db->dt[com_addr2];
	$contractor_reg_no = $db->dt[com_number];
/*	
	$contract_detail = str_replace("{상호명}", $com_name, $contract_detail);
	$contract_detail = str_replace("{대표자명}", $com_ceo, $contract_detail);
	$contract_detail = str_replace("{회사주소}", "[".$com_zip."] ".$com_addr1." ".$com_addr2, $contract_detail);
	

	$contract_detail = str_replace("{협력사 상호명}", $contractor_name, $contract_detail);
	$contract_detail = str_replace("{협력사 대표자명}", $contractor_ceo, $contract_detail);
	$contract_detail = str_replace("{협력사 회사주소}", "[".$contractor_zip."] ".$contractor_addr1." ".$contractor_addr2, $contract_detail);
	
	

	$db->query("Select * from common_seller_delivery where company_id ='".$contractor_id."'");
	$db->fetch();
	$ac_term_div = $db->dt[ac_term_div];
	$ac_term_date1 = $db->dt[ac_term_date1];
	$ac_term_date2 = $db->dt[ac_term_date2];
	$econtract_commission = $db->dt[econtract_commission];

	if($ac_term_div == 1){
		$ac_term_div_str = "월 1 회";
		$ac_term_date_str = "익월 ".$ac_term_date1." 일";
	}else if($ac_term_div == 2){
		$ac_term_div_str = "월 2 회";
		$ac_term_date_str = "익월 ".$ac_term_date1.",".$ac_term_date2." 일";
	}else if($ac_term_div == 3){
		$ac_term_div_str = "매주 1 회";
		$ac_term_date_str = "매주 ".$standard_week_name[$ac_term_date1]."요일";
	}

	$contract_detail = str_replace("{협력사 승인일자}", $authorized_date, $contract_detail); 
	$contract_detail = str_replace("{수수료율}", $econtract_commission, $contract_detail);
	$contract_detail = str_replace("{대금 지급횟수}", $ac_term_div_str, $contract_detail);
	$contract_detail = str_replace("{대금 지급일자}", $ac_term_date_str, $contract_detail);
*/
	$sql = "insert into econtract_info
				(ei_ix,company_id,com_ceo,com_zip,com_addr1,com_addr2,com_reg_no,use_com_reg_no,contractor_id,contractor_ceo,contractor_zip,contractor_addr1,contractor_addr2,contractor_reg_no,use_contractor_reg_no,et_ix,contract_type,contract_title,contract_group,contract_date,contract_sdate,contract_edate,contract_detail,priod_type,extension_year,use_relation_file,sign_type,charger_ix,editdate,regdate) 
				values
				('','$company_id','$com_ceo','$com_zip','$com_addr1','$com_addr2','$com_reg_no','$use_com_reg_no','$contractor_id','$contractor_ceo','$contractor_zip','$contractor_addr1','$contractor_addr2','$contractor_reg_no','$use_contractor_reg_no','$et_ix','$contract_type','$contract_title','$contract_group','$contract_date','$contract_sdate','$contract_edate','$contract_detail','$priod_type','$extension_year','$use_relation_file','$sign_type','$charger_ix',NOW(),NOW()) ";

	//echo nl2br($sql);
	$db->query($sql);
	$db->query("SELECT ei_ix FROM econtract_info WHERE ei_ix=LAST_INSERT_ID()");
	$db->fetch();
	$ei_ix = $db->dt[ei_ix];

	// 첨부 파일정보 생성
	if($use_relation_file == 1){
		$db->query("Select * from econtract_file_tmp where et_ix ='".$et_ix."'");
		$relation_files = $db->fetchall();

		foreach($relation_files as $key => $file_text){
			$sql = "REPLACE INTO econtract_info_file set 
					ei_ix = '".$ei_ix."',
					file_text ='".$file_text."',
					regdate = NOW()  ";
			//echo nl2br($sql);
				$db->query($sql);
		}
	}else{
		$sql = "delete from econtract_info_file where ei_ix = '".$ei_ix."' ";
		$db->query($sql);
	}		
}

function generateContract($mdb, $company_id, $contractor_id, $contract_detail){
	$mdb->query("Select date_format(authorized_date,'%Y-%m-%d') as authorized_date from common_seller_detail where company_id ='".$contractor_id."'");
	$mdb->fetch();
	$authorized_date = $mdb->dt[authorized_date];

	$contract_date = date("Y-m-d");
	$contract_sdate = $authorized_date; //date("Y-m-d"); // 계약일자 확인필요 
	$contract_edate = date("Y-m-d", strtotime("+1 year", strtotime($authorized_date))); // 계약 승인된 날짜부터 -- 셀러 승인날짜
	/*
	$priod_type = $mdb->dt[priod_type];
	$extension_year = $mdb->dt[extension_year];
	$use_relation_file = $mdb->dt[use_relation_file];
	$sign_type = $mdb->dt[sign_type];
	$charger_ix = $_SESSION["admininfo"]["charger_ix"];
	$use_com_reg_no = 1; // 무조건 사업자 등록번호를 사용해야 할듯.
	*/

	$mdb->query("Select * from common_company_detail where company_id ='".$company_id."'");
	$mdb->fetch();
	$company_id = $mdb->dt[company_id];
	$com_name = $mdb->dt[com_name];
	$com_ceo = $mdb->dt[com_ceo];
	$com_zip = $mdb->dt[com_zip];
	$com_addr1 = $mdb->dt[com_addr1];
	$com_addr2 = $mdb->dt[com_addr2];
	$com_reg_no = $mdb->dt[com_number];

	$mdb->query("Select * from common_company_detail where company_id ='".$contractor_id."'");
	$mdb->fetch();
	$contractor_id = $mdb->dt[company_id];
	$contractor_name = $mdb->dt[com_name];
	$contractor_ceo = $mdb->dt[com_ceo];
	$contractor_zip = $mdb->dt[com_zip];
	$contractor_addr1 = $mdb->dt[com_addr1];
	$contractor_addr2 = $mdb->dt[com_addr2];
	$contractor_reg_no = $mdb->dt[com_number];

	if(!$is_multiple){
		$contract_detail = str_replace("{상호명}", $com_name, $contract_detail);
		$contract_detail = str_replace("{대표자명}", $com_ceo, $contract_detail);
		$contract_detail = str_replace("{회사주소}", "[".$com_zip."] ".$com_addr1." ".$com_addr2, $contract_detail);
		

		$contract_detail = str_replace("{협력사 상호명}", $contractor_name, $contract_detail);
		$contract_detail = str_replace("{협력사 대표자명}", $contractor_ceo, $contract_detail);
		$contract_detail = str_replace("{협력사 회사주소}", "[".$contractor_zip."] ".$contractor_addr1." ".$contractor_addr2, $contract_detail);
		
		

		$mdb->query("Select * from common_seller_delivery where company_id ='".$contractor_id."'");
		$mdb->fetch();
		$ac_term_div = $mdb->dt[ac_term_div];
		$ac_term_date1 = $mdb->dt[ac_term_date1];
		$ac_term_date2 = $mdb->dt[ac_term_date2];
		$econtract_commission = $mdb->dt[econtract_commission];

		if($ac_term_div == 1){
			$ac_term_div_str = "월 1 회";
			$ac_term_date_str = "익월 ".$ac_term_date1." 일";
		}else if($ac_term_div == 2){
			$ac_term_div_str = "월 2 회";
			$ac_term_date_str = "익월 ".$ac_term_date1.",".$ac_term_date2." 일";
		}else if($ac_term_div == 3){
			$ac_term_div_str = "매주 1 회";
			$ac_term_date_str = "매주 ".$standard_week_name[$ac_term_date1]."요일";
		}

		$contract_detail = str_replace("{협력사 승인일자}", $authorized_date, $contract_detail); 
		$contract_detail = str_replace("{수수료율}", $econtract_commission, $contract_detail);
		$contract_detail = str_replace("{대금 지급횟수}", $ac_term_div_str, $contract_detail);
		$contract_detail = str_replace("{대금 지급일자}", $ac_term_date_str, $contract_detail);
		
	}//

	return $contract_detail;
}
?>