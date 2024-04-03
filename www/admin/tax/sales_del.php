<?
	# datafile_del.php
	
	include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
	include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
	$db = new Database;

	$chk = $_POST[chk];
	
	if(sizeof($chk) < 1) die;
$a = 0;
$b = 0;
	for($i=0; $i < sizeof($chk); $i++)
	{
		$idx_no = $chk[$i];

		if($idx_no)
		{
			
			$db->query("SELECT * FROM tax_sales WHERE idx = '$idx_no'");
			$db->fetch();
			
			/*팝빌 연동*/
			include_once("popbill/common.php");
			
			if($db->dt[publish_type] == '1'){
				$company_number = str_replace('-','',$db->dt[s_company_number]);
			}else if ($db->dt[publish_type] == '2'){
				$company_number = str_replace('-','',$db->dt[r_company_number]);
			}
			
			
			try {
				
				if($db->dt[publish_type] == '1'){
					$result = $TaxinvoiceService->Delete($company_number,ENumMgtKeyType::SELL,$idx_no);
				}else if ($db->dt[publish_type] == '2'){
					$result = $TaxinvoiceService->Delete($company_number,ENumMgtKeyType::BUY,$idx_no);
				}
				$SQL = "DELETE FROM tax_sales WHERE idx = '$idx_no'";
				$db->query($SQL);

				$SQL2 = "DELETE FROM tax_sales_detail WHERE p_idx = '$idx_no'";
				$db->query($SQL2);
			$a ++;	
				//echo '['.$result->code.'] '.$result->message;
			}
			catch(PopbillException $pe) {
				$b ++;
				//echo '['.$pe->getCode().'] '.$pe->getMessage();
			}
			
			
		}
		
	}
	
	echo "<script>alert ('일괄삭제 처리되었습니다. 성공 : ".$a." 실패 : ".$b."');parent.location.reload();</script>";
?>
<script>
alert ("삭제 되었습니다.");
parent.location.reload();
</script>