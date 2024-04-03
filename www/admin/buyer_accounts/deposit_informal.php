<?
include("../class/layout.class");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
$page_title = "예치금 일별통계";
$page_navigation = "구매자정산관리 > 예치금관리 > 예치금 일별통계";



$db = new MySQL;
$mdb = new MySQL;

$sms_design = new SMS;

	//검색 1주일단위 디폴트
		if ($startDate == ""){
			$before7day = mktime(0, 0, 0, date("m")  , date("d")-7, date("Y"));

			$startDate = date("Y-m-d", $before7day);
			$endDate = date("Y-m-d");
		}

		if ($vstartDate == ""){
			$before14day = mktime(0, 0, 0, date("m")  , date("d")-14, date("Y"));
			$before7day = mktime(0, 0, 0, date("m")  , date("d")-7, date("Y"));
			$vstartDate = date("Y-m-d", $before14day);
			$vendDate = date("Y-m-d",$before7day);
		}

		if($mode != 'search'){
			$send = 1;
		}

$Contents .="
			<table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='margin-top:10px;'>
			<tr>
				<td align='left' colspan=6 >".GetTitleNavigation("구매자정산관리", "예치금관리 > 예치금 일별통계 ")."</td>
			</tr>
			<tr>
				<td>
				<form name='searchmember' method='GET'>
				<input type='hidden' name='mode' value='search' />
				<table border='0' cellpadding='0' cellspacing='0' width='100%'>
					<tr>
					<td style='width:100%;' valign=top colspan=3>
						<table width=100%  border=0 cellpadding='0' cellspacing='0'>
							<tr>
								<td align='left' colspan=2 width='100%' valign=top style='padding-top:5px;'>

										<TABLE cellSpacing=0 cellPadding=10 style='width:100%;' align=center border=0>
										<TR>
											<TD bgColor=#ffffff style='padding:0 0 0 0;'>
											<table cellpadding=0 cellspacing=0 width='100%' class='search_table_box'>
												 <tr height=27>
													<td class='search_box_title'>조건설정</td>
													<td class='search_box_item' colspan='3'>
														<input type=radio name='send' value='1' id='schdays'  ".CompareReturnValue("1",$send,"checked")."><label for='schdays'>일별</label>
														<input type=radio name='send' value='2' id='schmonth' ".CompareReturnValue("2",$send,"checked")."><label for='schmonth'>월별</label>
														<!--<input type=radio name='send' value='3' id='schperioad' ".CompareReturnValue("3",$send,"checked")."><label for='schperioad'>기간별</label>-->
													</td>
												</tr>
												 <tr height='27'>
													<th class='search_box_title' bgcolor='#efefef' width='150' align='center'>";
														
														if($send == "4"){
															$Contents .="기준일자";
														}else{
															$Contents .="기간";
														}
													$Contents .="</th>
													<td class='search_box_item'>
														".search_date('startDate','endDate',$startDate,$endDate)."
													</td>
												 </tr>";
												$Contents .= "
											</table>
											</TD>
										</TR>
										</TABLE>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr >
					<td colspan=3 align=center style='padding:10px 0 20px 0'>
						<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
					</td>
				</tr>
				</table>
				</form>
				</td>
			</tr>
			</table>";
	
	
	if($send == "1"){
		
		$between_date = " and date_format(d.edit_date,'%Y-%m-%d') between '".$startDate."' and '".$endDate."'";
		$group_by = " group by date_format(d.edit_date,'%Y-%m-%d')";

	}else if($send == "2"){

		if($mode == 'excel'){
			$between_date = " and date_format(d.edit_date,'%Y-%m') between '".substr($startDate,0,7)."-01' and '".substr($endDate,0,7)."-31'";
			$group_by = " group by date_format(d.edit_date,'%Y-%m-%d')";
		}else{
			$between_date = " and date_format(d.edit_date,'%Y-%m') between '".substr($startDate,0,7)."' and '".substr($endDate,0,7)."'";
			$group_by = " group by date_format(d.edit_date,'%Y-%m')";
		}

	}else if($send == "3"){

		$between_date = " and date_format(d.edit_date,'%Y-%m-%d') between '".$startDate."' and '".$endDate."'";
		$group_by = " group by date_format(d.edit_date,'%Y-%m-%d')";
	}

	$sql = "select
				sum(if(d.state='1',d.deposit,0)) as wait_deposit,
				sum(if(d.state='2',d.deposit,0)) as cancel_deposit,
				sum(if(d.state='3',d.deposit,0)) as complete_deposit,
				sum(if(d.state='4',d.deposit,0)) as use_deposit,
				sum(if(d.state='5',d.deposit,0)) as request_deposit,
				sum(if(d.state='6',d.deposit,0)) as request_cancel_deposit,
				sum(if(d.state='7',d.deposit,0)) as confirm_deposit,
				sum(if(d.state='8',d.deposit,0)) as withdrawl_deposit,
				date_format(d.edit_date,'%Y-%m-%d') as edit_date
			from 
				shop_deposit as d 
			where
				1
				$between_date
				$group_by
				order by d.edit_date ASC
				";

	$db->query($sql);
	$data_array = $db->fetchall();

