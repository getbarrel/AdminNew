<?
include("../class/layout.class");
//include("./pie.graph.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");



$db = new Database;
$mdb = new Database;
$sms_design = new SMS;


$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr>
		    <td align='left' colspan=6 > ".GetTitleNavigation("매출요약", "매출관리 > 매출요약 > 주문상태별 요약 ")."</td>
	  </tr>
	  <tr height=40>
		<td >
			<div class='tab'>
						<table class='s_org_tab'>
						<tr>
							<td class='tab'>";
if(!$selected_month){
	$selected_month = date("Ym",time());
}
for($i=-4;$i < 6;$i++){
	$display_month = date("Ym",mktime(0,0,0,substr($selected_month,4,2)+$i,substr($selected_month,6,2),substr($selected_month,0,4)));
	$display_month2 = date("Y.m",mktime(0,0,0,substr($selected_month,4,2)+$i,substr($selected_month,6,2),substr($selected_month,0,4)));
	$Contents01 .= "
								<table id='tab_01'  ".($display_month == $selected_month ? "class=on":"").">
								<tr>
									<th class='box_01'></th>
									<td class='box_02' onclick=\"document.location.href='?selected_month=".$display_month."'\">".$display_month2."</td>
									<th class='box_03'></th>
								</tr>
								</table>
								";
}

$Contents01 .= "
							</td>
							<td class='btn' style='padding:10px 0px 0px 10px;'>

							</td>
						</tr>
						</table>
					</div>
		</td>
	  </tr>
	  <!--tr height=30><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>주문상태별 요약</b></td></tr-->
	  <tr>
	  	<td style='padding:5px 0px 0px 0px'>
	  		".PrintOrderHistory($selected_month)."
	  	</td>
	  </tr>

	  <tr height=50><td colspan=5 class=small><!--* 해당 통계는 주문상세내용을 기준으로 산정되며 매출액은 주문취소금액 과 입금예정 내역은 제외됩니다.-->
	   ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</td></tr>


	  <tr height=50><td colspan=5></td></tr>
	</table>";



$Contents = $Contents01;






$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = order_menu();
$P->strContents = $Contents;
$P->Navigation = "매출관리 > 주문상태별 요약";
$P->title = "주문상태별 요약";
echo $P->PrintLayOut();




