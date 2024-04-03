<?php
include($_SERVER["DOCUMENT_ROOT"]."/admin/tax/popbill/common.php");
if($act=='popbillurl'){
	$popbillurl = $TaxinvoiceService->GetPopbillURL($com_number,$charger_id,'CERT');
	if($popbillurl){
		header("Location: ".$popbillurl);
	}else{
		echo("<script>alert('POPBILL 에 등록되지 않은 사용자입니다.');window.close();</script>");
	}
}

if($act == 'print_url'){
	$com_number = str_replace('-','',$com_number);
	//echo $com_number;
	//echo 1;
	if($ix != 0){
		$print_url = $TaxinvoiceService->GetPrintURL($com_number,ENumMgtKeyType::SELL,$ix,$userid);
		
		if($print_url){
			header("Location: ".$print_url);
		}else{
			echo("<script>alert('세금계산서 정보가 없습니다.');window.close();</script>");
		}
	}else{
		echo("<script>alert('발급된 세금계산서 정보가 없습니다.');window.close();</script>");
	}
}
