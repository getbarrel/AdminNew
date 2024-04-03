<?
include_once("../class/layout.class");
include("../seller_accounts/accounts.lib.php");

if ($startDate == ""){
	$before30day = mktime(0, 0, 0, date("m")  , date("d")-30, date("Y"));
	$startDate = date("Y-m-d", $before30day);
	$endDate = date("Y-m-d");
}

$db = new Database;

$max = 15; //페이지당 갯수

if ($page == ''){
	$start = 0;
	$page  = 1;
}else{
	$start = ($page - 1) * $max;
}

if($mode!="search"){
	$pay_type[]="G";
	$pay_type[]="R";
}

$order_from = "cafe24_box";

$Contents = "
<table width='100%'>
	<tr>
		<td align='left' colspan=6 > ".GetTitleNavigation("카페24정산리스트", "주문관리 > 카페24정산리스트")."</td>
	</tr>
</table>
<form name='search_frm' method='get' action=''>
<input type='hidden' name='mode' value='search' />
<table width='100%' cellpadding='0' cellspacing='0' border=0>
	<tr>
		<td style='width:75%;' colspan=2 valign=top>
			<table width=100%  border=0>
				<tr height=25>
					<td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b class=blk>주문정보 검색하기</b></td>
				</tr>
				<tr>
					<td align='left' colspan=2 width='100%' valign=top style='padding-top:5px;'>
						<table class='box_shadow' style='width:100%;' align=left cellpadding='0' cellspacing='0' border='0'>
							<tr>
								<th class='box_01'></th>
								<td class='box_02'></td>
								<th class='box_03'></th>
							</tr>
							<tr>
								<th class='box_04'></th>
								<td class='box_05'>
									<TABLE cellSpacing=0 cellPadding=3 style='width:100%;' align=center border=0 class='search_table_box'>
											<col width=15%>
											<col width=35%>
											<col width=15%>
											<col width=35%>
											<tr>
												<th class='search_box_title'>결제타입</th>
												<td class='search_box_item' colspan='3'>
													<table cellpadding=0 cellspacing=0 width='100%' border='0' >
														<col width='15%'>
														<col width='15%'>
														<col width='15%'>
														<col width='15%'>
														<col width='15%'>
														<col width='15%'>
														<TR height=25>
															<TD>
																<input type='checkbox' name='pay_type[]'  id='pay_type_g' value='G' ".CompareReturnValue("G",$pay_type,' checked')." ><label for='pay_type_g'>정상</label>
															</TD>
															<TD>
																<input type='checkbox' name='pay_type[]'  id='pay_type_r' value='R' ".CompareReturnValue("R",$pay_type,' checked')." ><label for='pay_type_r'>환불</label>
															</TD>
															<TD></TD>
															<TD></TD>
															<TD></TD>
															<TD></TD>
														</TR>
													</TABLE>
												</td>
											</tr>
											<tr height=33>
												<th class='search_box_title'>
													입금(환불일자)
												</th>
												<td class='search_box_item' colspan=3>
													".search_date('startDate','endDate',$startDate,$endDate)."
												</td>
											</tr>
									</TABLE>
								</td>
								<th class='box_06'></th>
							</tr>
							<tr>
								<th class='box_07'></th>
								<td class='box_08'></td>
								<th class='box_09'></th>
							</tr>
							</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan=2 align=center style='padding:10px 0px;'>
			<input type='image' src='../images/".$admininfo["language"]."/bt_search.gif' border=0>
		</td>
	</tr>
</table>
</form>

