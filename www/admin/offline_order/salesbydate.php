<?
include("../class/layout.class");
//include("./pie.graph.php");
include($_SERVER["DOCUMENT_ROOT"]."/class/sms.class");
//include("../lib/report.lib.php");
include("../logstory/include/commerce.lib.php");
include("../logstory/include/util.php");


$Contents01 = "
	<table width='100%' cellpadding=0 cellspacing=0 border='0' align='left'>
	  <tr>
		    <td align='left' colspan=6 > ".GetTitleNavigation("영업매출통계", "영업관리 > 영업매출통계 > 일별매출액(종합)")."</td>
	  </tr>";
if(false){
$Contents01 .= "
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
	  </tr>";
}

if($groupbytype==""){
	$groupbytype="day";
}

$Contents01 .= "
	  <!--tr height=30><td style='border-bottom:2px solid #efefef'><img src='../images/dot_org.gif' align=absmiddle> <b>주문상태별 요약</b></td></tr-->
	  <tr>
	  	<td style='padding:5px 0px 0px 0px'>
	  		".salesByDateReportTable($vdate,$groupbytype,$SelectReport)."
	  	</td>
	  </tr>

	  <tr height=50><td colspan=5 class=small><!--* 해당 통계는 주문상세내용을 기준으로 산정되며 매출액은 주문취소금액 과 입금예정 내역은 제외됩니다.-->
	   ".getTransDiscription(md5($_SERVER["PHP_SELF"]),'A')."</td></tr>


	  <tr height=50><td colspan=5></td></tr>
	</table>";



$Contents = $Contents01;


$P = new LayOut();
$P->addScript = $Script;
$P->strLeftMenu = offline_order_menu();
$P->strContents = $Contents;
if($groupbytype=="day"){
	$P->Navigation = "영업매출통계 > 일별매출액(종합)";
	$P->title = "일별매출액(종합)";
}elseif($groupbytype=="month"){
	$P->Navigation = "영업매출통계 > 월별매출액(종합)";
	$P->title = "월별매출액(종합)";
}elseif($groupbytype=="term"){
	$P->Navigation = "영업매출통계 > 기간별매출액(종합)";
	$P->title = "기간별매출액(종합)";
}
echo $P->PrintLayOut();


