<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");
include("../report/etcreferer.chart.php");
include("../include/commerce.lib.php");

function ReportTable($vdate,$SelectReport=1){
    global $search_sdate, $search_edate;
    global $non_sale_status;

    $pageview01 = 0;
    $sumprice = 0;
    $sumprice_web = 0;
    $sumprice_mobile = 0;
    $sumucnt = 0;
    $sumucnt_web = 0;
    $sumucnt_mobile = 0;
    $sumcnt = 0;
    $sumcnt_web = 0;
    $sumcnt_mobile = 0;

    $fordb = new forbizDatabase();
    $fordb2 = new forbizDatabase();
    $fordb3 = new forbizDatabase();


    if($SelectReport == ""){
        $SelectReport = 1;
    }
    if($vdate == ""){
        $selected_date = date("Y-m-d", time());
        $vdate = date("Ymd", time());
        $vyesterday = date("Ymd", time()-84600);
        $voneweekago = date("Ymd", time()-84600*7);
        $vtwoweekago = date("Ymd", time()-84600*14);
        $vfourweekago = date("Ymd", time()-84600*28);
        $vonemonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)));
        $vtwomonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)));
        $selected_date = date("Y-m-d", time());
    }else{
        if($SelectReport ==3 && strlen($vdate) == 6){
            $vdate = $vdate."01";
        }
        $selected_date = date("Y-m-d", mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)));
        $vweekenddate = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6);
        $vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
        $voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
        $vtwoweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*14);
        $vfourweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
        $vonemonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)));
        $vtwomonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)));
        $selected_date = date("Y-m-d", mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)));
    }

    if($SelectReport == 1){
        /*
        $sql = "SELECT t.vtime as vtime, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode) as ucnt
                FROM ".TBL_LOGSTORY_TIME." t left join ".TBL_COMMERCE_SALESTACK." c on t.vtime = c.vtime where vdate = '$vdate' and step1 = 1 and step6 = 1 group by t.vtime ";
        */
        $sql = "select date_format(od.regdate, '%H') as vtime, 
					sum(od.pt_dcprice) as sales, 
					sum(case when o.payment_agent_type = 'W' then od.pt_dcprice else 0 end) as web_sales,
					sum(case when o.payment_agent_type = 'M' then od.pt_dcprice else 0 end) as mobile_sales,
					sum(od.pcnt) as cnt , 
					sum(case when o.payment_agent_type = 'W' then od.pcnt else 0 end) as web_cnt,
					sum(case when o.payment_agent_type = 'M' then od.pcnt else 0 end) as mobile_cnt,
					count(distinct o.oid) as ucnt,
					count(distinct  case when o.payment_agent_type = 'W' then o.oid else 0 end) as web_ucnt,
					count(distinct case when o.payment_agent_type = 'M' then o.oid else 0 end) as mobile_ucnt
					from shop_order o , shop_order_detail od 
					where o.oid = od.oid and od.regdate  between '".$selected_date." 00:00:00' and '".$selected_date." 23:59:59'  
					and od.status NOT IN ('".implode("','",$non_sale_status)."') 
					group by vtime ";
        //echo nl2br($sql);
        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");

    }else if($SelectReport == 2 || $SelectReport == 4){
        if($SelectReport == "4"){
            $vdate = $search_sdate;
            $selected_date = date('Y-m-d',strtotime($search_sdate));
            $vweekenddate = $search_edate;
        }

        /*
        $sql = "SELECT t.vtime as vtime, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt FROM ".TBL_LOGSTORY_TIME." t left join ".TBL_COMMERCE_SALESTACK." c on t.vtime = c.vtime where vdate between '$vdate' and '$vweekenddate' and step1 = 1 and step6 = 1 group by t.vtime ";
        */
        $sql = "select date_format(od.regdate, '%H') as vtime, 
					sum(od.pt_dcprice) as sales, 
					sum(case when o.payment_agent_type = 'W' then od.pt_dcprice else 0 end) as web_sales,
					sum(case when o.payment_agent_type = 'M' then od.pt_dcprice else 0 end) as mobile_sales,
					sum(od.pcnt) as cnt , 
					sum(case when o.payment_agent_type = 'W' then od.pcnt else 0 end) as web_cnt,
					sum(case when o.payment_agent_type = 'M' then od.pcnt else 0 end) as mobile_cnt,
					count(distinct o.oid) as ucnt,
					count(distinct  case when o.payment_agent_type = 'W' then o.oid else 0 end) as web_ucnt,
					count(distinct case when o.payment_agent_type = 'M' then o.oid else 0 end) as mobile_ucnt
					from shop_order o , shop_order_detail od 
					where o.oid = od.oid 
					and od.regdate  between '".$selected_date." 00:00:00' and '".substr($vweekenddate, 0, 4)."-".substr($vweekenddate, 4, 2)."-".substr($vweekenddate, 6, 2)." 23:59:59' 
					and od.status NOT IN ('".implode("','",$non_sale_status)."') 
					group by vtime ";

        if($SelectReport == 2){
            $dateString = "주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
        }else if($SelectReport == 4){
            $s_week_num = date("w",mktime(0,0,0,substr($search_sdate,4,2),substr($search_sdate,6,2),substr($search_sdate,0,4)));
            $e_week_num = date("w",mktime(0,0,0,substr($search_edate,4,2),substr($search_edate,6,2),substr($search_edate,0,4)));
            $dateString = "기간별 : ".getNameOfWeekday($s_week_num,$search_sdate,"priodname")."~".getNameOfWeekday($e_week_num,$search_edate,"priodname");
        }

    }else if($SelectReport == 3){
        /*
        $sql = "SELECT t.vtime as vtime, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt FROM ".TBL_LOGSTORY_TIME." t left join ".TBL_COMMERCE_SALESTACK." c on t.vtime = c.vtime where vdate LIKE '".substr($vdate,0,6)."%' and step1 = 1 and step6 = 1 group by t.vtime ";
        */
        $sql = "select date_format(od.regdate, '%H') as vtime, 
					sum(od.pt_dcprice) as sales, 
					sum(case when o.payment_agent_type = 'W' then od.pt_dcprice else 0 end) as web_sales,
					sum(case when o.payment_agent_type = 'M' then od.pt_dcprice else 0 end) as mobile_sales,
					sum(od.pcnt) as cnt , 
					sum(case when o.payment_agent_type = 'W' then od.pcnt else 0 end) as web_cnt,
					sum(case when o.payment_agent_type = 'M' then od.pcnt else 0 end) as mobile_cnt,
					count(distinct o.oid) as ucnt,
					count(distinct  case when o.payment_agent_type = 'W' then o.oid else 0 end) as web_ucnt,
					count(distinct case when o.payment_agent_type = 'M' then o.oid else 0 end) as mobile_ucnt
					from shop_order o , shop_order_detail od 
					where o.oid = od.oid and od.regdate between '".substr($selected_date,0,7)."-01 00:00:00' and '".substr($selected_date,0,7)."-31 23:59:59'  
					and od.status NOT IN ('".implode("','",$non_sale_status)."') 
					group by vtime ";

        $dateString = getNameOfWeekday(0,$vdate,"monthname");

    }
    //echo $sql;
