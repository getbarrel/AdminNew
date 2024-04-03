<?php
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/admin/tax/popbill/common.php");


$db = new Database;

print_R($_POST);


if($act == 'update'){
	
	$complete = 0;
	$fail = 0;
	
	for($i=0; $i < count($chk); $i++){
		$sql = "select * from tax_sales where idx = '".$chk[$i]."' ";
		$db->query($sql);
		$db->fetch();
		
		$company_number = str_replace('-','',$db->dt[s_company_number]);
		if($db->dt[publish_type] == '1'){
			$type = ENumMgtKeyType::SELL;
		}else if($db->dt[publish_type] == '2'){
			$type = ENumMgtKeyType::BUY;
		}else if($db->dt[publish_type] == '3'){
			$type = ENumMgtKeyType::TRUSTEE;
		}
		
		$result = $TaxinvoiceService->GetInfo($company_number,$type,$chk[$i]);
		if(!$result->code){
		
			switch($result->stateCode){
				case '100': 
					$status_text = '임시저장';
					$data_value = $result->stateCode;
					break;
				case '200': 
					$status_text = '승인대기';
					$data_value = $result->stateCode;
					break;
				case '210': 
					$status_text = '발행대기';
					$data_value = $result->stateCode;
					break;
				case '300':
				case '310':
					$status_text = '발행완료';
					$data_value = $result->stateCode;
					break;
				case '301':
				case '302':
				case '303':
				case '311':
				case '312':
				case '313':
					$status_text = '국세청 전송중';
					$data_value = $result->stateCode;
					break;
				case '304':
				case '314':
					$status_text = '국세청 전송완료';
					$data_value = $result->stateCode;
					break;
				case '305':
				case '315':
					$status_text = '국세청 전송실패';
					$data_value = $result->stateCode;
					break;
				case '400':
					$status_text = '거부';
					$data_value = $result->stateCode;
					break;
				case '500':
				case '510':
					$status_text = '취소';
					$data_value = $result->stateCode;
					break;
				case '600':
					$status_text = '발행취소';
					$data_value = $result->stateCode;
					break;
				default :
					$status_text = $result->stateCode;
					$data_value = $result->stateCode;
					break;
			}
		
		
			if($result->openYN == '1'){
				$sql = "update tax_sales set tax_mail_open ='Y' where idx = '".$chk[$i]."'";
				$db->query($sql);
			}
			if($result->stateCode){
				$sql = "update tax_sales set tax_status ='".$data_value."' , tax_status_text = '".$status_text."',tex_status_update_date = NOW()  where idx = '".$chk[$i]."'";
				$db->query($sql);
			}
			
			$complete ++;
			
		}else{
			$fail_code .=  $chk[$i].", ";
			$fail ++;
		}
	//	echo $sql;
	}
	/*메일보낸 횟수 저장*/
	
	
//	exit;
	
	echo "<script>alert ('업데이트완료. 성공 : ".$complete." 실패 : ".$fail." 실패문서코드 : ".$fail_code."'); parent.location.reload();</script>";
	
}

if($act == 'update_all'){
	
	$sql = "SELECT * FROM tax_sales where status = '1'";
	$db->query($sql);
	$tax_array = $db->fetchall();
	$complete = 0;
	$fail = 0;
	
	for($i=0; $i < count($tax_array); $i++){
	
		$company_number = str_replace('-','',$tax_array[$i][s_company_number]);
		if($tax_array[$i][publish_type] == '1'){
			$type = ENumMgtKeyType::SELL;
		}else if($tax_array[$i][publish_type] == '2'){
			$type = ENumMgtKeyType::BUY;
		}else if($tax_array[$i][publish_type] == '3'){
			$type = ENumMgtKeyType::TRUSTEE;
		}
		$idx = $tax_array[$i][idx];
		
		$result = $TaxinvoiceService->GetInfo($company_number,$type,$idx);
		//print_r($result);
		if(!$result->code){
		
			switch($result->stateCode){
				case '100': 
					$status_text = '임시저장';
					$data_value = $result->stateCode;
					break;
				case '200': 
					$status_text = '승인대기';
					$data_value = $result->stateCode;
					break;
				case '210': 
					$status_text = '발행대기';
					$data_value = $result->stateCode;
					break;
				case '300':
				case '310':
					$status_text = '발행완료';
					$data_value = $result->stateCode;
					break;
				case '301':
				case '302':
				case '303':
				case '311':
				case '312':
				case '313':
					$status_text = '국세청 전송중';
					$data_value = $result->stateCode;
					break;
				case '304':
				case '314':
					$status_text = '국세청 전송완료';
					$data_value = $result->stateCode;
					break;
				case '305':
				case '315':
					$status_text = '국세청 전송실패';
					$data_value = $result->stateCode;
					break;
				case '400':
					$status_text = '거부';
					$data_value = $result->stateCode;
					break;
				case '500':
				case '510':
					$status_text = '취소';
					$data_value = $result->stateCode;
					break;
				case '600':
					$status_text = '발행취소';
					$data_value = $result->stateCode;
					break;
				default :
					$status_text = $result->stateCode;
					$data_value = $result->stateCode;
					break;
			}
		
		
			if($result->openYN == '1'){
				$sql = "update tax_sales set tax_mail_open ='Y' where idx = '".$idx."'";
				$db->query($sql);
			}
			if($result->stateCode){
				$sql = "update tax_sales set tax_status ='".$data_value."' , tax_status_text = '".$status_text."',tex_status_update_date = NOW()  where idx = '".$idx."'";
				$db->query($sql);
			}
			
			$complete ++;
		}else{
			
			$sql = "update tax_sales set  tax_status_text = '업데이트실패' ,tex_status_update_date = NOW()  where idx = '".$idx."'";
			$db->query($sql);
				
			$fail_code .=  $idx.", ";
			$fail ++;
			
			
		}
	//	echo $sql;
	}
	
	
	echo "<script>alert ('업데이트완료. 성공 : ".$complete." 실패 : ".$fail." 실패문서코드 : ".$fail_code."'); parent.location.reload();</script>";
}