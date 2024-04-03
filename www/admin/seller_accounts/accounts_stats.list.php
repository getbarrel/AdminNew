<?
include("../class/layout.class");
include ("./accounts.lib.php");

$db = new Database;


//검색 1주일단위 디폴트
if ($startDate == ""){
	$before7day = mktime(0, 0, 0, date("m")  , date("d")-7, date("Y"));

	$startDate = date("Y-m-d", $before7day);
	$endDate = date("Y-m-d");
}

if($mode != 'search'){
	$period = 1;
}

$Contents .="
			<table cellpadding=0 cellspacing=0 width=100% border=0 align=center style='margin-top:10px;'>
			<tr>
				<td align='left' colspan=6 >".GetTitleNavigation($page_title, $page_navigation)."</td>
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
													<td class='search_box_item'>
														<input type=radio name='period' value='1' id='schdays'  ".CompareReturnValue("1",$period,"checked")."><label for='schdays'>일별</label>
														<input type=radio name='period' value='2' id='schmonth' ".CompareReturnValue("2",$period,"checked")."><label for='schmonth'>월별</label>
													</td>
												</tr>
												 <tr height='27'>
													<th class='search_box_title' bgcolor='#efefef' width='150' align='center'>기간</th>
													<td class='search_box_item'>
														".search_date('startDate','endDate',$startDate,$endDate)."
													</td>
												 </tr>
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

if($period == "1"){
	$aa_between_date = " ,'%Y-%m-%d') between '".$startDate."' and '".$endDate."'";
	$ar_between_date = " date_format(regdate,'%Y-%m-%d') between '".$startDate."' and '".$endDate."'";
	$ac_ap_between_date = $ar_between_date;
	$group_by = " group by date_format(stats_date,'%Y-%m-%d')";
}else if($period == "2"){
	$aa_between_date = " ,'%Y-%m') between '".substr($startDate,0,7)."' and '".substr($endDate,0,7)."'";
	$ar_between_date = " date_format(regdate,'%Y-%m-%d') between '".$startDate."' and '".$endDate."'";
	$ac_ap_between_date = $ar_between_date;
	$group_by = " group by date_format(stats_date,'%Y-%m')";
}

if($page_type=="seller"){
	$group_by = " group by company_id";
}

$sql="select company_id from ".TBL_COMMON_COMPANY_DETAIL." where com_type='A' ";
$db->query($sql);
$db->fetch();
$admin_com_id = $db->dt[company_id];

