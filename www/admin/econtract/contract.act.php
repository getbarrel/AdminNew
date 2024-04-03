<?
include("../class/layout.class"); 
include("./contract.lib.php");

$db = new Database();
if($act == "update"){
	
	if($contract_type == "1"){
		$sql = "update econtract_tmp set  contract_type = '' ";
		$db->query($sql);
	}

	$sql = "update econtract_tmp set 
				contract_type='$contract_type',
				contract_title='$contract_title',
				contract_group='$contract_group',
				contract_detail='$contract_detail',
				priod_type='$priod_type',
				extension_year='$extension_year',
				use_relation_file='$use_relation_file',
				charger_ix='$charger_ix',
				is_use='$is_use',
				editdate=NOW()
				where et_ix='".$et_ix."'  ";
	//echo nl2br($sql);
	//exit;
	$db->query($sql);

	if($use_relation_file == 1){
		$sql = "delete from econtract_file_tmp where et_ix = '".$et_ix."' ";
		$db->query($sql);

		foreach($relation_files as $key => $file_text){
			$sql = "REPLACE INTO econtract_file_tmp set 
					et_ix = '".$et_ix."',
					file_text ='".$file_text."',
					regdate = NOW()  ";
			//echo nl2br($sql);
				$db->query($sql);
		}
	}else{
		$sql = "delete from econtract_file_tmp where et_ix = '".$et_ix."' ";
		$db->query($sql);
	}
	echo "<script language='javascript' src='../_language/language.php'></script><script language='javascript'>alert('계약서 정보가 정상적으로 수정되었습니다.');parent.document.location.reload();</script>";
}

if($act == "delete"){

	$sql = "delete from econtract_tmp where et_ix = '".$et_ix."'  ";
	$db->query($sql);

	$sql = "delete from econtract_file_tmp where et_ix = '".$et_ix."' ";
	$db->query($sql);

	echo "<script language='javascript' src='../_language/language.php'></script><script language='javascript'>alert('계약서 정보가 정상적으로 삭제되었습니다.');parent.document.location.reload();</script>";
}

if($act == "insert"){
	
	
	$sql = "insert into econtract_tmp
	(et_ix,contract_type,contract_title,contract_group,contract_detail,priod_type,extension_year,use_relation_file,charger_ix,is_use,editdate,regdate) 
	values
	('','".$contract_type."','".$contract_title."','".$contract_group."','".$contract_detail."','".$priod_type."','".$extension_year."','".$use_relation_file."','".$charger_ix."','".$is_use."',NOW(),NOW())";
	
	//echo nl2br($sql);
	//exit;
	$db->query($sql);
	$db->query("SELECT et_ix FROM econtract_tmp WHERE et_ix=LAST_INSERT_ID()");
	$db->fetch();
	$et_ix = $db->dt[et_ix];

	if($use_relation_file == 1){
		$sql = "delete from econtract_file_tmp where et_ix = '".$et_ix."' ";
		$db->query($sql);

		foreach($relation_files as $key => $file_text){
			$sql = "REPLACE INTO econtract_file_tmp set 
					et_ix = '".$et_ix."',
					file_text ='".$file_text."',
					regdate = NOW()  ";
			//echo nl2br($sql);
				$db->query($sql);
		} 
	}

	echo "<script language='javascript' src='../_language/language.php'></script><script language='javascript'>alert('계약서 정보가 정상적으로 등록되었습니다.');parent.document.location.href='contract_list.php';</script>";
}

if($act == "getContractList"){

	$sql = "select et_ix, contract_title from econtract_tmp where is_use = 1 and contract_group = '".$contract_group."' ";

	$db->query($sql);
	$contracts = $db->fetchall();

	echo json_encode($contracts);

}

if($act == "getContract"){

	$sql = "select * from econtract_tmp where is_use = 1 and et_ix = '".$et_ix."' ";

	$db->query($sql);
	$contracts = $db->fetchall();
	$contract_info = $contracts[0];
	$contract_detail = $contract_info[contract_detail];
	if(!$is_multiple){
		$contract_info[contract_detail] = generateContract($db, $company_id, $contractor_id, $contract_detail);
	}

	$sql = "select *  from econtract_file_tmp where et_ix = '".$et_ix."' ";
	$db->query($sql);
	$relastion_files = $db->fetchall();
	if(count($relastion_files)){
		$contract_info[relation_files] = $relastion_files;
	}
	
	echo json_encode($contract_info);

}