//	echo $sql."\n";


    $sql2 = "select 
                        DATE_FORMAT(op.regdate, '%H') as group_name ,
                        o.payment_agent_type,
                        sum((
                            select 
                                sum(case when ops.pay_type IN ('G')  then ops.payment_price else -ops.payment_price end)
                            from shop_order_payment ops where op.oid = ops.oid
                            and ops.method = '13'
                            group by ops.oid
                        )) sale_mileage_order
                     from
                        shop_order o, shop_order_payment op 
                      where
                        o.oid=op.oid
                      and
                        op.method = '13' 
                      and 
                        op.oid in (
                            select oid from  shop_order_detail od 
							where od.regdate";
if($SelectReport == 1){
    $sql2 .= "
							between '".$selected_date." 00:00:00' and '".$selected_date." 23:59:59'
                        ";
} else if($SelectReport == 2){
    $sql2 .= "
							between '".$selected_date." 00:00:00' and '".substr($vweekenddate, 0, 4)."-".substr($vweekenddate, 4, 2)."-".substr($vweekenddate, 6, 2)." 23:59:59' 
                        ";

}else if($SelectReport == 3){
    $sql2 .= "
							between '".substr($selected_date,0,7)."-01 00:00:00' and '".substr($selected_date,0,7)."-31 23:59:59'  
                        ";
}


                    $sql2 .= "
							and od.status NOT IN ('".implode("','",$non_sale_status)."') 
							and od.order_from = 'self' 
                            group by od.oid
                        ) group by group_name, payment_agent_type
            ";

    $fordb->query($sql2);
    $mileage_orders = $fordb->fetchall();
    $mileage_orders_array = array();
    if(is_array($mileage_orders)){
        foreach($mileage_orders as $key=>$val){
            $mileage_orders_array[(int)$val['group_name']][$val['payment_agent_type']] = $mileage_orders[$key];
        }
    }

    $fordb->query($sql);

    $mstring = $mstring.TitleBar("매출액(시간대)",$dateString);
