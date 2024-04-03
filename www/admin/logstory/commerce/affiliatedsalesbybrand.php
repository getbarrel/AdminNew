<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");
include("../report/etcreferer.chart.php");
include("../include/commerce.lib.php");

function ReportTable($vdate,$SelectReport=1){
    global $depth,$referer_id, $non_sale_status, $order_status, $cancel_status, $return_status, $all_sale_status;
    global $search_sdate, $search_edate;



    $nview_cnt = 0;
    $cid = $referer_id;
    if($SelectReport == ""){
        $SelectReport = 1;
    }
    $fordb = new forbizDatabase();
    if($depth == ""){
        $depth = 0;
    }else{
        $depth = $depth+1;
    }



    if($vdate == ""){
        $vdate = date("Ymd", time());
        $selected_date = date("Ymd", time());
        $vyesterday = date("Ymd", time()-84600);
        $voneweekago = date("Ymd", time()-84600*7);
        $search_sdate = date("Y-m-d", time()-84600*7);
        $search_edate = date("Y-m-d", time());
    }else{
        if($SelectReport ==3){
            $vdate = $vdate."01";
        }
        $selected_date = date("Y-m-d", mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)));
        $vweekenddate = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6);
        $vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
        $voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
    }


    $sql = "select site_code, site_name from sellertool_site_info where disp = 1 ";
    $fordb->query($sql);
    $affiliates = $fordb->fetchall();

    $real_sale_status = array_merge($all_sale_status, $cancel_status, $return_status);

    if ($SelectReport == 1){
        $sql = "Select IFNULL(od.brand_code,'9999999999') as brand_code, od.brand_code as brand_code, od.cid as cid,  IFNULL(od.brand_name,'기타') as brand_name,  
							sum(case when od.status not in ('".implode("','",$real_sale_status)."')  then od.pcnt else 0 end) as real_sale_all_cnt, 
							sum(case when od.status not in ('".implode("','",$real_sale_status)."')  then od.ptprice else 0 end) as real_sale_all_sum 
							";
        for($j=0; $j < count($affiliates);$j++){
            $sql .= ", sum(case when od.status not in ('".implode("','",$real_sale_status)."') and od.order_from = '".$affiliates[$j]['site_code']."'  then od.pcnt else 0 end) as ".$affiliates[$j]['site_code']."_sale_all_cnt,
						 sum(case when od.status not in ('".implode("','",$real_sale_status)."') and od.order_from = '".$affiliates[$j]['site_code']."'  then od.ptprice else 0 end) as ".$affiliates[$j]['site_code']."_sale_all_sum
						 ";
        }

        $sql .= "
							from  shop_order_detail od 
							where od.regdate  between '".$selected_date." 00:00:00' and '".$selected_date." 23:59:59'   AND od.status NOT IN ('".implode("','",$non_sale_status)."') 
							group by od.brand_code
							";
        //echo nl2br($sql);
        //exit;
        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
    }else if($SelectReport == 2 || $SelectReport == 4){
        if($SelectReport == "4"){
            $vdate = $search_sdate;
            $selected_date = substr($search_sdate, 0, 4)."-".substr($search_sdate, 4, 2)."-".substr($search_sdate, 6, 2);
            $vweekenddate = date("Ymd", mktime(0,0,0,substr($search_edate,4,2),substr($search_edate,6,2),substr($search_edate,0,4)));
        }

        $sql = "Select IFNULL(od.brand_code,'9999999999') as brand_code, od.brand_code as brand_code, od.cid as cid,  IFNULL(od.brand_name,'기타') as brand_name,  
							sum(case when od.status not in ('".implode("','",$real_sale_status)."')  then od.pcnt else 0 end) as real_sale_all_cnt, 
							sum(case when od.status not in ('".implode("','",$real_sale_status)."')  then od.ptprice else 0 end) as real_sale_all_sum 
							";
        for($j=0; $j < count($affiliates);$j++){
            $sql .= ", sum(case when od.status not in ('".implode("','",$real_sale_status)."') and od.order_from = '".$affiliates[$j]['site_code']."'  then od.pcnt else 0 end) as ".$affiliates[$j]['site_code']."_sale_all_cnt,
					 sum(case when od.status not in ('".implode("','",$real_sale_status)."') and od.order_from = '".$affiliates[$j]['site_code']."'  then od.ptprice else 0 end) as ".$affiliates[$j]['site_code']."_sale_all_sum
					 ";
        }

        $sql .= "
						from  shop_order_detail od 
						where od.regdate  between '".$selected_date." 00:00:00' and '".substr($vweekenddate, 0, 4)."-".substr($vweekenddate, 4, 2)."-".substr($vweekenddate, 6, 2)." 23:59:59' 
						group by od.brand_code
						";
        //where date_format(od.regdate,'%Y%m%d')  between '".$vdate."' and '".$vweekenddate."'
        if($SelectReport == 2){
            $dateString = "주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
        }else if($SelectReport == 4){
            $s_week_num = date("w",mktime(0,0,0,substr($search_sdate,4,2),substr($search_sdate,6,2),substr($search_sdate,0,4)));
            $e_week_num = date("w",mktime(0,0,0,substr($search_edate,4,2),substr($search_edate,6,2),substr($search_edate,0,4)));
            $dateString = "기간별 : ".getNameOfWeekday($s_week_num,$search_sdate,"priodname")."~".getNameOfWeekday($e_week_num,$search_edate,"priodname");
        }

    }else if($SelectReport == 3){

        $sql = "Select IFNULL(od.brand_code,'9999999999') as brand_code, od.brand_code as brand_code, od.cid as cid,  IFNULL(od.brand_name,'기타') as brand_name,  
							sum(case when od.status not in ('".implode("','",$real_sale_status)."')  then od.pcnt else 0 end) as real_sale_all_cnt, 
							sum(case when od.status not in ('".implode("','",$real_sale_status)."')  then od.ptprice else 0 end) as real_sale_all_sum 
							";
        for($j=0; $j < count($affiliates);$j++){
            $sql .= ", sum(case when od.status not in ('".implode("','",$real_sale_status)."') and od.order_from = '".$affiliates[$j]['site_code']."'  then od.pcnt else 0 end) as ".$affiliates[$j]['site_code']."_sale_all_cnt,
					 sum(case when od.status not in ('".implode("','",$real_sale_status)."') and od.order_from = '".$affiliates[$j]['site_code']."'  then od.ptprice else 0 end) as ".$affiliates[$j]['site_code']."_sale_all_sum
					 ";
        }

        $sql .= "
						from  shop_order_detail od 
						where od.regdate between '".substr($selected_date,0,7)."-01 00:00:00' and '".substr($selected_date,0,7)."-31 23:59:59' 
						group by od.brand_code
						";
        $dateString = "월간 : ".getNameOfWeekday(0,$vdate,"monthname");

        //echo nl2br($sql);
        //exit;

    }
    //echo "cid:".$cid."<br>";
    //echo "depth:".$depth."<br>";
    //echo time()."<br>";

    if($_SERVER["REMOTE_ADDR"] == "175.209.244.68"){
        //echo nl2br($sql);
    }
    if($sql){
        $fordb->query($sql);
    }

    $mstring = "<table width='100%' border=0>
						<tr><td>".TitleBar("상품군별 분석 : ".($cid ? getCategoryPath($cid,4):""),$dateString)."</td><td align=right>(단위:원) </td></tr>
					</table>";
    $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>
						<col width='3%'>
						<col width='*'>
						<col width='6%'>
						<col width='7%'>
							";
    for($j=0; $j < count($affiliates);$j++){
        $mstring .= "<col width='6%'>
						<col width='7%'>";

    }
    $mstring .= "	";
    $mstring .= "
		<tr height=30>
			<td class=s_td rowspan=2>순</td>
			<td class=m_td rowspan=2>날짜</td>
			<td class=m_td rowspan=2>건수</td>
			<td class=m_td rowspan=2>합계</td>
			";
    for($j=0; $j < count($affiliates);$j++){
        $mstring .= "<td class=m_td colspan=1>".$affiliates[$j]['site_name']."</td>";

    }
    $mstring .= "	
			<!--td class=m_td colspan=8>매출</td>
			<td class=m_td rowspan=3>실매출액<br>원가</td>
			<td class=m_td colspan=2 rowspan=2>수익</td-->
		</tr> 
		<tr height=30 align=center>	";
    for($j=0; $j < count($affiliates);$j++){
        $mstring .= "<!--td class=m_td nowrap>수량(개)</td-->
							<td class=m_td >주문액(원)</td>";

    }
    $mstring .= "	
			</tr>\n";
    /*

    sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.pcnt else 0 end) as order_sale_cnt,
                    sum(case when od.status NOT IN ('".implode("','",$non_sale_status)."')  then od.ptprice else 0 end) as order_sale_sum,
                    sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.pcnt else 0 end) as cancel_sale_cnt,
                    sum(case when od.status IN ('".implode("','",$cancel_status)."')  then od.ptprice else 0 end) as cancel_sale_sum,
                    sum(case when od.status IN ('".implode("','",$return_status)."')  then od.pcnt else 0 end) as return_sale_cnt,
                    sum(case when od.status IN ('".implode("','",$return_status)."')  then od.ptprice else 0 end) as return_sale_sum,

                    */
    $real_sale_all_sum = 0;
    $real_sale_all_cnt = 0;
    $affiliate_sale_sum = array();
    for($i=0;$i<$fordb->total;$i++){
        $fordb->fetch($i);
        if(!empty($fordb->dt['vdate'])) {
            $week_num = date("w",mktime(0,0,0,substr($fordb->dt['vdate'],4,2),substr($fordb->dt['vdate'],6,2),substr($fordb->dt['vdate'],0,4)));
        }else {
            $week_num = '';
        }

        $mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i' ".($week_num == "0" ? "style='color:red;'":"")." ".($week_num == "6" ? "style='color:blue;'":"")."> 
		<td class='list_box_td list_bg_gray' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($i+1)."</td>
		<td class='list_box_td point' style='text-align:left;' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".$fordb->dt['brand_name']." </td>
		

		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['real_sale_all_cnt'],0)."&nbsp;</td>
		<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['real_sale_all_sum'],0)."&nbsp;</td>
