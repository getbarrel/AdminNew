<?
	#proc.publish.php

	include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
	include($_SERVER["DOCUMENT_ROOT"]."/class/database.class");
	$db = new Database;

	$chk = $_POST[chk];
	
	if(sizeof($chk) < 1) die;
$a = 0;
$b = 0;
$c = 0;
	for($i=0; $i < sizeof($chk); $i++)
	{
		$idx_no = $chk[$i];
		//print_r($idx_no);
		//exit;
		if($idx_no)
		{
			$db->query("SELECT * FROM tax_sales WHERE idx = '$idx_no'");
			$db->fetch();
			
			if($db->dt[signdate] >= date('Y-m-d')){
				$c ++; // 미래세금계산서일경우 발행 안되야 함
			}else{
				
				/*팝빌 연동*/
				include_once("popbill/common.php");
				
			
				//임시로 등록된 문서를 발행하는 프로세스
				
				$s_company_number = str_replace('-','',$db->dt[s_company_number]);
				$r_company_number = str_replace('-','',$db->dt[r_company_number]);
				//임시 테스트용 사업자 번호 
				//$s_company_number = "2148868761";
				//echo $s_company_number;
				try {
					//$result = $TaxinvoiceService->Issue('1231212312',ENumMgtKeyType::SELL,'123123','발행 메모',null,true,'userid');
					if($db->dt[publish_type] == '1'	){//정발행
						$result = $TaxinvoiceService->Issue($s_company_number,ENumMgtKeyType::SELL,$idx_no,$db->dt[s_company_name].' : 발행내역',null,true,null);
						if($act == "all"){ // 국세청 즉시 발행 일 경우 
							$result = $TaxinvoiceService->SendToNTS($s_company_number,ENumMgtKeyType::SELL,$idx_no,$_SESSION[admin_config][popbill_id]);
						}
					
					}else if($db->dt[publish_type] == '2'){//역발행
						$result = $TaxinvoiceService->Request($r_company_number,ENumMgtKeyType::BUY,$idx_no,$db->dt[s_company_name].' : 역)발행요청','daisomall');
						
						if($act == "all"){ // 국세청 즉시 발행 일 경우 
							$result = $TaxinvoiceService->SendToNTS($r_company_number,ENumMgtKeyType::BUY,$idx_no,$_SESSION[admin_config][popbill_id]);
						}
					}
					
					//$result = $TaxinvoiceService->Issue($s_company_number,ENumMgtKeyType::SELL,$idx,$_POST[s_company_name].' : 발행내역',null,true,null);
					
					// 발행이 완료 되면 DB의 발행 상태를 업데이트 한다.
					$SQL = "UPDATE tax_sales SET status = '1' WHERE idx = '$idx_no'";
					$db->query($SQL);
					$a ++;
					//echo '['.$result->code.'] '.$result->message;
				}
				catch(PopbillException $pe) {
					//echo "<script>alert ('".$pe->getMessage()."');</script>";
					$b ++;
					//echo '['.$pe->getCode().'] '.$pe->getMessage();
				}
				
			}
		}
	}
	if($act == "all"){
		echo "<script>alert ('일괄국세청 발행 처리되었습니다. 성공 : ".$a." 실패 : ".$b." 미래 : ".$c."');parent.location.reload();</script>";
	}else{
		echo "<script>alert ('일괄발행 처리되었습니다. 성공 : ".$a." 실패 : ".$b." 미래 : ".$c."');parent.location.reload();</script>";
	}
	
?>
<script>
alert ("발행되었습니다.");
parent.location.reload();
</script>