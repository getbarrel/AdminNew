<?
	include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
	include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
	$db = new Database;

	$toStatus = $_GET[toStatus];
	$idx = $_GET[idx];

	//$SQL = "UPDATE tax_sales SET status = '$toStatus' WHERE idx = '$idx'";
	//$db->query($SQL);
	
	if($toStatus == "1")
	{
		echo "<script>alert ('발행이 정상적으로 이루어졌습니다.');parent.location.reload();</script>";
	}
	if($toStatus == "3")
	{
		include_once("popbill/common.php");
		
		$sql = "select * from tax_sales where idx = '$idx' ";
		$db->query($sql);
		$db->fetch();
		
		if($db->dt[publish_type] == '1'){//매출일때
			//공급자 기준이니 공급자 사업자 번호 호출
			
			$company_number = $db->dt[r_company_number];
			$KeyType = ENumMgtKeyType::SELL;
		}else if($db->dt[publish_type] == '2'){//매입일때
			// 공급 받는자 기준이니 공급받는자 사업자 번호 호출
			
			$company_number = $db->dt[r_company_number];
			$KeyType = ENumMgtKeyType::BUY;
		}
		try {
			$result = $TaxinvoiceService->CancelIssue($company_number,$KeyType,$idx,$idx .'건 발행 취소','daisomall');
			
			$sql = "update tax_sales set status = '3' where idx = '$idx'";
			$db->query($sql);
			
			echo "<script>alert ('발행취소되었습니다.');parent.location.reload();</script>";
		}catch(PopbillException $pe) {
			echo "<script>alert ('".$pe->getMessage()."');parent.location.reload();</script>";
			//echo '['.$pe->getCode().'] '.$pe->getMessage();
		}
		
	}
	if($toStatus == "5")
	{
		echo "<script>alert ('승인거부 처리되었습니다.');parent.location.reload();</script>";
	}
	if($toStatus == "6")
	{
		echo "<script>alert ('승인취소 처리되었습니다.');parent.location.reload();</script>";
	}
?>