$aa_where = "
where od.product_type NOT IN (".implode(',',$sns_product_type).") 
and od.account_type !='3' 
and DATE_FORMAT(DATE_ADD(case od.ac_delivery_type when '".ORDER_STATUS_INCOM_COMPLETE."' then od.ic_date when '".ORDER_STATUS_DELIVERY_READY."' then od.dr_date when '".ORDER_STATUS_DELIVERY_ING."' then od.di_date when '".ORDER_STATUS_DELIVERY_COMPLETE."' then od.dc_date when '".ORDER_STATUS_BUY_FINALIZED."' then od.bf_date else od.dc_date end,INTERVAL od.ac_expect_date DAY) ".$aa_between_date."
and od.company_id != '".$admin_com_id."'
and
(
	(
		".$AC_NORMARL_QUERY."
	)
	OR
	(
		".$AC_REFUND_QUERY."
	)
) ";

$aa_sub_where="
and odr.product_type NOT IN (".implode(',',$sns_product_type).") 
and odr.company_id != '".$admin_com_id."'
and odr.account_type !='3'
and  DATE_FORMAT(DATE_ADD(case odr.ac_delivery_type when '".ORDER_STATUS_INCOM_COMPLETE."' then odr.ic_date when '".ORDER_STATUS_DELIVERY_READY."' then odr.dr_date when '".ORDER_STATUS_DELIVERY_ING."' then odr.di_date when '".ORDER_STATUS_DELIVERY_COMPLETE."' then odr.dc_date when '".ORDER_STATUS_BUY_FINALIZED."' then odr.bf_date else odr.dc_date end,INTERVAL odr.ac_expect_date DAY) ".$aa_between_date."";

$aa_sql = "select
	stats_date, company_id,
	sum(case when refund_bool='Y' then -(p_sell_price+d_expect_price) else (p_sell_price+d_expect_price) end) as sell_aa_price,
	sum(case when refund_bool='Y' then -(p_expect_price-p_fee_price-p_dc_allotment_price) else (p_expect_price-p_fee_price-p_dc_allotment_price) end) as p_aa_price,
	sum(case when refund_bool='Y' then -(d_expect_price-d_dc_allotment_price) else (d_expect_price-d_dc_allotment_price) end) as d_aa_price,
	'0' as sell_ar_price,
	'0' as p_ar_price,
	'0' as d_ar_price,
	'0' as sell_ac_price,
	'0' as p_ac_price,
	'0' as d_ac_price,
	'0' as sell_ap_price,
	'0' as p_ap_price,
	'0' as d_ap_price
from
(
	select
		DATE_FORMAT(DATE_ADD(case od.ac_delivery_type when '".ORDER_STATUS_INCOM_COMPLETE."' then od.ic_date when '".ORDER_STATUS_DELIVERY_READY."' then od.dr_date when '".ORDER_STATUS_DELIVERY_ING."' then od.di_date when '".ORDER_STATUS_DELIVERY_COMPLETE."' then od.dc_date when '".ORDER_STATUS_BUY_FINALIZED."' then od.bf_date else od.dc_date end,INTERVAL od.ac_expect_date DAY),'%Y-%m-%d') as stats_date,
		od.company_id,

		case when ".$AC_REFUND_QUERY." then 'Y' else 'N' end as refund_bool,

		pt_dcprice as p_sell_price,
		case when od.account_type in ('1','') then od.pt_dcprice else od.coprice*od.pcnt end as p_expect_price,
		case when od.account_type in ('1','') then od.pt_dcprice*(od.commission)/100 else od.coprice*od.pcnt*(od.commission)/100 end as p_fee_price,

		(select sum(dc.dc_price_seller) from shop_order_detail_discount dc where dc.oid=od.oid and dc.od_ix=od.od_ix and dc.dc_type not in ('DCP','DE')) as p_dc_allotment_price,

		(
			case when 
				".$AC_REFUND_QUERY."
			then 
				case when 
					od.od_ix = (select max(odr.od_ix) from shop_order_detail odr where odr.oid=od.oid and odr.ori_company_id = od.ori_company_id and odr.delivery_type = od.delivery_type and odr.delivery_package = od.delivery_package and odr.delivery_method = od.delivery_method and odr.delivery_pay_method = od.delivery_pay_method and odr.delivery_addr_use = od.delivery_addr_use and odr.factory_info_addr_ix = od.factory_info_addr_ix and (case od.delivery_package when 'Y' then odr.pid=od.pid else 1=1 end) 
					and ".str_replace("od.","odr.",$AC_REFUND_QUERY)." ".$aa_sub_where.")
				then
					(
						odv.refund_delivery_price - odv.ac_refund_delivery_price
					) 
				else
					'0' 
				end
			else
				case when 
					od.od_ix = (select max(odr.od_ix) from shop_order_detail odr where odr.oid=od.oid and odr.ori_company_id = od.ori_company_id and odr.delivery_type = od.delivery_type and odr.delivery_package = od.delivery_package and odr.delivery_method = od.delivery_method and odr.delivery_pay_method = od.delivery_pay_method and odr.delivery_addr_use = od.delivery_addr_use and odr.factory_info_addr_ix = od.factory_info_addr_ix and (case od.delivery_package when 'Y' then odr.pid=od.pid else 1=1 end)
					and ".str_replace("od.","odr.",$AC_NORMARL_QUERY)." ".$aa_sub_where.")
				then
					(
						odv.delivery_price - odv.ac_delivery_price 
					) 
				else
					'0' 
				end
			end

		) as d_expect_price,
		
		(
			case when 
				".$AC_REFUND_QUERY."
			then
				'0'
			else
				case when 
					od.od_ix = (select max(odr.od_ix) from shop_order_detail odr where odr.oid=od.oid and odr.ori_company_id = od.ori_company_id and odr.delivery_type = od.delivery_type and odr.delivery_package = od.delivery_package and odr.delivery_method = od.delivery_method and odr.delivery_pay_method = od.delivery_pay_method and odr.delivery_addr_use = od.delivery_addr_use and odr.factory_info_addr_ix = od.factory_info_addr_ix and (case od.delivery_package when 'Y' then odr.pid=od.pid else 1=1 end)
					and ".str_replace("od.","odr.",$AC_NORMARL_QUERY)." ".$aa_sub_where.")
				then
					(select sum(dc.dc_price_seller) from shop_order_detail_discount dc where dc.oid=od.oid and dc.ode_ix=odv.ode_ix and dc.dc_type in ('DCP','DE')) 
				else
					'0' 
				end
			end
		) as d_dc_allotment_price

	from
		".TBL_SHOP_ORDER_DETAIL." od
	left join
		".TBL_SHOP_ORDER." o on o.oid = od.oid
	left join shop_order_delivery odv on (
		odv.oid=od.oid
		and odv.ori_company_id = od.ori_company_id
		and odv.delivery_type = od.delivery_type
		and odv.delivery_package = od.delivery_package
		and odv.delivery_method = od.delivery_method
		and odv.delivery_pay_type = od.delivery_pay_method
		and odv.delivery_addr_use = od.delivery_addr_use
		and odv.factory_info_addr_ix = od.factory_info_addr_ix
		and (case od.delivery_package when 'Y' then odv.pid=od.pid else 1=1 end)
		and odv.delivery_type != '1'
	)
	".$aa_where."
) o
";

$ar_sql="select 
	date_format(regdate,'%Y-%m-%d') as stats_date,
	company_id,
	'0' as sell_aa_price,
	'0' as p_aa_price,
	'0' as d_aa_price,
	p_sell_price+d_sell_price as sell_ar_price,
	p_ac_price as p_ar_price,
	d_ac_price as d_ar_price,
	'0' as sell_ac_price,
	'0' as p_ac_price,
	'0' as d_ac_price,
	'0' as sell_ap_price,
	'0' as p_ap_price,
	'0' as d_ap_price
from shop_accounts 
where ".$ar_between_date." and status = '".ORDER_STATUS_ACCOUNT_READY."' ";

$ac_ap_sql="select 
	date_format(regdate,'%Y-%m-%d') as stats_date,
	company_id,
	'0' as sell_aa_price,
	'0' as p_aa_price,
	'0' as d_aa_price,
	'0' as sell_ar_price,
	'0' as p_ar_price,
	'0' as d_ar_price,
	case when status='".ORDER_STATUS_ACCOUNT_COMPLETE."'  then p_sell_price+d_sell_price else '0' end as sell_ac_price,
	case when status='".ORDER_STATUS_ACCOUNT_COMPLETE."'  then p_tax_total_price+p_tax_free_price else '0' end as p_ac_price,
	case when status='".ORDER_STATUS_ACCOUNT_COMPLETE."'  then d_tax_total_price+d_tax_free_price else '0' end as d_ac_price,
	case when status='".ORDER_STATUS_ACCOUNT_PAYMENT."'  then p_sell_price+d_sell_price else '0' end as sell_ap_price,
	case when status='".ORDER_STATUS_ACCOUNT_PAYMENT."'  then p_tax_total_price+p_tax_free_price else '0' end as p_ap_price,
	case when status='".ORDER_STATUS_ACCOUNT_PAYMENT."'  then d_tax_total_price+d_tax_free_price else '0' end as d_ap_price
from shop_accounts_remittance 
where ".$ac_ap_between_date." ";

$sql="select ";

if($page_type=="seller"){
	$sql.=" b.* ,c.com_name
	from (
		select ";
}
	$sql.="
	stats_date,
	company_id,
	sum(sell_aa_price) as sell_aa_price,
	sum(p_aa_price) as p_aa_price,
	sum(d_aa_price) as d_aa_price,
	sum(sell_ar_price) as sell_ar_price,
	sum(p_ar_price) as p_ar_price,
	sum(d_ar_price) as d_ar_price,
	sum(sell_ac_price) as sell_ac_price,
	sum(p_ac_price) as p_ac_price,
	sum(d_ac_price) as d_ac_price,
	sum(sell_ap_price) as sell_ap_price,
	sum(p_ap_price) as p_ap_price,
	sum(d_ap_price) as d_ap_price
from (";
	if($page_type=="systhesize"){
		$sql.="
		".$aa_sql."
		union all
		".$ar_sql."
		union all";
	}
	$sql.="
	".$ac_ap_sql."
) a
where stats_date is not null 
".$group_by." ";

if($page_type=="seller"){
	$sql.=" ) b left join common_company_detail c on (b.company_id = c.company_id) ";
}


$db->query($sql);
$data_array = $db->fetchall("object");

if(!$mode){
    $data_array = array();
}
/*
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
*/

$Contents .= "
	<table width='100%' border='0' cellpadding='0' cellspacing='0' align='center' >
	<tr height=30 >
		<td>
		</td>
		<td colspan=5 align=right>";
		
		/*
		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
			$Contents .= "<span class='helpcloud' help_height='30' help_html='엑셀 다운로드 형식을 설정하실 수 있습니다.'>
			<a href='excel_config.php?".$QUERY_STRING."&info_type=deposit_informal&excel_type=deposit_info_excel' rel='facebox' ><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a></span>";
		}else{
			$Contents .= "
			<a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_set.gif'></a>";
		}
		*/
		
		/*
		if(checkMenuAuth(md5($_SERVER["PHP_SELF"]),"E")){
			$Contents .= " <a href='deposit_informal.php?mode=excel&".str_replace($mode, "excel",$_SERVER["QUERY_STRING"])."'><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
		}else{
			$Contents .= " <a href=\"".$auth_excel_msg."\"><img src='../images/".$admininfo["language"]."/btn_excel_save.gif' border=0></a>";
		}
		*/

$Contents .= "
		</td>
	</tr>
	</table>";

	
	$Contents .= "
				<table cellspacing='0' cellpadding='0' width='100%' border='0' class='list_table_box'>
				<tbody>
				<tr align='center' style='background-color:#f7f7f7' height='27'>";
					if($page_type=="seller"){
						$Contents .= "
						<th class='s_td' width='*' rowspan='2'>업체명</th>";
					}else{
						$Contents .= "
						<th class='s_td' width='*' rowspan='2'>일자</th>";
					}
					
					if($page_type=="systhesize"){
						$Contents .= "
						<th class='m_td' colspan='3'>정산예정금액</th>
						<th class='m_td' colspan='3'>정산확정금액</th>
						<th class='m_td' colspan='3'>송금대기금액</th>
						<th class='e_td' colspan='3'>송금완료금액</th>";
					}else{
						$Contents .= "
						<th class='m_td' colspan='5'>합계</th>
						<th class='m_td' colspan='5'>송금대기금액</th>
						<th class='e_td' colspan='5'>송금완료금액</th>";
					}
				$Contents .= "
				</tr>
				<tr align='center' style='background-color:#f7f7f7' height='27'>";
					if($page_type=="systhesize"){
						$Contents .= "
						<th class='m_td' width='8%'>합계</th>
						<th class='m_td' width='8%'>상품금액</th>
						<th class='m_td' width='8%'>배송금액</th>
						<th class='m_td' width='8%'>합계</th>
						<th class='m_td' width='8%'>상품금액</th>
						<th class='m_td' width='8%'>배송금액</th>
						<th class='m_td' width='8%'>합계</th>
						<th class='m_td' width='8%'>상품금액</th>
						<th class='m_td' width='8%'>배송금액</th>
						<th class='m_td' width='8%'>합계</th>
						<th class='m_td' width='8%'>상품금액</th>
						<th class='m_td' width='8%'>배송금액</th>";
					}else{
						$Contents .= "
						<th class='m_td' width='6%'>합계</th>
						<th class='m_td' width='6%'>상품금액</th>
						<th class='m_td' width='6%'>배송금액</th>
						<th class='m_td' width='6%'>수익</th>
						<th class='m_td' width='6%'>수익율</th>
						<th class='m_td' width='6%'>합계</th>
						<th class='m_td' width='6%'>상품금액</th>
						<th class='m_td' width='6%'>배송금액</th>
						<th class='m_td' width='6%'>수익</th>
						<th class='m_td' width='6%'>수익율</th>
						<th class='m_td' width='6%'>합계</th>
						<th class='m_td' width='6%'>상품금액</th>
						<th class='m_td' width='6%'>배송금액</th>
						<th class='m_td' width='6%'>수익</th>
						<th class='m_td' width='6%'>수익율</th>";
					}
				$Contents .= "
				</tr>";
	if($page_type=="systhesize"){
		$Contents .= "
				<tr align='center' style='background-color:#f7f7f7' height='27'>
					<td>총합계</td>
					<td>{sum_total_aa_price}</td>
					<td>{sum_p_aa_price}</td>
					<td>{sum_d_aa_price}</td>
					<td>{sum_total_ar_price}</td>
					<td>{sum_p_ar_price}</td>
					<td>{sum_d_ar_price}</td>
					<td>{sum_total_ac_price}</td>
					<td>{sum_p_ac_price}</td>
					<td>{sum_d_ac_price}</td>
					<td>{sum_total_ap_price}</td>
					<td>{sum_p_ap_price}</td>
					<td>{sum_d_ap_price}</td>
				</tr>";
	}else{
		$Contents .= "
				<tr align='center' style='background-color:#f7f7f7' height='27'>
					<td>총합계</td>
					<td>{sum_total_price}</td>
					<td>{sum_p_total_price}</td>
					<td>{sum_d_total_price}</td>
					<td>{sum_total_profit_price}</td>
					<td>{sum_total_profit_rate}%</td>

					<td>{sum_total_ac_price}</td>
					<td>{sum_p_ac_price}</td>
					<td>{sum_d_ac_price}</td>
					<td>{sum_ac_profit_price}</td>
					<td>{sum_ac_profit_rate}%</td>

					<td>{sum_total_ap_price}</td>
					<td>{sum_p_ap_price}</td>
					<td>{sum_d_ap_price}</td>
					<td>{sum_ap_profit_price}</td>
					<td>{sum_ap_profit_rate}%</td>
				</tr>";
	}

	$Contents = $Contents.$Contents2;

	for($i=0;$i<count($data_array);$i++){
		
		if($period == '2'){	//월별
			$stats_date = substr($data_array[$i][stats_date],0,7);
		}else{
			$stats_date = $data_array[$i][stats_date];
		}
			
		if($page_type=="systhesize"){
			$Contents .= "
			<tr height='30' align='center'>
				<td class='list_box_td list_bg_gray' nowrap>".$stats_date."</td>
				<td class='list_box_td point'>".number_format($data_array[$i][p_aa_price]+$data_array[$i][d_aa_price])."</td>
				<td class='list_box_td '>".number_format($data_array[$i][p_aa_price])."</td>
				<td class='list_box_td '>".number_format($data_array[$i][d_aa_price])."</td>
				<td class='list_box_td point'>".number_format($data_array[$i][p_ar_price]+$data_array[$i][d_ar_price])."</td>
				<td class='list_box_td '>".number_format($data_array[$i][p_ar_price])."</td>
				<td class='list_box_td '>".number_format($data_array[$i][d_ar_price])."</td>
				<td class='list_box_td point'>".number_format($data_array[$i][p_ac_price]+$data_array[$i][d_ac_price])."</td>
				<td class='list_box_td '>".number_format($data_array[$i][p_ac_price])."</td>
				<td class='list_box_td '>".number_format($data_array[$i][d_ac_price])."</td>
				<td class='list_box_td point'>".number_format($data_array[$i][p_ap_price]+$data_array[$i][d_ap_price])."</td>
				<td class='list_box_td '>".number_format($data_array[$i][p_ap_price])."</td>
				<td class='list_box_td '>".number_format($data_array[$i][d_ap_price])."</td>
			</tr>";

			$sum_total_aa_price += $data_array[$i][p_aa_price]+$data_array[$i][d_aa_price];
			$sum_p_aa_price += $data_array[$i][p_aa_price];
			$sum_d_aa_price += $data_array[$i][d_aa_price];
			$sum_total_ar_price += $data_array[$i][p_ar_price]+$data_array[$i][d_ar_price];
			$sum_p_ar_price += $data_array[$i][p_ar_price];
			$sum_d_ar_price += $data_array[$i][d_ar_price];
			$sum_total_ac_price += $data_array[$i][p_ac_price]+$data_array[$i][d_ac_price];
			$sum_p_ac_price += $data_array[$i][p_ac_price];
			$sum_d_ac_price += $data_array[$i][d_ac_price];
			$sum_total_ap_price += $data_array[$i][p_ap_price]+$data_array[$i][d_ap_price];
			$sum_p_ap_price += $data_array[$i][p_ap_price];
			$sum_d_ap_price += $data_array[$i][d_ap_price];

		}else{

			$total_ac_price = $data_array[$i][p_ac_price]+$data_array[$i][d_ac_price];
			$total_ac_profit_price = $data_array[$i][sell_ac_price]-($data_array[$i][p_ac_price]+$data_array[$i][d_ac_price]);

			$total_ap_price = $data_array[$i][p_ap_price]+$data_array[$i][d_ap_price];
			$total_ap_profit_price = $data_array[$i][sell_ap_price]-($data_array[$i][p_ap_price]+$data_array[$i][d_ap_price]);
			
			$total_price=$total_ac_price+$total_ap_price;
			$total_profit_price = $total_ac_profit_price+$total_ap_profit_price;
			
			$total_sell_price = $data_array[$i][sell_ac_price]+$data_array[$i][sell_ap_price];

			$Contents .= "
			<tr height='30' align='center'>";
				if($page_type=="seller"){
					$Contents .= "
					<td class='list_box_td list_bg_gray' nowrap>".$data_array[$i][com_name]."</td>";
				}else{
					$Contents .= "
					<td class='list_box_td list_bg_gray' nowrap>".$stats_date."</td>";
				}
				$Contents .= "
				<td class='list_box_td point'>".number_format($total_price)."</td>
				<td class='list_box_td '>".number_format($data_array[$i][p_ac_price]+$data_array[$i][p_ap_price])."</td>
				<td class='list_box_td '>".number_format($data_array[$i][d_ac_price]+$data_array[$i][d_ap_price])."</td>
				<td class='list_box_td '>".number_format($total_profit_price)."</td>
				<td class='list_box_td '>
					".($total_sell_price ? number_format(round($total_profit_price/$total_sell_price*100),2) : 0)."%
				</td>
				<td class='list_box_td point'>".number_format($total_ac_price)."</td>
				<td class='list_box_td '>".number_format($data_array[$i][p_ac_price])."</td>
				<td class='list_box_td '>".number_format($data_array[$i][d_ac_price])."</td>
				<td class='list_box_td '>".number_format($total_ac_profit_price)."</td>
				<td class='list_box_td '>
					".($data_array[$i][sell_ac_price] ? number_format(round($total_ac_profit_price/$data_array[$i][sell_ac_price]*100),2) : 0)."%
				</td>
				<td class='list_box_td point'>".number_format($total_ap_price)."</td>
				<td class='list_box_td '>".number_format($data_array[$i][p_ap_price])."</td>
				<td class='list_box_td '>".number_format($data_array[$i][d_ap_price])."</td>
				<td class='list_box_td '>".number_format($total_ap_profit_price)."</td>
				<td class='list_box_td '>
					".($data_array[$i][sell_ap_price] ? number_format(round($total_ap_profit_price/$data_array[$i][sell_ap_price]*100),2) : 0)."%
				</td>
			</tr>";

			$sum_total_price += $total_price;
			$sum_p_total_price += $data_array[$i][p_ac_price]+$data_array[$i][p_ap_price];
			$sum_d_total_price += $data_array[$i][d_ac_price]+$data_array[$i][d_ap_price];
			$sum_total_profit_price += $total_profit_price;
			$sum_total_sell_price += $total_sell_price;

			$sum_total_ac_price += $total_ac_price;
			$sum_p_ac_price += $data_array[$i][p_ac_price];
			$sum_d_ac_price += $data_array[$i][d_ac_price];
			$sum_ac_profit_price += $total_ac_profit_price;
			$sum_sell_ac_price += $data_array[$i][sell_ac_price];
			
			$sum_total_ap_price += $total_ap_price;
			$sum_p_ap_price += $data_array[$i][p_ap_price];
			$sum_d_ap_price += $data_array[$i][d_ap_price];
			$sum_ap_profit_price += $total_ap_profit_price;
			$sum_sell_ap_price += $data_array[$i][sell_ap_price];
		}
	}
	

	$Contents =str_replace("{sum_total_aa_price}",number_format($sum_total_aa_price),$Contents);
	$Contents =str_replace("{sum_p_aa_price}",number_format($sum_p_aa_price),$Contents);
	$Contents =str_replace("{sum_d_aa_price}",number_format($sum_d_aa_price),$Contents);
	$Contents =str_replace("{sum_total_ar_price}",number_format($sum_total_ar_price),$Contents);
	$Contents =str_replace("{sum_p_ar_price}",number_format($sum_p_ar_price),$Contents);
	$Contents =str_replace("{sum_d_ar_price}",number_format($sum_d_ar_price),$Contents);
	$Contents =str_replace("{sum_total_ac_price}",number_format($sum_total_ac_price),$Contents);
	$Contents =str_replace("{sum_p_ac_price}",number_format($sum_p_ac_price),$Contents);
	$Contents =str_replace("{sum_d_ac_price}",number_format($sum_d_ac_price),$Contents);
	$Contents =str_replace("{sum_total_ap_price}",number_format($sum_total_ap_price),$Contents);
	$Contents =str_replace("{sum_p_ap_price}",number_format($sum_p_ap_price),$Contents);
	$Contents =str_replace("{sum_d_ap_price}",number_format($sum_d_ap_price),$Contents);
	
	$Contents =str_replace("{sum_total_price}",number_format($sum_total_price),$Contents);
	$Contents =str_replace("{sum_p_total_price}",number_format($sum_p_total_price),$Contents);
	$Contents =str_replace("{sum_d_total_price}",number_format($sum_d_total_price),$Contents);
	$Contents =str_replace("{sum_total_profit_price}",number_format($sum_total_profit_price),$Contents);
	$Contents =str_replace("{sum_total_profit_rate}",($sum_total_sell_price ? number_format(round($sum_total_profit_price/$sum_total_sell_price*100),2) : 0),$Contents);
	
	$Contents =str_replace("{sum_ac_profit_price}",number_format($sum_ac_profit_price),$Contents);
	$Contents =str_replace("{sum_ac_profit_rate}",($sum_sell_ac_price ? number_format(round($sum_ac_profit_price/$sum_sell_ac_price*100),2) : 0),$Contents);
	$Contents =str_replace("{sum_ap_profit_price}",number_format($sum_ap_profit_price),$Contents);
	$Contents =str_replace("{sum_ap_profit_rate}",($sum_sell_ap_price ? number_format(round($sum_ap_profit_price/$sum_sell_ap_price*100),2) : 0),$Contents);

	$Contents = $Contents."
				</tbody>
				</table>";

$Script = "
<script language='javascript' >

</script>";

$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = seller_accounts_menu();
$P->Navigation = $page_navigation;
$P->title = $page_title;
$P->strContents = $Contents;
echo $P->PrintLayOut();

?>