if($act == "regContract"){

//print_r($_POST);
$com_zip = $com_zip1."-".$com_zip2;
$com_reg_no = $com_reg_no1."-".$com_reg_no2."-".$com_reg_no3;
$contractor_zip = $contractor_zip1."-".$contractor_zip2;
$contractor_reg_no = $contractor_reg_no1."-".$contractor_reg_no2."-".$contractor_reg_no3;

if($is_multiple){
	//print_r($_POST);
	//exit;
		if(is_array($seller)){
			$contract_detail_tmp = $contract_detail;
			foreach($seller as $key => $contractor_id){
				//echo $contractor_id;
				//exit;
				$db->query("Select * from common_company_detail where company_id ='".$contractor_id."'");
				$db->fetch();
				//$contractor_id = $db->dt[company_id];
				$contractor_ceo = $db->dt[com_ceo];
				$contractor_zip = $db->dt[com_zip];
				$contractor_addr1 = $db->dt[com_addr1];
				$contractor_addr2 = $db->dt[com_addr2];
				$contractor_reg_no = $db->dt[com_number];

				$db->query("Select date_format(authorized_date,'%Y-%m-%d') as authorized_date from common_seller_detail where company_id ='".$contractor_id."'");
				$db->fetch();
				$authorized_date = $db->dt[authorized_date];

				$contract_date = date("Y-m-d");
				$contract_sdate = $authorized_date; //date("Y-m-d"); // 계약일자 확인필요 
				$contract_edate = date("Y-m-d", strtotime("+1 year", strtotime($authorized_date))); // 계약 승인된 날짜부터 -- 셀러 승인날짜

				$contract_detail = generateContract($db, $company_id, $contractor_id, $contract_detail_tmp);

				$sql = "insert into econtract_info
							(ei_ix,company_id,com_ceo,com_zip,com_addr1,com_addr2,com_reg_no,use_com_reg_no,contractor_id,contractor_ceo,contractor_zip,contractor_addr1,contractor_addr2,contractor_reg_no,use_contractor_reg_no,et_ix,contract_type,contract_title,contract_group,contract_date,contract_sdate,contract_edate,contract_detail,priod_type,extension_year,use_relation_file,sign_type,charger_ix,editdate,regdate) 
							values
							('','$company_id','$com_ceo','$com_zip','$com_addr1','$com_addr2','$com_reg_no','$use_com_reg_no','$contractor_id','$contractor_ceo','$contractor_zip','$contractor_addr1','$contractor_addr2','$contractor_reg_no','$use_contractor_reg_no','$et_ix','$contract_type','$contract_title','$contract_group','$contract_date','$contract_sdate','$contract_edate','$contract_detail','$priod_type','$extension_year','$use_relation_file','$sign_type','$charger_ix',NOW(),NOW()) ";

				//echo nl2br($sql);
				$db->query($sql);
				$db->query("SELECT ei_ix FROM econtract_info WHERE ei_ix=LAST_INSERT_ID()");
				$db->fetch();
				$ei_ix = $db->dt[ei_ix];


				if($use_relation_file == 1){
					$sql = "delete from econtract_info_file where ei_ix = '".$ei_ix."' ";
					$db->query($sql);

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
			//exit;
		}
		//exit;
}else{
		$contract_detail = generateContract($db, $company_id, $contractor_id, $contract_detail);

		$db->query("Select date_format(authorized_date,'%Y-%m-%d') as authorized_date from common_seller_detail where company_id ='".$contractor_id."'");
		$db->fetch();
		$authorized_date = $db->dt[authorized_date];

		$contract_date = date("Y-m-d");
		$contract_sdate = $authorized_date; //date("Y-m-d"); // 계약일자 확인필요 
		$contract_edate = date("Y-m-d", strtotime("+1 year", strtotime($authorized_date))); // 계약 승인된 날짜부터 -- 셀러 승인날짜


		$sql = "insert into econtract_info
					(ei_ix,company_id,com_ceo,com_zip,com_addr1,com_addr2,com_reg_no,use_com_reg_no,contractor_id,contractor_ceo,contractor_zip,contractor_addr1,contractor_addr2,contractor_reg_no,use_contractor_reg_no,et_ix,contract_type,contract_title,contract_group,contract_date,contract_sdate,contract_edate,contract_detail,priod_type,extension_year,use_relation_file, sign_type,charger_ix,editdate,regdate) 
					values
					('','$company_id','$com_ceo','$com_zip','$com_addr1','$com_addr2','$com_reg_no','$use_com_reg_no','$contractor_id','$contractor_ceo','$contractor_zip','$contractor_addr1','$contractor_addr2','$contractor_reg_no','$use_contractor_reg_no','$et_ix','$contract_type','$contract_title','$contract_group','$contract_date','$contract_sdate','$contract_edate','$contract_detail','$priod_type','$extension_year','$use_relation_file','$sign_type','$charger_ix',NOW(),NOW()) ";

		$db->query($sql);
		//echo nl2br($sql);
		//exit;

		$db->query("SELECT ei_ix FROM econtract_info WHERE ei_ix=LAST_INSERT_ID()");
		$db->fetch();
		$ei_ix = $db->dt[ei_ix];


		if($use_relation_file == 1){
			$sql = "delete from econtract_info_file where ei_ix = '".$ei_ix."' ";
			$db->query($sql);

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
		
		$vars["ei_ix"] = $ei_ix;
		$vars["status"] = "CA";
		$vars["status_message"] = "계약서 등록";
		$vars["admin_message"] = "";
		$vars["company_id"] = $company_id;
		$vars["charger_ix"] = $charger_ix;

		set_econtract_status($db, $vars);

}

echo "<script language='javascript' src='../_language/language.php'></script><script language='javascript'>alert('전자 계약서가 정상적으로 작성되었습니다.');parent.document.location.href='econtract_list.php';</script>";

}

if($act == "updateContract"){

$com_zip = $com_zip1."-".$com_zip2;
$com_reg_no = $com_reg_no1."-".$com_reg_no2."-".$com_reg_no3;
$contractor_zip = $contractor_zip1."-".$contractor_zip2;
$contractor_reg_no = $contractor_reg_no1."-".$contractor_reg_no2."-".$contractor_reg_no3;

$sql = "update econtract_info set			
			company_id='$company_id',
			com_ceo='$com_ceo',
			com_zip='$com_zip',
			com_addr1='$com_addr1',
			com_addr2='$com_addr2',
			com_reg_no='$com_reg_no',
			use_com_reg_no='$use_com_reg_no',
			contractor_id='$contractor_id',
			contractor_ceo='$contractor_ceo',
			contractor_zip='$contractor_zip',
			contractor_addr1='$contractor_addr1',
			contractor_addr2='$contractor_addr2',
			contractor_reg_no='$contractor_reg_no',
			use_contractor_reg_no='$use_contractor_reg_no',
			et_ix='$et_ix',
			contract_type='$contract_type',
			contract_title='$contract_title',
			contract_group='$contract_group',
			contract_date='$contract_date',
			contract_sdate='$contract_sdate',
			contract_edate='$contract_edate',
			contract_detail='$contract_detail',
			priod_type='$priod_type',
			extension_year='$extension_year',
			use_relation_file='$use_relation_file',
			sign_type='$sign_type',
			charger_ix='$charger_ix',
			editdate=NOW()
			where ei_ix='$ei_ix' ";

			$db->query($sql);

			if($use_relation_file == 1){
				$sql = "delete from econtract_info_file where ei_ix = '".$ei_ix."' ";
				$db->query($sql);

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

			$vars["ei_ix"] = $ei_ix;
			$vars["status"] = "CA";
			$vars["status_message"] = "계약서 수정";
			$vars["admin_message"] = "";
			$vars["company_id"] = $company_id;
			$vars["charger_ix"] = $charger_ix;

			set_econtract_status($db, $vars);
			
			echo "<script language='javascript' src='../_language/language.php'></script><script language='javascript'>alert('전자 계약서가 정상적으로 수정되었습니다.');parent.document.location.href='econtract_list.php';</script>";
}

if($act == "deleteContract"){

	$sql = "update econtract_info set status = 'CRM' where ei_ix = '".$ei_ix."' ";
	$db->query($sql);

	/*
	$sql = "delete from econtract_info where ei_ix = '".$ei_ix."' ";
	$db->query($sql);

	$sql = "delete from econtract_info_status where ei_ix = '".$ei_ix."' ";
	$db->query($sql);
	*/
	//$sql = "delete from econtract_info_file where ei_ix = '".$ei_ix."' ";
	//$db->query($sql);
	$vars["ei_ix"] = $ei_ix;
	$vars["status"] = "CRM";
	$vars["status_message"] = "계약서 삭제 ";
	$vars["admin_message"] = "계약서 삭제";
	$vars["company_id"] = $_SESSION["admininfo"]["company_id"];
	$vars["charger_ix"] = $_SESSION["admininfo"]["charger_ix"];


	set_econtract_status($db, $vars);

	echo "<script language='javascript' src='../_language/language.php'></script><script language='javascript'>alert('전자 계약서가 정상적으로 삭제되었습니다.');parent.document.location.reload();</script>";
}


if($act == "signInsert"){
	//echo $signed_document; exit;
	if($sign_type == "H"){
		$sql = "	update econtract_info set			
				orgin_document='$orgin_document',
				com_signature='".$signed_document."',
				signature_date=NOW()
				where ei_ix='$ei_ix' ";
		$db->query($sql);

		$result[bool] = true;
		$result[message] = "전자서명이 정상적으로 처리 되었습니다.";
		echo json_encode($result);
	}else{
		$sql = "	update econtract_info set	
				contractor_signature='".$signed_document."',
				contractor_signature_date=NOW()
				where ei_ix='$ei_ix' ";
		$db->query($sql);

		$result[bool] = true;
		$result[message] = "전자서명이 정상적으로 처리 되었습니다.";
		echo json_encode($result);
	}

	/*
	* 서명이 완료 됐으면 상태를 서명완료로 변경
	*/
	$sql = "select * from econtract_info ei where contractor_signature != '' and com_signature != ''  and ei_ix='".$ei_ix."' ";
	$db->query($sql);

	if($db->total){
		$sql = "	update econtract_info set	 status = 'CC' where ei_ix='".$ei_ix."' ";
		$db->query($sql);

		$vars["ei_ix"] = $ei_ix;
		$vars["status"] = "CC";
		$vars["status_message"] = "시스템 계약완료 ";
		$vars["admin_message"] = "계약완료";
		$vars["company_id"] = $_SESSION["admininfo"]["company_id"];
		$vars["charger_ix"] = $_SESSION["admininfo"]["charger_ix"];


		set_econtract_status($db, $vars);
	}
 }

/*
/admin/econtract/contract.act.php
sign_type=H 본사, C : 계약업체
orgin_document= (전자계약원문)
com_signature= (본사 전자서명내용)
ei_ix= (전자계약코드)

sign_type=C : 계약업체
contractor_signature= (업체 전자서명내용)
ei_ix= (전자계약코드)
*/

if($act == "changeContractStatus"){
	// print_r($_POST);
	 //exit;
	if(is_array($ei_ix)){
		foreach($ei_ix as $key => $value){
			$sql = "	update econtract_info set	 status = '".$change_status."' where ei_ix='".$value."' ";
			$db->query($sql);

			$vars["ei_ix"] = $value;
			$vars["status"] = $change_status;
			$vars["status_message"] = $cancel_message;
			$vars["admin_message"] = "계약서 상태변경";
			$vars["company_id"] = $_SESSION["admininfo"]["company_id"];
			$vars["charger_ix"] = $_SESSION["admininfo"]["charger_ix"];


			set_econtract_status($db, $vars);
		}
	}else{
		$sql = "	update econtract_info set	 status = '".$change_status."' where ei_ix='".$ei_ix."' ";
		$db->query($sql);

		$vars["ei_ix"] = $ei_ix;
		$vars["status"] = $change_status;
		$vars["status_message"] = $cancel_message;
		$vars["admin_message"] = "계약서 상태변경";
		$vars["company_id"] = $_SESSION["admininfo"]["company_id"];
		$vars["charger_ix"] = $_SESSION["admininfo"]["charger_ix"];


		set_econtract_status($db, $vars);
	}

		$result[bool] = true;
		if($change_status == "CRT"){
			$result[message] = "전자서명이 정상적으로 반려 처리 되었습니다.";
		}else if($change_status == "CRS"){
			$result[message] = "전자서명이 정상적으로 취소 처리 되었습니다.";
		}else{
			$result[message] = "전자서명이 정상적으로 처리 되었습니다.";
		}
		echo json_encode($result);
}