if($mode == 'excel'){

	$info_type = 'deposit_informal';

	$goods_infos = $data_array;

	include("excel_out_columsinfo.php");

	$sql = "select conf_val from inventory_config where charger_ix='".$admininfo[charger_ix]."' and conf_name='deposit_info_".$info_type."' ";
	$db->query($sql);
	$db->fetch();
	$stock_report_excel = $db->dt[conf_val];

	$sql = "select conf_val from inventory_config where charger_ix='".$admininfo[charger_ix]."' and conf_name='check_deposit_info_".$info_type."' ";
	$db->query($sql);
	$db->fetch();
	$stock_report_excel_checked = $db->dt[conf_val];

	$check_colums = unserialize(stripslashes($stock_report_excel_checked));

	$columsinfo = $colums;

	include '../include/phpexcel/Classes/PHPExcel.php';
	PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

	date_default_timezone_set('Asia/Seoul');

	$inventory_excel = new PHPExcel();

	// 속성 정의
	$inventory_excel->getProperties()->setCreator("포비즈 코리아")
								 ->setLastModifiedBy("Mallstory.com")
								 ->setTitle("accounts plan price List")
								 ->setSubject("accounts plan price List")
								 ->setDescription("generated by forbiz korea")
								 ->setKeywords("mallstory")
								 ->setCategory("accounts plan price List");
	$col = 'A';
	foreach($check_colums as $key => $value){
		$inventory_excel->getActiveSheet(0)->setCellValue($col . "1", $columsinfo[$value][title]);
		$col++;
	}
	$before_pid = "";

	for($i = 0; $i < count($goods_infos); $i++)
	{
		$j="A";
		foreach($check_colums as $key => $value){
			if($key == "total_deposit"){
				$value_str = $goods_infos[$i][complete_deposit] - $goods_infos[$i][use_deposit] - $goods_infos[$i][withdrawl_deposit];
			}else{
				$value_str = $goods_infos[$i][$value];//$db1->dt[$value];
			}

			$inventory_excel->getActiveSheet()->setCellValue($j . ($z + 2), $value_str);
			$j++;

			unset($history_text);
		}
		$z++;
	}
	// 첫번째 시트 선택
	$inventory_excel->setActiveSheetIndex(0);

	// 너비조정
	$col = 'A';
	foreach($check_colums as $key => $value){
		$inventory_excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
		$col++;
	}

	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="deposit_Info_'.$info_type.'.xls"');
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($inventory_excel, 'Excel5');
	$objWriter->save('php://output');

	exit;


}
$Contents .= "
	<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
	<tr height=30 >
		<td>
		</td>
		<td colspan=5 align=right>";

		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
			$Contents .= "<span class='helpcloud' help_height='30' help_html='엑셀 다운로드 형식을 설정하실 수 있습니다.'>
			<a href='excel_config.php?".$QUERY_STRING."&info_type=deposit_informal&excel_type=deposit_info_excel' rel='facebox' ><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a></span>";
		}else{
			$Contents .= "
			<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a>";
		}

		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
			$Contents .= " <a href='deposit_informal.php?mode=excel&".str_replace($mode, "excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
		}else{
			$Contents .= " <a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
		}