//	$mstring .= "<table cellpadding=0 cellspacing=0 width=745 border=0 >\n";
//	$mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'>".etcrefererGraph($vdate,$SelectReport)."</td></tr>\n";
//	$mstring .= "<tr  align=center><td colspan=3 ></td></tr>\n";
//	$mstring .= "</table>";
    /*
        $mstring .= "<table cellpadding=3 cellspacing=0 width=745  >\n";
        $mstring .= "<tr height=25 align=center><td width=150 class=s_td width=30>항목</td><td class=m_td width=200>페이지 명</td><td class=e_td width=50>페이지뷰</td></tr>\n";
        $mstring .= "<tr height=25 bgcolor=#ffffff>
            <td bgcolor=#efefef align=center id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">최다 요청</td>
            <td align=right id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">오후 2:00:00 ∼ 오후 3:00:00</td>
            <td bgcolor=#efefef  align=right id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">543</td>
            </tr>\n";
        $mstring .= "<tr height=25 bgcolor=#ffffff>
            <td bgcolor=#efefef align=center id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">최소 요청</td>
            <td align=right id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">오후 2:00:00 ∼ 오후 3:00:00</td>
            <td bgcolor=#efefef  align=right id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">33</td>
            </tr>\n";
        $mstring .= "</table><br>";
    */
    $mstring .= "<table cellpadding=0 cellspacing=0 width=100% border=0 >\n";
    $mstring .= "<tr  align=center><td colspan=3 style='padding:10px 0px;'><div id='stacked-chart' style='width: 100%; height: 300px; padding: 0px; position: relative;' /></td></tr>\n";
    $mstring .= "</table><br>";

    $mstring .= "<table cellpadding=3 cellspacing=0 width=100% class='list_table_box'  >\n";
    $mstring .= "<tr height=30 align=center style='font-weight:bold'>
						<td class=s_td width=10% rowspan=2>시간</td>
						<td class=m_td width=30% colspan=3>매출액</td>
						<td class=m_td width=30% colspan=3>구매자수</td>
						<td class=e_td width=30% colspan=3>구매수량</td>
						</tr>
						<tr height=30 align=center style='font-weight:bold'>
							<td class=s_td >합계</td>
							<td class=m_td>웹</td>
							<td class=m_td nowrap>모바일</td>
							<td class=s_td >합계</td>
							<td class=m_td>웹</td>
							<td class=m_td nowrap>모바일</td>
							<td class=s_td >합계</td>
							<td class=m_td>웹</td>
							<td class=m_td nowrap>모바일</td>
						</tr>
						\n";

//	if($fordb->total == 0){
//		$mstring .= "<tr height=50 bgcolor=#ffffff align=center><td colspan=5>결과값이 없습니다.</td></tr>\n";
//	}else{