<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
 <tr height=30>";
	
	$SQL="";

	if(count($pay_type) > 0){

		if(in_array("G",$pay_type)){
			$SQL.="SELECT
					'N' as refund_bool, od.oid, od.ptprice, od.ptprice - od.pt_dcprice as total_sale_price, ic_date as pay_date
				FROM
					".TBL_SHOP_ORDER_DETAIL." od 
				WHERE
					od.order_from='".$order_from."'
				AND
					od.status not in ('".ORDER_STATUS_SETTLE_READY."','".ORDER_STATUS_EXCHANGE_READY."')
				AND
					od.delivery_policy !='9'
				AND
					DATE_FORMAT(od.ic_date,'%Y%m%d') between '".str_replace("-","",$startDate)."' and '".str_replace("-","",$endDate)."' ";
			
		}

		if(count($pay_type)==2){
			$SQL.=" UNION ALL ";
		}

		if(in_array("R",$pay_type)){
			$SQL.=" SELECT
					'Y' as refund_bool, od.oid, od.ptprice, od.ptprice - od.pt_dcprice as total_sale_price, fc_date as pay_date
				FROM 
					".TBL_SHOP_ORDER_DETAIL." od 
				WHERE
					od.order_from='".$order_from."'
				AND 
					od.status not in ('".ORDER_STATUS_SETTLE_READY."','".ORDER_STATUS_EXCHANGE_COMPLETE."')
				AND
					od.refund_status = '".ORDER_STATUS_REFUND_COMPLETE."'
				AND
					DATE_FORMAT(od.fc_date,'%Y%m%d') between '".str_replace("-","",$startDate)."' and '".str_replace("-","",$endDate)."' ";
		}

		$SQL = trim($SQL);
		$db->query($SQL);
		$total = $db->total;

		$sum_ptprice = 0;
		for ($i = 0; $i < $db->total; $i++){
			$db->fetch($i);
			
			if($db->dt[refund_bool]=="Y")		$sum_ptprice+=$db->dt[ptprice]*-1;
			else								$sum_ptprice+=$db->dt[ptprice];
		}


		$sql="SELECT o.buserid, od.* FROM (
			".$SQL."
		) od left join ".TBL_SHOP_ORDER." o on (o.oid=od.oid) 
		ORDER BY pay_date desc
		LIMIT $start, $max ";

		$db->query($sql);
	}


	if($act == "excel"){

		include '../include/phpexcel/Classes/PHPExcel.php';

		PHPExcel_Settings::setZipClass(PHPExcel_Settings::ZIPARCHIVE);

		date_default_timezone_set('Asia/Seoul');

		$excel = new PHPExcel();


		// 속성 정의
		$excel->getProperties()->setCreator("포비즈 코리아")
									 ->setLastModifiedBy("Mallstory.com")
									 ->setTitle("accounts List")
									 ->setSubject("accounts List")
									 ->setDescription("generated by forbiz korea")
									 ->setKeywords("mallstory")
									 ->setCategory("accounts List");
		
		$excel->getActiveSheet(0)->setCellValue('A' . 1, "번호");
		$excel->getActiveSheet(0)->setCellValue('B' . 1, "Cafe24 ID");
		$excel->getActiveSheet(0)->setCellValue('C' . 1, "주문번호");
		$excel->getActiveSheet(0)->setCellValue('D' . 1, "결제타입");
		$excel->getActiveSheet(0)->setCellValue('E' . 1, "상품금액");
		$excel->getActiveSheet(0)->setCellValue('F' . 1, "할인금액");
		$excel->getActiveSheet(0)->setCellValue('G' . 1, "입금(환불일자)");
		
		if($SQL!=""){
			$db->query($SQL);
		}

		for ($i = 0; $i < $db->total; $i++)
		{
			$db->fetch($i);
			
			if($db->dt[refund_bool]=="Y"){
				$refund_bool="환불";
				$ptprice=$db->dt[ptprice]*-1;
			}else{
				$refund_bool="정상";
				$ptprice=$db->dt[ptprice];
			}

			$excel->getActiveSheet()->setCellValue('A' . ($i + 2), ($i+1));
			$excel->getActiveSheet()->setCellValue('B' . ($i + 2), $db->dt[buserid]);
			$excel->getActiveSheet()->setCellValue('C' . ($i + 2), $db->dt[oid]);
			$excel->getActiveSheet()->setCellValue('D' . ($i + 2), $refund_bool);
			$excel->getActiveSheet()->setCellValue('E' . ($i + 2), $ptprice);
			$excel->getActiveSheet()->setCellValue('F' . ($i + 2), $db->dt[total_sale_price]);
			$excel->getActiveSheet()->setCellValue('G' . ($i + 2), $db->dt[pay_date]);
		}

		// 첫번째 시트 선택
		$excel->setActiveSheetIndex(0);

		$excel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$excel->getActiveSheet()->getColumnDimension('B')->setWidth(10);
		$excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
		$excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
		$excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
		$excel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
		$excel->getActiveSheet()->getColumnDimension('G')->setWidth(15);


		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.iconv("utf-8","cp949","cafe24주문리스트.xls").'"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
		$objWriter->save('php://output');

		exit;
	}

 $Contents .= "<td colspan=3 align=left><b class=blk>전체 : ".number_format($total)." 건 상품금액 : ".number_format($sum_ptprice)." 원</b> </td>
			<td colspan=9 align=right >
				<a href='../order/cafe24_order_list.php?act=excel&".$QUERY_STRING."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>
			</td>
		</tr>
  </table>";

	$Contents .= "
	  <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' class='list_table_box'>
		<tr height='25' >
			<td width='14%' align='center'  class='m_td' nowrap><b>Cafe24 ID</b></td>
			<td width='*' align='center' class='m_td'><b>주문번호</b></td>
			<td width='14%' align='center'  class='m_td' nowrap><b>결제타입</b></td>
			<td width='14%' align='center' class='m_td' nowrap><b>상품금액</b></td>
			<td width='14%' align='center' class='m_td' nowrap><b>할인금액(쿠폰제외)</b></td>
			<td width='20%' align='center' class='m_td' nowrap><b>입금(환불일자)</b></td>
		</tr>";

	if($db->total){
		for ($i = 0; $i < $db->total; $i++)
		{
			$db->fetch($i);

			if($db->dt[refund_bool]=="Y"){
				$refund_bool="환불";
				$ptprice=$db->dt[ptprice]*-1;
			}else{
				$refund_bool="정상";
				$ptprice=$db->dt[ptprice];
			}
	
			$Contents .= "<tr height=28 >";
				$Contents .= "<td  class='list_box_td ' style='line-height:140%' align=center>".$db->dt[buserid]."</td>";
				$Contents .= "<td class='list_box_td point' style='line-height:140%' align=center><spanstyle='color:#007DB7;font-weight:bold;'>".$db->dt[oid]."</span></td>";
				$Contents .= "<td style='line-height:140%' align=center class='list_box_td'>".$refund_bool."</td>";
				$Contents .= "<td class='list_box_td ' align='center' nowrap>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($ptprice)."
				".$currency_display[$admin_config["currency_unit"]]["back"]."</td>";
				$Contents .= "<td class='list_box_td ' align='center' nowrap>".$currency_display[$admin_config["currency_unit"]]["front"]."".number_format($db->dt[total_sale_price])."
				".$currency_display[$admin_config["currency_unit"]]["back"]."</td>";
				$Contents .= "<td class='list_box_td' align='center'  nowrap>".$db->dt[pay_date]."</td>";
			$Contents .= "</tr>";
		}

	}else{
		$Contents .= "<tr height=50><td colspan='6' align=center>조회된 결과가 없습니다.</td></tr>
				";
	}
	$Contents .= "
	  </table>";



if($QUERY_STRING == "nset=$nset&page=$page"){
	$query_string = str_replace("nset=$nset&page=$page","",$QUERY_STRING) ;
}else{
	$query_string = str_replace("nset=$nset&page=$page&","","&".$QUERY_STRING) ;
}

$Contents .= "
  <table width='100%' border='0' cellpadding='3' cellspacing='0' align='center' >
  <tr height=40>
    <td colspan='12' align='right'>&nbsp;".page_bar($total, $page, $max,$query_string,"")."&nbsp;</td>
  </tr>
</table>
";

$P = new LayOut();

$P->strLeftMenu = order_menu();
$P->OnloadFunction = "";
$P->addScript = "";
$P->Navigation = "주문관리 > 카페24정산리스트 ";
$P->title = "카페24정산리스트";
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>