";

        for($j=0; $j < count($affiliates);$j++){
            $mstring .= "<!--td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[$affiliates[$j]['site_code']."_sale_all_cnt"],0)."&nbsp;</td-->
					<td class='list_box_td number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt[$affiliates[$j]['site_code']."_sale_all_sum"],0)."&nbsp;</td>";

            $affiliate_sale_sum[$affiliates[$j]['site_code']] += $fordb->dt[$affiliates[$j]['site_code']."_sale_all_sum"];

        }





        $mstring .= " 
		</tr>\n";
        $real_sale_all_sum = $real_sale_all_sum + returnZeroValue($fordb->dt['real_sale_all_sum']);
        $real_sale_all_cnt = $real_sale_all_cnt + returnZeroValue($fordb->dt['real_sale_all_cnt']);




    }

    if ($real_sale_all_sum == 0){
        $mstring .= "<tr  align=center height=200><td colspan=16 class='list_box_td'  >결과값이 없습니다.</td></tr>\n";
    }

    $mstring .= "<tr height=25 align=right>
	<td class=s_td align=center colspan=2>합계</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_all_cnt,0)."</td>
	<td class='e_td number' style='padding-right:10px;'>".number_format($real_sale_all_sum,0)."</td>";
    for($j=0; $j < count($affiliates);$j++){
        $mstring .= "<td class='e_td number' style='padding-right:10px;'>".number_format($affiliate_sale_sum[$affiliates[$j]['site_code']],0)."</td>";
    }
    $mstring .= " 
	</tr>\n";
    $mstring .= "</table>\n";
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


    $mstring .= HelpBox("상품군별분석", $help_text);
    return $mstring;
}