$Contents .= "
		</td>
	</tr>
	</table>";

	
	$Contents .= "
				<table cellspacing='0' cellpadding='0' width='100%' border='0' class='list_table_box'>
				<colgroup>
				<col width='9%'>

				<col width='8%'>
				<col width='8%'>
				<col width='8%'>

				<col width='8%'>

				<col width='8%'>
				<col width='8%'>
				<col width='8%'>
				<col width='8%'>

				<col width='8%'>
				<col width='8%'>

				</colgroup>
				<tbody>
				<tr align='center' style='background-color:#f7f7f7' height='27'>
					<th class='s_td' rowspan='2'>일자</th> 
					<th class='m_td' colspan='3'>입금</th>
					<th class='m_td' colspan='1'>사용</th>
					<th class='m_td' colspan='4'>출금</th>
					<th class='m_td' rowspan='2'>입출금 합계</th>
					<th class='m_td' rowspan='2'>보유예치금</th>
				</tr>
				<tr align='center' style='background-color:#f7f7f7' height='27'>
					<th>입금대기</th>
					<th>입금취소</th>
					<th>입금완료(+)</th>

					<th>사용완료(-)</th>

					<th>출금요청</th>
					<th>출금취소</th>
					<th>출금확정</th>
					<th>출금완료(-)</th>
				</tr>";

	$Contents1 = "
				<tr align='center' style='background-color:#f7f7f7' height='27'>
					<td>평균</td>
					<td>0.54</td>
					<td>0</td>
					<td>0.54</td>
					<td>0</td>
					<td>0</td>
					<td>0</td>
					<td>0.54</td>
					<td>0</td>
					<td>0.54</td>
					<td>100%</td>
				</tr>";

	$Contents .= "
				<tr align='center' style='background-color:#f7f7f7' height='27'>
					<td>총합계</td>
					<td>{sum_wait_deposit}</td>
					<td>{sum_cancel_deposit}</td>
					<td>{sum_complete_deposit}</td>
					<td>{sum_use_deposit}</td>
					<td>{sum_request_deposit}</td>
					<td>{sum_request_cancel_deposit}</td>
					<td>{sum_confirm_deposit}</td>
					<td>{sum_withdrawl_deposit}</td>
					<td>{sum_deposit}</td>
					<td></td>
				</tr>";
	
	$Contents = $Contents.$Contents2;

	for($i=0;$i<count($data_array);$i++){

		if($send == '2'){	//월별
			$edit_date = substr($data_array[$i][edit_date],0,7);

			$sql = "select sum(total_deposit) as total_deposit from shop_deposit_informal where date_format(edit_date,'%Y-%m') = '".$edit_date."'";
			$mdb->query($sql);
			$mdb->fetch();
			$total_use_deposit = $mdb->dt[total_deposit];

		}else{
			$edit_date = $data_array[$i][edit_date];
			$sql = "select * from shop_deposit_informal where edit_date = '".$data_array[$i][edit_date]."'";
			$mdb->query($sql);
			$mdb->fetch();
			$total_use_deposit = $mdb->dt[total_deposit];
		}
	$Contents .= "
				<tr height='30' align='center'>
					<td class='list_box_td list_bg_gray'><font class='ver8'>".$edit_date."</font></td>
					<td class='list_box_td '><font class='ver8'>".number_format($data_array[$i][wait_deposit])."</font></td>
					<td class='list_box_td '><font class='ver8'>".number_format($data_array[$i][cancel_deposit])."</font></td>
					<td class='list_box_td point' style='padding-left:5px;'>".number_format($data_array[$i][complete_deposit])."<font class='ver8'></font></td>
					<td class='list_box_td point'><font class='ver8'>".number_format($data_array[$i][use_deposit])."</font></td>
					<td class='list_box_td '>".number_format($data_array[$i][request_deposit])."</td>
					<td class='list_box_td '>".number_format($data_array[$i][request_cancel_deposit])."</td>
					<td class='list_box_td point'>".number_format($data_array[$i][confirm_deposit])."</td>
					<td class='list_box_td list_bg_gray'><font class='ver8'>".number_format($data_array[$i][withdrawl_deposit])."</font></td>
					<td class='list_box_td list_bg_gray'>".number_format($data_array[$i][complete_deposit] - $data_array[$i][use_deposit] - $data_array[$i][withdrawl_deposit])."</td>
					<td class='list_box_td'>".number_format($total_use_deposit)."</td>
				</tr>";
				
				$sum_wait_deposit += $data_array[$i][wait_deposit];
				$sum_cancel_deposit += $data_array[$i][wait_deposit];
				$sum_complete_deposit += $data_array[$i][complete_deposit];
				$sum_use_deposit += $data_array[$i][use_deposit];
				$sum_request_deposit += $data_array[$i][request_deposit];
				$sum_request_cancel_deposit += $data_array[$i][request_cancel_deposit];
				$sum_confirm_deposit += $data_array[$i][confirm_deposit];
				$sum_withdrawl_deposit += $data_array[$i][withdrawl_deposit];
				$sum_deposit += $data_array[$i][complete_deposit] - $data_array[$i][use_deposit] - $data_array[$i][withdrawl_deposit];
				$sum_total_use_deposit += $total_use_deposit;
	}
	
	$Contents =str_replace("{sum_wait_deposit}",number_format($sum_wait_deposit),$Contents);
	$Contents =str_replace("{sum_cancel_deposit}",number_format($sum_cancel_deposit),$Contents);
	$Contents =str_replace("{sum_complete_deposit}",number_format($sum_complete_deposit),$Contents);
	$Contents =str_replace("{sum_use_deposit}",number_format($sum_use_deposit),$Contents);
	$Contents =str_replace("{sum_request_deposit}",number_format($sum_request_deposit),$Contents);
	$Contents =str_replace("{sum_request_cancel_deposit}",number_format($sum_request_cancel_deposit),$Contents);
	$Contents =str_replace("{sum_confirm_deposit}",number_format($sum_confirm_deposit),$Contents);
	$Contents =str_replace("{sum_withdrawl_deposit}",number_format($sum_withdrawl_deposit),$Contents);
	$Contents =str_replace("{sum_deposit}",number_format($sum_deposit),$Contents);
	$Contents =str_replace("{sum_total_use_deposit}",number_format($sum_total_use_deposit),$Contents);


	$Contents = $Contents."
				</tbody>
				</table>";

$Script = "<script language='javascript' src='../include/DateSelect.js'></script>
<script language='javascript' >
function ChangeOrderDate(frm){
	if(frm.orderdate.checked){
		$('#startDate').addClass('point_color');
		$('#endDate').addClass('point_color');
		$('#endDate').attr('disabled',false);
		$('#startDate').attr('disabled',false);
	}else{
		$('#startDate').removeClass('point_color');
		$('#endDate').removeClass('point_color');
		$('#endDate').attr('disabled',true);
		$('#startDate').attr('disabled',true);
	}
}
function ChangevOrderDate(frm){
	if(frm.vorderdate.checked){
		$('#vstartDate').addClass('point_color');
		$('#vendDate').addClass('point_color');
		$('#vendDate').attr('disabled',false);
		$('#vstartDate').attr('disabled',false);
	}else{
		$('#vstartDate').removeClass('point_color');
		$('#vendDate').removeClass('point_color');
		$('#vendDate').attr('disabled',true);
		$('#vstartDate').attr('disabled',true);
	}
}
</script>";

$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = buyer_accounts_menu();
$P->Navigation = $page_navigation;
$P->title = $page_title;
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>