function salesByDateReportTable($vdate,$groupbytype="day",$SelectReport=1){
	global $depth,$referer_id, $non_sale_status, $order_status, $cancel_status, $return_status, $all_sale_status;
	global $search_sdate, $search_edate,$sdate,$edate;

	$nview_cnt = 0;
	$cid = $referer_id;
	if($SelectReport == ""){
		$SelectReport = 1;
	}

	$fordb = new Database();
	$db = new Database();

	if($depth == ""){
		$depth = 0;
	}else{
		$depth = $depth+1;
	}
	
	if(empty($sdate)){
		$sdate = date("Ymd", time()-84600*7);
		$edate = date("Ymd", time());
	}

	if($vdate == ""){
		$vdate = date("Ymd", time());
		$vyesterday = date("Ymd", time()-84600);
		$voneweekago = date("Ymd", time()-84600*7);
	}else{
		if($SelectReport ==3){
			$vdate = $vdate."01";
		}
		$vweekenddate = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6);
		$vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
		$voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
	}

	$where =" and od.order_from in ('offline')";

	if($groupbytype=="day"){
		$select=" date_format(od.regdate,'%Y%m%d') as vdate ,";
		//$where .=" and date_format(od.regdate,'%Y%m%d') LIKE '".substr($vdate,0,6)."%'";
		$where .=" and od.regdate between '".substr($vdate,0,4)."-".substr($vdate,4,2)."-01 00:00:00' and '".substr($vdate,0,4)."-".substr($vdate,4,2)."-31 23:59:59' ";
		$group_by = "group by date_format(od.regdate,'%Y%m%d')";
		$sales_plan_select ="plan_date ,";
		$sales_plan_where ="where plan_date LIKE '".substr($vdate,0,6)."%'";
		$sales_plan_group_by ="group by plan_date ";
	}elseif($groupbytype=="month"){
		$select=" date_format(od.regdate,'%Y%m') as vdate ,";
		//$where .=" and date_format(od.regdate,'%Y%m%d') LIKE '".substr($vdate,0,4)."%'";
		$where .=" and od.regdate between '".substr($vdate,0,4)."-".substr($vdate,4,2)."-01 00:00:00' and '".substr($vdate,0,4)."-".substr($vdate,4,2)."-31 23:59:59' ";
		$group_by = "group by date_format(od.regdate,'%Y%m')";
		$sales_plan_select ="substr(plan_date,1,6) as plan_date ,";
		$sales_plan_where ="where plan_date LIKE '".substr($vdate,0,4)."%'";
		$sales_plan_group_by ="group by substr(plan_date,1,6) ";
	}elseif($groupbytype=="term"){
		$select=" date_format(od.regdate,'%Y%m%d') as vdate ,";
		//$where .=" and date_format(od.regdate,'%Y%m%d') between '".$sdate."' and '".$edate."' ";
		$where .=" and od.regdate between '".substr($sdate,0,4)."-".substr($sdate,4,2)."-".substr($sdate,6,2)." 00:00:00' and '".substr($edate,0,4)."-".substr($edate,4,2)."-".substr($edate,6,2)." 23:59:59' ";
		$group_by = "group by date_format(od.regdate,'%Y%m%d')";
		$sales_plan_select ="plan_date ,";
		$sales_plan_where ="where plan_date between '".$sdate."' and '".$edate."'";
		$sales_plan_group_by ="group by plan_date";
	}

	$db->query("select $sales_plan_select sum(plan_price) as plan_price from shop_member_sales_plan $sales_plan_where $sales_plan_group_by ");
	$sales_plan = $db->fetchall();

	$sql = "select data.vdate, sum(data.order_sale_cnt) as order_sale_cnt, sum(order_sale_sum) as order_sale_sum, sum(order_coprice_sum) as order_coprice_sum, 
				sum(sale_all_cnt) as sale_all_cnt, sum(sale_all_sum) as sale_all_sum, sum(coprice_all_sum) as coprice_all_sum, 
				sum(cancel_sale_cnt) as cancel_sale_cnt, sum(cancel_sale_sum) as cancel_sale_sum, sum(cancel_coprice_sum) as cancel_coprice_sum, 
				sum(return_sale_cnt) as return_sale_cnt, sum(return_sale_sum) as return_sale_sum, sum(return_coprice_sum) as return_coprice_sum, 
				sum(whole_delivery_cnt) as whole_delivery_cnt, sum(whole_delivery_sum) as whole_delivery_sum, sum(return_delivery_cnt) as return_delivery_cnt, sum(return_delivery_sum) as return_delivery_sum
				from (
				Select $select
				sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pcnt else 0 end) as order_sale_cnt,
				sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then (od.pt_dcprice)  else 0 end) as order_sale_sum,
				sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.coprice*od.pcnt else 0 end) as order_coprice_sum,

				sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.pcnt else 0 end) as sale_all_cnt,
				sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then (od.pt_dcprice)  else 0 end) as sale_all_sum,
				sum(case when od.status not in ('".implode("','",$all_sale_status)."')  then od.coprice*od.pcnt else 0 end) as coprice_all_sum,

				sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end) as cancel_sale_cnt,
				sum(case when od.status IN ('".implode("','",$cancel_status)."')  then (od.pt_dcprice)  else 0 end) as cancel_sale_sum,
				sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.coprice*od.pcnt else 0 end) as cancel_coprice_sum,

				sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end) as return_sale_cnt,
				sum(case when od.status IN ('".implode("','",$return_status)."')  then (od.pt_dcprice)  else 0 end) as return_sale_sum,
				sum(case when od.status IN ('".implode("','",$return_status)."')  then od.coprice*od.pcnt else 0 end) as return_coprice_sum, 
				0 as whole_delivery_cnt,
				0 as whole_delivery_sum,
				0 as return_delivery_cnt,
				0 as return_delivery_sum
				from  shop_order_detail od

				where od.status NOT IN ('".implode("','",$non_sale_status)."') 
				$where
				$group_by
				";

			$sql .= "
				union 
				select $select
				0 as order_sale_cnt,
				0 as order_sale_sum,
				0 as order_coprice_sum,

				0 as sale_all_cnt,
				0 as sale_all_sum,
				0 as coprice_all_sum,

				0 as cancel_sale_cnt,
				0 as cancel_sale_sum,
				0 as cancel_coprice_sum,

				0 as return_sale_cnt,
				0 as return_sale_sum,
				0 as return_coprice_sum, 
				sum(case when oph.price_div = 'D' and payment_status = 'G'  then 1 else 0 end) as whole_delivery_cnt,
				sum(case when oph.price_div = 'D' and payment_status = 'G'  then oph.expect_price else 0 end) as whole_delivery_sum,
				sum(case when oph.price_div = 'D' and payment_status = 'F'  then 1 else 0 end) as return_delivery_cnt,
				sum(case when oph.price_div = 'D' and payment_status = 'F'  then oph.expect_price else 0 end) as return_delivery_sum
				from 
				shop_order o, shop_order_detail od , shop_order_price_history oph

				where  o.oid = od.oid and od.oid = oph.oid
				$where
				$group_by

				";

				$sql .= ") data
				group by vdate ";

				//							left join ".TBL_COMMERCE_VIEWINGVIEW." b on od.pid = b.pid
				//AND od.status NOT IN ('".implode("','",$non_sale_status)."')
				//and substr(c.cid,1,".(($depth+1)*3).") = substr(b.cid,1,3)


		$dateString = "월간 : ".getNameOfWeekday(0,$vdate,"monthname");

	//echo nl2br($sql);
	if($sql){
		$fordb->query($sql);
	}

	if($groupbytype=="day"){
		$mstring = "<table width='100%' border=0>
						<tr><td>".TitleBar("상품군별 분석 : ".($cid ? getCategoryPath($cid,4):""),$dateString)."</td><td align=right>(단위:원) </td></tr>
					</table>";
	}elseif($groupbytype=="month"){
		$mstring = "<table width='100%' border=0>
						<tr><td>".TitleBarYear("","")."</td><td align=right>(단위:원) </td></tr>
					</table>";
	}elseif($groupbytype=="term"){
		$mstring = "<table width='100%' border=0>
						<tr><td>".TitleBarterm("","")."</td></tr>
					</table>";
	}

	$mstring .= "<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>";

		$mstring .= "
						<col width='3%'>
						<col width='*'>";

		$mstring .= "
						<col width='3%'>
						<col width='6%'>
						<col width='3%'>
						<col width='6%'>
						<col width='3%'>
						<col width='6%'>
						<col width='3%'>
						<col width='6%'>
						<col width='3%'>
						<col width='6%'>
						<col width='6%'>
						<col width='3%'>
						<col width='6%'>
						<col width='3%'>
						<col width='6%'>
						<col width='3%'>
						<col width='6%'>
						<col width='3%'>
						<col width='6%'>
						<col width='6%'>";
	$mstring .= "
		<tr height=30>";

			$mstring .= "
			<td class=s_td rowspan=3>순</td>
			<td class=m_td rowspan=3>날짜</td>
			<td class=m_td rowspan=2 colspan=2>매출성과율</td>";

			$mstring .= "
			<td class=m_td colspan=2>주문매출</td>
			<td class=m_td colspan=8>매출</td>
			<td class=m_td rowspan=3>실매출액<br>원가</td>
			<td class=m_td colspan=2 rowspan=2>수익</td>
			<td class=m_td colspan=6>배송비</td>
			<td class=m_td rowspan=3 >매출액<br>(상품+배송비)</td>
		</tr>
		<tr height=30>
			<td class='m_td small' colspan=2 style='line-height:140%;'><b>전체주문</b><br>(입금예정포함)</td>
			<td class=m_td colspan=2>전체매출액(전체)</td>
			<td class=m_td colspan=2>취소매출액(-)</td>
			<td class=m_td colspan=2>반품매출액(-)</td>
			<td class=m_td colspan=2>실매출액(+)</td>
			<td class=m_td colspan=2>전체매출액</td>
			<td class=m_td colspan=2>환불매출액</td>
			<td class=m_td colspan=2>실매출액</td>
		</tr>
		<tr height=30 align=center>
			<td class=m_td nowrap>목표매출</td>
			<td class=m_td >성과율(%)</td>
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td >마진(원)</td>
			<td class=m_td >마진율(%)</td>
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			<td class=m_td nowrap>수량(개)</td>
			<td class=m_td >주문액(원)</td>
			</tr>\n";
			/*

			sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pcnt else 0 end) as order_sale_cnt,
							sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then (od.pt_dcprice)  else 0 end) as order_sale_sum,
							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end) as cancel_sale_cnt,
							sum(case when od.status IN ('".implode("','",$cancel_status)."')  then (od.pt_dcprice)  else 0 end) as cancel_sale_sum,
							sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end) as return_sale_cnt,
							sum(case when od.status IN ('".implode("','",$return_status)."')  then (od.pt_dcprice)  else 0 end) as return_sale_sum,

							*/

	for($i=0;$i<$fordb->total;$i++){
		$fordb->fetch($i);

		$real_sale_cnt = $fordb->dt[sale_all_cnt]-$fordb->dt[cancel_sale_cnt]-$fordb->dt[return_sale_cnt];
		$real_sale_sum = $fordb->dt[sale_all_sum]-$fordb->dt[cancel_sale_sum]-$fordb->dt[return_sale_sum];
		$sale_coprice = $fordb->dt[coprice_all_sum]-$fordb->dt[cancel_coprice_sum]-$fordb->dt[return_coprice_sum];
		$margin = $real_sale_sum - $sale_coprice;
		if($real_sale_sum > 0){
			$margin_rate = $margin/$real_sale_sum*100;
		}else{
			$margin_rate = 0;
		}

		$real_sale_sum_with_deliveryprice = $real_sale_sum +  $fordb->dt[whole_delivery_sum]-$fordb->dt[return_delivery_sum];

		//if($groupbytype=="day"){

			$week_num = date("w",mktime(0,0,0,substr($fordb->dt[vdate],4,2),substr($fordb->dt[vdate],6,2),substr($fordb->dt[vdate],0,4)));

			$mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i'>
			<td class='list_box_td list_bg_gray' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($i+1)."</td>";
			
			if($groupbytype=="day"||$groupbytype=="term"){
				$mstring .= "<td class='list_box_td point' style='text-align:left;' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".getNameOfWeekday($week_num, $fordb->dt[vdate],"priodname")." </td>";
			}elseif($groupbytype=="month"){
				$mstring .= "<td class='list_box_td point' style='text-align:left;' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".substr($fordb->dt[vdate],0,4)."년 ".substr($fordb->dt[vdate],4,2)."월</td>";
			}

			$sales_plan_price = 0;
			for($j=0;$j<count($sales_plan);$j++){
				if($sales_plan[$j][plan_date]==$fordb->dt[vdate]){
					//echo count($sales_plan)."<br/>";
					$sales_plan_price = $sales_plan[$j][plan_price];
					//unset($sales_plan[$j]);
					break;
				}
			}

			if($sales_plan_price){
				$sales_plan_rate=$real_sale_sum_with_deliveryprice/$sales_plan_price*100;
			}else{
				$sales_plan_rate=0;
			}

			$mstring .= "
			<td class='list_box_td number blue_point str' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($sales_plan_price,0)."</td>
			<td class='list_box_td number blue_point str' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($sales_plan_rate,0)."%</td>

			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[order_sale_cnt],0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[order_sale_sum],0)."&nbsp;</td>

			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[sale_all_cnt],0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[sale_all_sum],0)."&nbsp;</td>

			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[cancel_sale_cnt],0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[cancel_sale_sum],0)."&nbsp;</td>

			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[return_sale_cnt],0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[return_sale_sum],0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($real_sale_cnt,0)."&nbsp;</td>
			<td class='list_box_td number point' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($real_sale_sum,0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($sale_coprice,0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($margin,0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($margin_rate,0)." %&nbsp;</td>
			
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[whole_delivery_cnt],0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[whole_delivery_sum],0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[return_delivery_cnt],0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[return_delivery_sum],0)."&nbsp;</td>
			<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[whole_delivery_cnt]-$fordb->dt[return_delivery_cnt],0)."&nbsp;</td>
			<td class='list_box_td number point' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[whole_delivery_sum]-$fordb->dt[return_delivery_sum],0)."&nbsp;</td>";

			
			$mstring .= "
			<td class='list_box_td number blue_point str' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($real_sale_sum_with_deliveryprice,0)."</td>";

			$mstring .= "
			</tr>\n";
		//}
		
		$order_sale_cnt = $order_sale_cnt + returnZeroValue($fordb->dt[order_sale_cnt]);
		$order_sale_sum = $order_sale_sum + returnZeroValue($fordb->dt[order_sale_sum]);

		$sale_all_cnt = $sale_all_cnt + returnZeroValue($fordb->dt[sale_all_cnt]);
		$sale_all_sum = $sale_all_sum + returnZeroValue($fordb->dt[sale_all_sum]);

		$cancel_sale_cnt = $cancel_sale_cnt + returnZeroValue($fordb->dt[cancel_sale_cnt]);
		$cancel_sale_sum = $cancel_sale_sum + returnZeroValue($fordb->dt[cancel_sale_sum]);

		$return_sale_cnt = $return_sale_cnt + returnZeroValue($fordb->dt[return_sale_cnt]);
		$return_sale_sum = $return_sale_sum + returnZeroValue($fordb->dt[return_sale_sum]);

		$real_sale_cnt_sum = $real_sale_cnt_sum + returnZeroValue($real_sale_cnt);
		$real_sale_sum_sum = $real_sale_sum_sum + returnZeroValue($real_sale_sum);
		$sale_coprice_sum = $sale_coprice_sum + returnZeroValue($sale_coprice);
		$margin_sum = $margin_sum + returnZeroValue($margin);

		$whole_delivery_cnt += returnZeroValue($fordb->dt[whole_delivery_cnt]);
		$whole_delivery_sum += returnZeroValue($fordb->dt[whole_delivery_sum]);
		$return_delivery_cnt += returnZeroValue($fordb->dt[return_delivery_cnt]);
		$return_delivery_sum += returnZeroValue($fordb->dt[return_delivery_sum]);

		$real_delivery_cnt += returnZeroValue($fordb->dt[whole_delivery_cnt]-$fordb->dt[return_delivery_cnt]);
		$real_delivery_sum += returnZeroValue($fordb->dt[whole_delivery_sum]-$fordb->dt[return_delivery_sum]);

		$real_sale_sum_with_deliveryprice_sum += $real_sale_sum_with_deliveryprice;

	}

	if ($sale_all_sum == 0){
		if($groupbytype=="day"){
			$mstring .= "<tr  align=center height=200><td colspan=24 class='list_box_td'  >결과값이 없습니다.</td></tr>\n";
		}else{
			$mstring .= "<tr  align=center height=200><td colspan=24 class='list_box_td'  >결과값이 없습니다.</td></tr>\n";
		}
		
	}
	
	if($real_sale_sum_sum > 0){
		$margin_sum_rate = $margin_sum/$real_sale_sum_sum*100;
	}else{
		$margin_sum_rate = 0;
	}
	
	$sales_plan_price_sum=0;
	for($j=0;$j<count($sales_plan);$j++){
		$sales_plan_price_sum += $sales_plan[$j][plan_price];
	}

	if($sales_plan_price_sum){
		$sales_plan_rate_sum=$real_sale_sum_with_deliveryprice_sum/$sales_plan_price_sum*100;
	}else{
		$sales_plan_rate_sum=0;
	}


		$mstring .= "<tr height=25 align=right>
		<td class=s_td align=center colspan=2>합계</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($sales_plan_price_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($sales_plan_rate_sum,0)."%</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($order_sale_cnt,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($order_sale_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($sale_all_cnt,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($sale_all_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($cancel_sale_cnt,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($cancel_sale_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($return_sale_cnt,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($return_sale_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_cnt_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_sum_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($sale_coprice_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($margin_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($margin_sum_rate,0)." %</td>

		<td class='e_td number' style='padding-right:10px;'>".number_format($whole_delivery_cnt,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($whole_delivery_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($return_delivery_cnt,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($return_delivery_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($real_delivery_cnt,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($real_delivery_sum,0)."</td>
		<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_sum_with_deliveryprice_sum,0)."</td>
		</tr>\n";

	$mstring .= "</table>\n";

	if($groupbytype=="day"){
		$mstring .= "<table width='100%'><tr><td> </td><td align=right style='padding-top:10px;'>VAT 포함</td></tr></table>";

		/*
		$help_text = "
		<table>
			<tr>
				<td style='line-height:150%'>
				- 카테고리별 상품조회 회수를 바탕으로 귀사 사이트의 인기카테고리와 비인기 카테고리를 정확히 파악하여 그에 맞는 운영및 마케팅 정책을 수립 수행할수 있습니다<br>
				- 좌측 카테고리를 클릭하면 하부 카테고리에 대한 상세 정보가 표시 됩니다<br><br>
				</td>
			</tr>
		</table>
		";*/

		$help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'B' );


		$mstring .= HelpBox("일별매출(종합)", $help_text);
	}
	return $mstring;
}
?>