function PrintOrderHistory($vdate){
	global $admininfo,$sns_product_type;
	$odb = new Database;

	$vdate = $vdate."01";//date("Ymd", time());
	$today = date("Ymd", time());
	$firstday = date("Ymd", time()-84600*date("w"));
	$lastday = date("Ymd", time()+84600*(6-date("w")));


	if($admininfo[admin_level] == 9){
		if($admininfo[mem_type] == "MD"){
			$addWhere = " and od.company_id in (".getMySellerList($admininfo[charger_ix]).") ";
		}

		$sql = "
					Select date_format(od.regdate,'%Y%m%d') as vdate ,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER_DETAIL." od
					where date_format(od.regdate,'%Y%m') =  '".date("Ym",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)))."'
					AND od.product_type NOT IN (".implode(',',$sns_product_type).") $addWhere
					group by date_format(od.regdate,'%Y%m%d') ";

		//echo nl2br($sql);
		//exit;

	//echo $sql;
	}else if($admininfo[admin_level] == 8){
		$sql = "
					Select date_format(od.regdate,'%Y%m%d') as vdate ,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."','".ORDER_STATUS_ACCOUNT_READY."','".ORDER_STATUS_ACCOUNT_COMPLETE."') then ptprice else 0 end),0) as total_price,
					IFNULL(sum(case when status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."') then 1 else 0 end),0) as total_order_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_READY."'  then 1 else 0 end),0) as incom_ready_cnt,
					IFNULL(sum(case when status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else 0 end),0) as incom_end_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."') then 1 else 0 end),0) as delivery_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else 0 end),0) as return_total_cnt,
					IFNULL(sum(case when status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."') then 1 else 0 end),0) as cancel_total_price
				 	from ".TBL_SHOP_ORDER_DETAIL." od
					where date_format(od.regdate,'%Y%m') =  '".date("Ym",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)))."'
					AND od.product_type NOT IN (".implode(',',$sns_product_type).") and od.company_id = '".$admininfo[company_id]."'  $addWhere
					group by date_format(od.regdate,'%Y%m%d') ";
	/*
		$sql = "Select
					sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(regdate,'%Y%m%d') =  '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)))."' then od.ptprice else '0' end) as today_total_price,
					sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(regdate,'%Y%m%d') between '".$firstday."' and $lastday then od.ptprice else '0' end) as thisweek_total_price,
					sum(case when od.status in ('".ORDER_STATUS_INCOM_READY."','".ORDER_STATUS_INCOM_COMPLETE."','".ORDER_STATUS_DELIVERY_READY."','".ORDER_STATUS_DELIVERY_ING."','".ORDER_STATUS_DELIVERY_COMPLETE."')  and date_format(date,'%Y%m') = '".substr($vdate,0,6)."'  then od.ptprice else '0' end) as thismonth_total_price,
					sum(case when od.status in ('".ORDER_STATUS_CANCEL_APPLY."','".ORDER_STATUS_CANCEL_COMPLETE."')  and date_format(date,'%Y%m') = '".substr($vdate,0,6)."'  then total_price else '0' end) as thismonth_cancel_total_price,
					sum(case when od.status = '".ORDER_STATUS_INCOM_READY."'  then 1 else '0' end) as ready_cnt,
					sum(case when od.status = '".ORDER_STATUS_INCOM_COMPLETE."'  then 1 else '0' end) as order_end_cnt,
					sum(case when od.status in ('".ORDER_STATUS_RETURN_APPLY."','".ORDER_STATUS_RETURN_COMPLETE."')  then 1 else '0' end) as thismonth_return_total_cnt
				 	FROM ".TBL_SHOP_ORDER_DETAIL." o, ".TBL_SHOP_PRODUCT." p, ".TBL_SHOP_ORDER_DETAIL." od
				 	where o.oid = od.oid and od.pid = p.id and p.admin = '".$admininfo[company_id]."' ";
	*/
		//echo $sql;
	}




	$odb->query($sql);
	$datas = $odb->fetchall();//$odb->getrows();

	$datas_title[0] = "기간";
	$datas_title[1] = "매출액(원)";
	$datas_title[2] = "주문건수(건)";
	$datas_title[3] = "입금예정(건)";
	$datas_title[4] = "입금확인(건)";
	$datas_title[5] = "배송준비/배송중(건)";
	$datas_title[6] = "교환(건)";
	$datas_title[7] = "주문취소(건)";

	$mstring = "
	<table border='0' cellspacing='1' cellpadding='0' width='100%'>
      <tr>
        <td bgcolor='#F8F9FA'>
			<table cellpadding=0 cellspacing=1 width=100% bgcolor=#c0c0c0 class='list_table_box'>
				<col width='*' />
				<col width='13%' />
				<col width='12%' />
				<col width='12%' />
				<col width='12%' />
				<col width='15%' />
				<col width='12%' />
				<col width='12%' />
		";
	if($selected_date == ""){
		$selected_date = $vdate;
		$selected_date = $selected_date."01";
	}
	//echo $selected_date."<br>";
	$nLoop = date("t", mktime(0, 0, 0, substr($selected_date,4,2), substr($selected_date,6,2), substr($selected_date,0,4)));
		//print_r($datas);
	for($i=0, $j;$j<$nLoop;$j++){

			if($i == 0){
				$mstring .= "<tr bgcolor=#ffffff align=center height=30><td class='s_td'>".$datas_title[0]."</td><td class='m_td'>".$datas_title[1]."</td><td class='m_td'>".$datas_title[2]."</td><td class='m_td'>".$datas_title[3]."</td><td class='m_td'>".$datas_title[4]."</td><td class='m_td'>".$datas_title[5]."</td><td class='m_td'>".$datas_title[6]."</td><td class='e_td'>".$datas_title[7]."</td></tr>";
				$i++;
			}else{
				$display_date = date("Ymd",mktime(0,0,0,substr($selected_date,4,2),substr($selected_date,6,2),substr($selected_date,0,4))+60*60*24*($j-1));
				$display_date2 = date("Y-m-d",mktime(0,0,0,substr($selected_date,4,2),substr($selected_date,6,2),substr($selected_date,0,4))+60*60*24*($j-1));
				//echo $datas[$i][0]."==".date("Ymd",mktime(0,0,0,substr($selected_date,4,2),substr($selected_date,6,2),substr($selected_date,0,4))+60*60*24*($j-1))."<br>";
				if($datas[$i][0] == date("Ymd",mktime(0,0,0,substr($selected_date,4,2),substr($selected_date,6,2),substr($selected_date,0,4))+60*60*24*($j-1))){
						$mstring .= "<tr bgcolor=#ffffff height=25 align=right>
						<td class='list_box_td list_bg_gray blk' style='padding:0px 10px 0 10px;text-align:center;' ><b>".$display_date2."</b></td>
						<td class='list_box_td point' style='padding:0px 20px 0 10px;text-align:right;' >".number_format($datas[$i][total_price])."</td>
						<td class='list_box_td list_bg_gray'>".number_format($datas[$i][total_order_cnt])."</td>
						<td class='list_box_td point'>".number_format($datas[$i][incom_ready_cnt])."</td>
						<td class='list_box_td list_bg_gray'>".number_format($datas[$i][incom_end_cnt])."</td>
						<td class='list_box_td'>".number_format($datas[$i][delivery_cnt])."</td>
						<td class='list_box_td list_bg_gray'>".number_format($datas[$i][return_total_cnt])."</td>
						<td class='list_box_td'>".number_format($datas[$i][cancel_total_price])."</td>
						</tr>";
						$sales_sum += $datas[$i][total_price];
						$order_cnt_sum += $datas[$i][total_order_cnt];
						$incom_ready_sum += $datas[$i][incom_ready_cnt];
						$incom_complete_sum += $datas[$i][incom_end_cnt];
						$delivery_sum += $datas[$i][delivery_cnt];
						$exchange_sum += $datas[$i][return_total_cnt];
						$cancel_sum += $datas[$i][cancel_total_price];
						$i++;

				}else{
					$mstring .= "<tr bgcolor=#ffffff height=25 align=right>
						<td class='list_box_td list_bg_gray blk' style='padding:0px 10px 0 10px;text-align:center;' ><b>".$display_date2."</b></td>
						<td class='list_box_td point' style='padding:0px 20px 0 10px;text-align:right;' >0</td>
						<td class='list_box_td list_bg_gray'>0</td>
						<td class='list_box_td point'>0</td>
						<td class='list_box_td list_bg_gray'>0</td>
						<td class='list_box_td'>0</td>
						<td class='list_box_td list_bg_gray'>0</td>
						<td class='list_box_td'>0</td>
						</tr>";
					//$i++;
				}
			}
	}
	$mstring .= "<tr bgcolor=#ffffff height=25 align=right>
				<td class='list_box_td list_bg_gray blk' style='padding:0px 0 0 10px;text-align:center;' ><b>합계</b></td>
				<td class='list_box_td point blk' style='padding:0px 20px 0 10px;text-align:right;' ><b>".number_format($sales_sum)."</b></td>
				<td class='list_box_td list_bg_gray blk'><b>".number_format($order_cnt_sum)."</b></td>
				<td class='list_box_td point blk'><b>".number_format($incom_ready_sum)."</b></td>
				<td class='list_box_td list_bg_gray blk'><b>".number_format($incom_complete_sum)."</b></td>
				<td class='list_box_td blk'><b>".number_format($delivery_sum)."</b></td>
				<td class='list_box_td list_bg_gray blk'><b>".number_format($exchange_sum)."</b></td>
				<td class='list_box_td blk'><b>".number_format($cancel_sum)."</b></td>
				</tr>";

	$mstring .= "</table>";
	$mstring .= "</td></tr></table>";

	return $mstring;


}


?>