//	$fordb2->fetch(0);
//	$fordb3->fetch(0);
    $j = 0;
    $fordb->fetch($j);

    $labels = array("웹","모바일");
    $ykeys = array("a","b");

    for($i=0;$i <= 23;$i++){
        if(!empty($fordb->dt['vtime'])){
            $vtime = $fordb->dt['vtime'];
        }else{
            $vtime = "";
        }

        $sale_mileage_order_web = $mileage_orders_array[$i]['sale_mileage_order']['W'];
        $sale_mileage_order_mobile = $mileage_orders_array[$i]['sale_mileage_order']['M'];
        $sale_mileage_order = $sale_mileage_order_web + $sale_mileage_order_mobile;

        if($vtime == $i){
            //echo $vdate."<br>";
            $chart_data[] = array(
                'y' => date("Y-m-d H:00",strtotime($vdate)+(60*60*$i)),
                'a' => ($fordb->dt['web_sales']) ,
                'b' => $fordb->dt['mobile_sales']
            );

            $mstring .= "
				<tr height=30 bgcolor=#ffffff align=right id='Report$i'>
				<td class='list_box_td list_bg_gray '  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=center nowrap>$i </td>
				<td class='list_box_td point number'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px' >".number_format($fordb->dt['sales'] - $sale_mileage_order,0)."</td>
				<td class='list_box_td number'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px' >".number_format($fordb->dt['web_sales'] - $sale_mileage_order_web,0)."</td>
				<td class='list_box_td number'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px' >".number_format($fordb->dt['mobile_sales'] - $sale_mileage_order_mobile,0)."</td>
				<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".returnZeroValue($fordb->dt['web_ucnt']+$fordb->dt['mobile_ucnt'])."</td>
				<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".returnZeroValue($fordb->dt['web_ucnt'])."</td>
				<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".returnZeroValue($fordb->dt['mobile_ucnt'])."</td>
				<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".returnZeroValue($fordb->dt['cnt'])."</td>
				<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".returnZeroValue($fordb->dt['web_cnt'])."</td>
				<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".returnZeroValue($fordb->dt['mobile_cnt'])."</td>
				</tr>";
            $sumprice = $sumprice + $fordb->dt['sales'] - $sale_mileage_order;
            $sumprice_web = $sumprice_web + $fordb->dt['web_sales'] - $sale_mileage_order_web;
            $sumprice_mobile = $sumprice_mobile + $fordb->dt['mobile_sales'] - $sale_mileage_order_mobile;

            $sumucnt = $sumucnt + $fordb->dt['web_ucnt'] + $fordb->dt['mobile_ucnt'];
            $sumucnt_web = $sumucnt_web + $fordb->dt['web_ucnt'];
            $sumucnt_mobile = $sumucnt_mobile + $fordb->dt['mobile_ucnt'];

            $sumcnt = $sumcnt + $fordb->dt['cnt'];
            $sumcnt_web = $sumcnt_web + $fordb->dt['web_cnt'];
            $sumcnt_mobile = $sumcnt_mobile + $fordb->dt['mobile_cnt'];
            $j = $j + 1;
            $fordb->fetch($j);
        }else{
            $chart_data[] = array(
                'y' => date("Y-m-d H:00",strtotime($vdate)+(60*60*$i)),
                'a' => 0 ,
                'b' => 0
            );

            $mstring .= "
				<tr height=30  align=right id='Report$i'>
				<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=center nowrap> $i </td>
				<td class='list_box_td point number'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px' align=right>0</td>
				<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >0</td>
				<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >0</td>
				<td class='list_box_td list_bg_gray number'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px' align=right>0</td>
				<td class='list_box_td '  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >0</td>
				<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >0</td>
				<td class='list_box_td list_bg_gray number'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px' align=right>0</td>
				<td class='list_box_td '  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >0</td>
				<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >0</td>
				</tr>";
        }
    }

