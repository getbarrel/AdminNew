<?php
include("../class/layout.class");
$db = new Database;

if($act == "send_mail"){
	
	$sql = "SELECT * FROM tax_sales where idx = $idx ";
	$db->query($sql);
	$db->fetch();
	
	$publish_type = $db->dt[publish_type];//발행타입정보 1:정발행, 2:역발행, 3:위수탁
	
	if($publish_type == '1'){
		$company_number = str_replace('-','',$db->dt[s_company_number]);//공급자 사업자 번호
		//$MgtKeyType = ENumMgtKeyType::SELL;
	}else if ($publish_type == '2'){
		$company_number = str_replace('-','',$db->dt[r_company_number]);//공급받는자 사업자 번호
		//$MgtKeyType = ENumMgtKeyType::BUY;
	}
	//echo $MgtKeyType;
	//exit;
	//echo $_SESSION[admin_config][popbill_id];
	//exit;
	include("popbill/common.php");
		if($publish_type == '1'){
			$result = $TaxinvoiceService->SendEmail($company_number,ENumMgtKeyType::SELL,$idx,$email,$_SESSION[admin_config][popbill_id]);
		}else if ($publish_type == '2'){
			$result = $TaxinvoiceService->SendEmail($company_number,ENumMgtKeyType::BUY,$idx,$email,$_SESSION[admin_config][popbill_id]);
		}
		if($result->code==1){
			echo "<script>alert('이메일 재전송 완료.".$email."');window.close();</script>";
			
			$sql = "update tax_sales set mail_re_send =mail_re_send+1 where idx = '".$idx."'";
			$db->query($sql);
			exit;
		}else{
			echo "<script>alert('".$result->message."');window.close();</script>";
			exit;
		}
}