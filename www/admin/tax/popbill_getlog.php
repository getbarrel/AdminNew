<?
//include($_SERVER["DOCUMENT_ROOT"]."/admin/webedit/webedit.lib.php");
include("../class/layout.class");
include("popbill/common.php");

$db = new MySQL;

$sql = "SELECT * FROM tax_sales where idx = $idx ";
$db->query($sql);
$db->fetch();

$publish_type = $db->dt[publish_type];//발행타입정보 1:정발행, 2:역발행, 3:위수탁

if($publish_type == '1'){
	$company_number = str_replace('-','',$db->dt[s_company_number]);//공급자 사업자 번호
	$result = $TaxinvoiceService->GetLogs($company_number,ENumMgtKeyType::SELL,$idx);
}else if ($publish_type == '2'){
	$company_number = str_replace('-','',$db->dt[r_company_number]);//공급받는자 사업자 번호
	$result = $TaxinvoiceService->GetLogs($company_number,ENumMgtKeyType::BUY,$idx);
}

//print_R($result);

$Script = "
<script language='JavaScript' >

</Script>";

$Contents = "
<TABLE cellSpacing=0 cellPadding=0 width='100%' align=center border=0>
	<TR>
		<td align=center colspan=2>
		<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center'>
			
			<tr>
				<td align=center style='padding: 0 5px 0 5px'>
					<table width=100% class='list_table_box'>
						<tr height='28' bgcolor='#ffffff'>
							<td width='22%' align='center' class='m_td'><font color='#000000'><b>회사명</b></font></td>
							<td width='*' align='center' class=m_td><font color='#000000'><b>구분</b></font></td>
							<td width='24%' align='center' class=m_td><font color='#000000'><b>담당자</b></font></td>
							<td width='20%' align='center' class=m_td><font color='#000000'><b>변경일</b></font></td>
						</tr>
					";
					//if($result->code=="1"){
						for($i=0;$i  < count($result); $i++){	
						$log = $result[$i]->log;
						$com_name = $result[$i]->procCorpName;
						$com_contac_name = $result[$i]->procContactName;
						$memo = $result[$i]->procMemo;
						$type = $result[$i]->docLogType;
						$date_time = $result[$i]->regDT;
						$date = date('Y-m-d',strtotime($date_time)); 
						/*switch($result[$i]->docLogType){
							case '301':
							case '302':
							case '303':
							case '311':
							case '312':
							case '313':
								$type_text = "국세청전송중";
								break;
							case '304':
							case '314':
								$type_text = "국세청전송완료";
								break;
							case '305':
							case '315':
								$type_text = "국세청전송실패";
								break;
							case '300':
							case '310':
								$type_text = "발행완료";
								break;
							case '500':
							case '510':
								$type_text = "취소";
								break;
							case '100':
								$type_text = "임시저장";
								break;
							case '200':
								$type_text = "승인대기";
								break;
							case '210':
								$type_text = "발행대기";
								break;
							case '400':
								$type_text = "거부";
								break;
							case '600':
								$type_text = "발행취소";
								break;
							default :
								$type_text = "기타";
								break;
						}*/
						$Contents .= "
							<tr height='35' bgcolor='#ffffff'>
								<td class='list_box_td list_bg_gray'>$com_name</td>
								<td class='list_box_td '>$log".($type == '111' ? "<br>$memo" : "")."</td>
								<td class='list_box_td list_bg_gray'>$com_contac_name</td>
								<td class='list_box_td '>$date</td>
							</tr>
						";
						}
					/*}else{
						$Contents .= "
							<tr height='35' bgcolor='#ffffff'>
								<td class='list_box_td' colspan='4'>내역이 존재하지 않습니다.</td>
							</tr>
						";
					}		*/	
					$Contents .= "
					</table>
				</td>
			</tr>
		</table>
		</td>
	</tr>
</TABLE>
";

$P = new ManagePopLayOut();
$P->addScript = $Script;
$P->Navigation = "문서 이력확인";
$P->NaviTitle = "문서 이력확인";
$P->strContents = $Contents;
echo $P->PrintLayOut();

//print_r($script_times);

?>