//	}
    $mstring .= "<tr height=30 align=center>
	<td class=s_td >합계</td>
	<td class='m_td number' style='padding-right:20px' >".number_format($sumprice,0)."</td>
	<td class='m_td number' style='padding-right:20px' >".number_format($sumprice_web,0)."</td>
	<td class='m_td number' style='padding-right:20px' >".number_format($sumprice_mobile,0)."</td>
	<td class=m_td >".number_format($sumucnt)."</td>
	<td class=m_td >".number_format($sumucnt_web)."</td>
	<td class=m_td >".number_format($sumucnt_mobile)."</td>
	<td class=e_td >".number_format($sumcnt)."</td>
	<td class=e_td >".number_format($sumcnt_web)."</td>
	<td class=e_td >".number_format($sumcnt_mobile)."</td>
	</tr>\n";
    $mstring .= "</table><br>\n";
//	$mstring .= "<table cellpadding=3 cellspacing=0 width=745  >\n";
//	if ($pageview01 == 0){
//		$mstring .= "<tr height=50 bgcolor=#ffffff align=center><td colspan=5>결과값이 없습니다.</td></tr>\n";
//	}
//	$mstring .= "<tr height=2 bgcolor=#ffffff align=center><td colspan=5 width=190></td></tr>\n";
//	$mstring .= "<tr height=25 align=center><td width=50 class=s_td width=30 colspan=2>합계</td><td class=e_td width=190>".$pageview01."</td></tr>\n";
//	$mstring .= "</table>\n";
    /*
        $help_text = "
        <table>
            <tr>
                <td style='line-height:150%'>
                - 쇼핑몰에서 발생한 매출을 시간대별로 보여주는 리포트입니다.<br>

                </td>
            </tr>
        </table>
        ";*/
    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );


    $mstring .= HelpBox("매출액(시간대)", $help_text);



    $mstring .= "<link href='../css/morris.css' rel='stylesheet'>
<!--script src='../js/jquery-1.10.2.min.js'></script-->
<script src='../js/jquery-migrate-1.2.1.min.js'></script>
<script src='../js/bootstrap.min.js'></script>

<script src='../js/modernizr.min.js'></script> 
<script src='../js/jquery.sparkline.min.js'></script>
<script src='../js/toggles.min.js'></script>

<script src='../js/retina.min.js'></script> 
<script src='../js/jquery.cookies.js'></script>

<script src='../js/flot/flot.min.js'></script>
<script src='../js/flot/flot.resize.min.js'></script>
<script src='../js/flot/flot.symbol.min.js'></script>
<script src='../js/flot/flot.crosshair.min.js'></script>
<script src='../js/flot/flot.categories.min.js'></script>
<script src='../js/flot/flot.pie.min.js'></script>
<script src='../js/morris.min.js'></script>
<script src='../js/raphael-2.1.0.min.js'></script>

<script src='../js/custom.js'></script>
<!--script src='../js/charts.js'></script-->
<script script='javascript'>
  new Morris.Bar({
        // ID of the element in which to draw the chart.
        element: 'stacked-chart',
        // Chart data records -- each entry in this array corresponds to a point on
        // the chart.
        data: ".json_encode($chart_data).",
        xkey: 'y',
        ykeys: ".json_encode($ykeys).",
        labels: ".json_encode($labels).",
        barColors: ['#5BC0DE', '#1CAF9A'],
        lineWidth: '1px',
        fillOpacity: 0.8,
        smooth: false,
        stacked: true,
        hideHover: false
    });
</script>";
//lineColors: ['#D9534F', '#428BCA','#1CAF9A','#5BC0DE'],
//print_r($chart_data);
    return $mstring;
}

if ($mode == "iframe"){
    echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";


    $ca = new Calendar();
    $ca->SelectReport = $SelectReport;
    $ca->LinkPage = 'salesbytime.php';


    echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";

    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.ChangeCalenderView($SelectReport);</Script>";


}else{
    $p = new forbizReportPage();
    $p->TopNaviSelect = "step2";
    $p->forbizLeftMenu = commerce_munu('salesbytime.php');//.text_button("#", "test","190").colorCirCleBoxStart("#efefef",190)."test<br>test<br>test<br>test<br>test<br>".colorCirCleBoxEnd("#efefef");
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->Navigation = "이커머스분석 > 매출종합분석 > 매출액(시간대)";
    $p->title = "매출액(시간대)";
    $p->PrintReportPage();
}
?>