if ($mode == "iframe"){
    echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";


    $ca = new Calendar();
    $ca->SelectReport = $SelectReport;
    $ca->LinkPage = 'affiliatedsalesbybrand.php';


    echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";

    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.ChangeCalenderView($SelectReport);</Script>";


}else{
    $p = new forbizReportPage();
    $p->TopNaviSelect = "step2";
    $p->forbizLeftMenu = commerce_munu('affiliatedsalesbybrand.php');//.text_button("#", "test","190").colorCirCleBoxStart("#efefef",190)."test<br>test<br>test<br>test<br>test<br>".colorCirCleBoxEnd("#efefef");
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->Navigation = "이커머스분석 > 매출종합분석 > 제휴 브랜드별 분석";
    $p->ContentsWidth = "97%";
    $p->title = "제휴 브랜드별 분석";
    $p->PrintReportPage();
}




function ReportTable2($vdate,$SelectReport=1){
    $pageview01 = 0;
    $fordb = new forbizDatabase();
    $fordb2 = new forbizDatabase();
    $fordb3 = new forbizDatabase();


    if($SelectReport == ""){
        $SelectReport = 1;
    }
    if($vdate == ""){
        $vdate = date("Ymd", time());
        $vyesterday = date("Ymd", time()-84600);
        $voneweekago = date("Ymd", time()-84600*7);
        $vtwoweekago = date("Ymd", time()-84600*14);
        $vfourweekago = date("Ymd", time()-84600*28);
        $vonemonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)));
        $vtwomonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)));
    }else{
        if($SelectReport ==3){
            $vdate = substr($vdate,0,6)."01";
        }
        $vweekenddate = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6);
        $vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
        $voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
        $vtwoweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*14);
        $vfourweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
        $vonemonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)));
        $vtwomonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)));
    }

    if($SelectReport == 1){
        $nLoop = 24;
    }else if($SelectReport ==2){
        $nLoop = 7;
    }else if($SelectReport ==3){
        $nLoop = date("t", mktime(0, 0, 0, substr($vdate,4,2), substr($vdate,6,2), substr($vdate,0,4)));
    }

    if($SelectReport == 1){
        $sql = "Select vdate, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt from ".TBL_COMMERCE_SALESTACK." where   vdate = '$vdate' and step6 = 1 group by vdate order by vdate";
        $sql2 = "Select vdate, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt from ".TBL_COMMERCE_SALESTACK." where   vdate = '$vyesterday' and step6 = 1 group by vdate order by vdate";
        $sql3 = "Select vdate, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt from ".TBL_COMMERCE_SALESTACK." where   vdate = '$voneweekago' and step6 = 1 group by vdate order by vdate";

        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
        $title1 = "해당일";
        $title2 = "1일전";
        $title3 = "일주전";
    }else if($SelectReport == 2){

        $sql = "SELECT vdate, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt FROM ".TBL_COMMERCE_SALESTACK." c where vdate between '$vdate' and '$vweekenddate' and step6 = 1 group by vdate ";

        $dateString = "주간 : ". getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);

    }else if($SelectReport == 3){
        $sql = "SELECT c.vdate as vdate, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt FROM ".TBL_COMMERCE_SALESTACK." c where vdate LIKE '".substr($vdate,0,6)."%' and step6 = 1 group by c.vdate ";

        $dateString = getNameOfWeekday(0,$vdate,"monthname");

    }






    $mstring = $mstring.TitleBar("제휴 브랜드별 분석",$dateString);
    if($SelectReport == 1){
        $fordb->query($sql);
        $fordb2->query($sql2);
        $fordb3->query($sql3);

        $mstring .= "<table cellpadding=3 cellspacing=0 width=100% class='list_table_box'  >\n";
        $mstring .= "<tr height=30  align=center style='font-weight:bold'><td class=s_td width=150>날짜</td><td class=m_td width=165>매출액</td><td class=m_td width=150 nowrap>구매자수</td><td class=e_td width=150 nowrap>구매수량</td></tr>\n";

//	if($fordb->total == 0){
//		$mstring .= "<tr height=50 bgcolor=#ffffff align=center><td colspan=5>결과값이 없습니다.</td></tr>\n";
//	}else{


        $fordb->fetch(0);
        $fordb2->fetch(0);
        $fordb3->fetch(0);

        $mstring .= "
			<tr height=30  id='Report$i'>
			<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0;' nowrap>$title1 </td>
			<td class='list_box_td point number'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:20px' align=center>".number_format($fordb->dt['sales'],0)."</td>
			<td class='list_box_td list_bg_gray'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=right>".returnZeroValue($fordb->dt['ucnt'])."</td>
			<td class='list_box_td'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=right>".returnZeroValue($fordb->dt['cnt'])."</td>
			</tr>";
        $i = $i + 1;
        $mstring .= "
			<tr height=30  id='Report$i'>
			<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0;' nowrap>$title2 </td>
			<td class='list_box_td point number'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:20px' align=right>".BarchartView($fordb->dt['sales'],$fordb2->dt['sales'])."</td>
			<td class='list_box_td list_bg_gray'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=right>".BarchartView(returnZeroValue($fordb->dt['ucnt']),returnZeroValue($fordb2->dt['ucnt']))."</td>
			<td class='list_box_td'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=right>".BarchartView(returnZeroValue($fordb->dt['cnt']),returnZeroValue($fordb2->dt['cnt']))."</td>
			</tr>";
        $i = $i + 1;
        $mstring .= "
			<tr height=30  id='Report$i'>
			<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:0;' nowrap>$title3 </td>
			<td class='list_box_td point number'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:20px' align=right>".BarchartView(returnZeroValue($fordb->dt['sales']),returnZeroValue($fordb3->dt['sales']))."</td>
			<td class='list_box_td list_bg_gray'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=right>".BarchartView(returnZeroValue($fordb->dt['ucnt']),returnZeroValue($fordb3->dt['ucnt']))."</td>
			<td class='list_box_td'  align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=right>".BarchartView(returnZeroValue($fordb->dt['cnt']),returnZeroValue($fordb3->dt['cnt']))."</td>
			</tr>
			";

        /*
                $mstring .= "<tr height=30>
                <td style='padding-left:20px' class=s_td>결제완료</td><td align=center class=m_td width=125>".$fordb->dt['step6']."</td><td align=center class=m_td width=125>-</td><td align=center class=e_td width=125>-</td>
                </tr>\n";
        */
        //	$exitcnt = $pageview01 + returnZeroValue($fordb->dt['visit_cnt']);


        //}

        $mstring .= "</table>\n<br>";

    }else if ($SelectReport == 2){
        $fordb->query($sql);
        //echo $sql;

        $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  class='list_table_box'  >\n";
        $mstring .= "<tr height=30  align=center style='font-weight:bold'><td class=s_td width=150>시간</td><td class=m_td width=165>매출액</td><td class=m_td width=150 nowrap>구매자수</td><td class=e_td width=150 nowrap>구매수량</td></tr>\n";

        if($fordb->total == 0){
            $mstring .= "<tr height=150 h align=center><td colspan=4>결과값이 없습니다.</td></tr>\n";
        }else{
            $j = 0;
            $fordb->fetch($j);
            for($i=0;$i < $nLoop;$i++){

                if($fordb->dt['vdate'] == date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*$i)){
                    $mstring .= "
				<tr height=30 id='Report$i'>
				<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=center nowrap>".getNameOfWeekday($i,$vdate)."</td>
				<td class='list_box_td point number'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px' align=right>".number_format(returnZeroValue($fordb->dt['sales']),0)."</td>
				<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px'>".returnZeroValue($fordb->dt['ucnt'])."</td>
				<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px'>".returnZeroValue($fordb->dt['cnt'])."</td>
				</tr>";

                    $j = $j + 1;
                    $fordb->fetch($j);
                    $sumprice = $sumprice + $fordb->dt['sales'];
                    $sumucnt = $sumucnt + $fordb->dt['ucnt'];
                    $sumcnt = $sumcnt + $fordb->dt['cnt'];
                }else{
                    $mstring .= "
				<tr height=30  id='Report$i'>
				<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=center nowrap>".getNameOfWeekday($i,$vdate)."</td>
				<td class='list_box_td point number'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px' align=right>0</td>
				<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px'>0</td>
				<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px'>0</td>
				</tr>";
                }

            }

        }
        $mstring .= "<tr height=30  >
	<td class=s_td align=center>합계</td>
	<td class='m_td number' style='padding-right:20px' width=165>".number_format($sumprice,0)."</td>
	<td class=m_td style='padding-right:20px' nowrap>".number_format(returnZeroValue($sumucnt))."</td>
	<td class=e_td style='padding-right:20px' nowrap>".number_format(returnZeroValue($sumcnt))."</td>
	</tr>\n";
        $mstring .= "</table><br>\n";
    }else if ($SelectReport == 3){
        $fordb->query($sql);

        $mstring .= "<table cellpadding=3 cellspacing=0 width=100% class='list_table_box'   >\n";
        $mstring .= "<tr height=30  align=center style='font-weight:bold'><td class=s_td width=150>시간</td><td class=m_td width=165>매출액</td><td class=m_td width=150 nowrap>구매자수</td><td class=e_td width=150 nowrap>구매수량</td></tr>\n";

        if($fordb->total == 0){
            $mstring .= "<tr class='list_box_td'  align=center><td colspan=4 height=150 >결과값이 없습니다.</td></tr>\n";
        }else{
            $j = 0;
            $fordb->fetch($j);
            for($i=0;$i < $nLoop;$i++){

                if($fordb->dt['vdate'] == date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*$i)){
                    $mstring .= "
				<tr height=30  id='Report$i'>
				<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=center nowrap>".getNameOfWeekday($i,$vdate,"dayname")."</td>
				<td class='list_box_td point number'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px' >".number_format(returnZeroValue($fordb->dt['sales']),0)."</td>
				<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px'>".number_format(returnZeroValue($fordb->dt['ucnt']))."</td>
				<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px'>".number_format(returnZeroValue($fordb->dt['cnt']))."</td>
				</tr>";

                    $j = $j + 1;
                    $fordb->fetch($j);
                    $sumprice = $sumprice + $fordb->dt['sales'];
                    $sumucnt = $sumucnt + $fordb->dt['ucnt'];
                    $sumcnt = $sumcnt + $fordb->dt['cnt'];
                }else{
                    $mstring .= "
				<tr height=30  id='Report$i'>
				<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=center nowrap>".getNameOfWeekday($i,$vdate,"dayname")."</td>
				<td class='list_box_td point number'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px'>0</td>
				<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px'>0</td>
				<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px'>0</td>
				</tr>";
                }

            }

        }
        $mstring .= "<tr height=30 >
	<td class=s_td align=center>합계</td>
	<td class='m_td number' style='padding-right:20px'>".number_format($sumprice,0)."</td>
	<td class=m_td style='padding-right:20px'>".number_format(returnZeroValue($sumucnt))."</td>
	<td class=e_td style='padding-right:20px'>".number_format(returnZeroValue($sumcnt))."</td>
	</tr>\n";
        $mstring .= "</table><br>\n";
    }

//	$mstring .= "<table cellpadding=3 cellspacing=0 width=745  >\n";
//	if ($pageview01 == 0){
//		$mstring .= "<tr height=50 class='list_box_td'  align=center><td colspan=5>결과값이 없습니다.</td></tr>\n";
//	}
//	$mstring .= "<tr height=2 class='list_box_td'  align=center><td colspan=5 width=190></td></tr>\n";
//	$mstring .= "<tr height=30  align=center><td width=50 class=s_td width=30 colspan=2>합계</td><td class=e_td width=190>".$pageview01."</td></tr>\n";
//	$mstring .= "</table>\n";
    /*
        $help_text = "
        <table>
            <tr>
                <td style='line-height:150%'>
                - 쇼핑몰에서 발생한 매출을 일자별로 보여주며 일 단위로 쇼핑몰의 전반적인 매출관련 내용을 요약하여 이전 매출 내역과 비교해 간략하게 보여주는 리포트입니다.<br>
                - 매출액 : 쇼핑몰의 총 매출액과 이전 매출액을 비교하여 매출비율을 확인하실 수 있습니다.<br>
                - 구매자수 : 쇼핑몰의 총 구매자수와 이전 대비 해당 일의 구매자수를 비교하여 해당 일의 구매자 비율을 확인하실 수 있습니다.<br>
                - 구매수량 : 쇼핑몰의 총 구매수량과 이전 대비 해당 일의 구매수량을 비교하여 확인하실 수 있습니다.<br>

                </td>
            </tr>
        </table>
        ";*/
    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );


    $mstring .= HelpBox("제휴 브랜드별 분석", $help_text);
    return $mstring;
}


?>
