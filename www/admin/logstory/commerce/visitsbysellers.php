<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");
include("../report/etcreferer.chart.php");

function ReportTable($vdate,$SelectReport=1){
    global $search_sdate, $search_edate;



    $pageview01 = 0;
    $fordb = new forbizDatabase();
    $fordb2 = new forbizDatabase();


    if($SelectReport == ""){
        $SelectReport = 1;
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

    if($SelectReport == 1){

        $sql = "Select a.cid, c.pname ,  c.sellprice as vprice, ccd.com_name, sum(a.nview_cnt) as nview_cnt, IFNULL(cs.vquantity,0) as order_cnt , IFNULL(cs.vprice,0) as order_price
			from ".TBL_COMMERCE_VIEWINGVIEW." a, ".TBL_SHOP_PRODUCT." c 
			left join (Select a.cid, sum(a.vquantity) as vquantity , a.vprice, a.pid
				from ".TBL_COMMERCE_SALESTACK." a
				where vdate = '$vdate' and step6 = 1 and a.vreferer_id LIKE '000001003011%'
				group by a.pid, a.cid, a.vprice
				order by vquantity desc) cs	on c.id = cs.pid
				join common_company_detail ccd on c.admin = ccd.company_id
			where a.pid = c.id and vdate = '$vdate' and a.vreferer_id LIKE '000001003011%'
			group by ccd.company_id 
			order by nview_cnt desc limit 0,50";
        //echo nl2br($sql);
        //exit;
        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
    }else if($SelectReport == 2 || $SelectReport == 4){
        if($SelectReport == "4"){
            $vdate = $search_sdate;
            $vweekenddate = $search_edate;
        }

        $sql = "Select a.cid, c.pname ,  c.sellprice as vprice, ccd.com_name, sum(a.nview_cnt) as nview_cnt, IFNULL(cs.vquantity,0) as order_cnt , IFNULL(cs.vprice,0) as order_price
				from ".TBL_COMMERCE_VIEWINGVIEW." a, ".TBL_SHOP_PRODUCT." c   
				left join
					(Select a.cid , a.vprice, a.pid, sum(a.vquantity) as vquantity
					from ".TBL_COMMERCE_SALESTACK." a
					where vdate between '$vdate' and '$vweekenddate' and step6 = 1 and a.vreferer_id LIKE '000001003011%'
					group by a.cid , a.vprice, a.pid
					order by vquantity desc) cs	on c.id = cs.pid
					join common_company_detail ccd on c.admin = ccd.company_id
				where a.pid = c.id and vdate between '$vdate' and '$vweekenddate' and a.vreferer_id LIKE '000001003011%'
				group by ccd.company_id
				order by nview_cnt desc limit 0,50";

        if($SelectReport == 2){
            $dateString = "주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
        }else if($SelectReport == 4){
            $s_week_num = date("w",mktime(0,0,0,substr($search_sdate,4,2),substr($search_sdate,6,2),substr($search_sdate,0,4)));
            $e_week_num = date("w",mktime(0,0,0,substr($search_edate,4,2),substr($search_edate,6,2),substr($search_edate,0,4)));
            $dateString = "기간별 : ".getNameOfWeekday($s_week_num,$search_sdate,"priodname")."~".getNameOfWeekday($e_week_num,$search_edate,"priodname");
        }
    }else if($SelectReport == 3){

        $sql = "Select a.cid, c.pname,  IFNULL(c.sellprice,0) as vprice, ccd.com_name, sum(a.nview_cnt) as nview_cnt, IFNULL(cs.vquantity,0) as order_cnt , IFNULL(cs.vprice,0) as order_price
				from ".TBL_COMMERCE_VIEWINGVIEW." a, ".TBL_SHOP_PRODUCT." c
				left join
					(
					Select a.cid, a.vprice, a.pid, sum(a.vquantity) as vquantity
					from ".TBL_COMMERCE_SALESTACK." a
					where vdate LIKE '".substr($vdate,0,6)."%'  and step6 = 1 and a.vreferer_id LIKE '000001003011%'
					group by a.cid, a.vprice, a.pid
					order by vquantity desc
					) cs on c.id = cs.pid
				join common_company_detail ccd on c.admin = ccd.company_id 
				where a.pid = c.id and vdate LIKE '".substr($vdate,0,6)."%' and a.vreferer_id LIKE '000001003011%'
				group by  ccd.company_id
				order by nview_cnt desc limit 0,50"; // IFNULL(c.sellprice,0), IFNULL(cs.vquantity,0), IFNULL(cs.vprice,0)

        $dateString = getNameOfWeekday(0,$vdate,"monthname");
    }

    //echo nl2br($sql);
    if($sql){
        $fordb->query($sql);
    }


    $mstring = TitleBar("셀러별 방문수",$dateString);


    $mstring .= "<table cellpadding=0 cellspacing=0 width=100% style='table-layout:fixed'  class='list_table_box'  >
							<col width='5%'>
							<col width=* >
							<col width=8% nowrap>
							<col width=8% nowrap>
							<col width=8% nowrap>
							<col width=8% nowrap>
							<col width=13% style='color:red'>
				";
    $mstring .= "<tr height=30 align=center style='font-weight:bold'>
								<td class=s_td >순</td>
								<td class=m_td >셀러명</td>
								<td class=m_td >조회횟수</td>
								<td class=m_td >단가</td>
								<td class=m_td >구매수</td>
								<td class=m_td >구매금액</td>
								<td class=e_td >방문비율 </td>
								</tr>\n";

    if($fordb->total == 0){
        $mstring = $mstring."<tr height=150 bgcolor=#ffffff align=center><td colspan=7>결과값이 없습니다.</td></tr>\n";
    }else{
        for($i=0;$i<$fordb->total;$i++){
            $fordb->fetch($i);
            $visit_sum += $fordb->dt['nview_cnt'];
        }
        for($i=0;$i<$fordb->total;$i++){
            $fordb->fetch($i);

            $mstring .= "<tr height=40 bgcolor=#ffffff  id='Report$i'>
			<td class='list_box_td list_bg_gray' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=center nowrap>".($i+1)."</td>
			<td class='list_box_td point' style='text-align:left;padding:10px;line-height:140%;' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" wrap><b>".$fordb->dt['com_name']."</b></td>
			<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".$fordb->dt['nview_cnt']."</td>
			<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['vprice'])."</td>
			<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".$fordb->dt['order_cnt']."</td>
			<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['order_cnt']*$fordb->dt['order_price'])."</td>
			<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='color:red'>".number_format($fordb->dt['nview_cnt']/$visit_sum*100,0)."%</td>
			</tr>";

            /*
            $mstring .= "<tr height=30>
            <td style='padding-left:20px' class=s_td>결제완료</td><td align=center class=m_td width=125>".$fordb->dt['step6']."</td><td align=center class=m_td width=125>-</td><td align=center class=e_td width=125>-</td>
            </tr>\n";
    */
            $nview_cnt = $nview_cnt + returnZeroValue($fordb->dt['nview_cnt']);
            $vprice = $vprice + $fordb->dt['vprice'];
            $order_cnt = $order_cnt + returnZeroValue($fordb->dt['order_cnt']);
            $order_price = $order_price + returnZeroValue($fordb->dt['order_cnt']*$fordb->dt['order_price']);
            $vsale = $vsale + ($fordb->dt['vprice'] * $fordb->dt['nview_cnt']/100);
        }
    }
    //$mstring = $mstring."</table>\n";
    /*$mstring = $mstring."<table cellpadding=0 cellspacing=0 width=100% style='table-layout:fixed;margin-top:5px;'  class='list_table_box' >
                                    <col width='5%'>
                            <col width=* >
                            <col width=8% nowrap>
                            <col width=8% nowrap>
                            <col width=8% nowrap>
                            <col width=8% nowrap>
                            <col width=13% style='color:red' nowrap>";
*/

    //$mstring = $mstring."<tr height=2 bgcolor=#ffffff align=center><td colspan=7></td></tr>\n";
    $mstring = $mstring."<tr height=30 align=center>
				<td width=50 class=s_td width=30 colspan=2>합계</td>
				<td class=m_td >&nbsp;".$nview_cnt."</td>
				<td class=m_td >".number_format($vprice)."</td>
				<td class=m_td >&nbsp;".number_format($order_cnt)."</td>
				<td class=m_td >".number_format($order_price)."</td>
				<td class=e_td >-</td>
				</tr>\n";
    $mstring = $mstring."</table><br><br>\n";

    /*
    $help_text = "
    <table>
        <tr>
            <td style='line-height:150%'>
            - 상품조회 수치를 통해본 상품의 인기도입니다 <br>
            - 또한 조회수와 조회한 상품의 가격을 바탕으로 산출한 잠재매출입니다 약간은 허구적인 수치이나 1/100 로 환산하여 사이트의 정략적인 평가에 도움이 될수 있습니다 <br><br>
            </td>
        </tr>
    </table>
    ";*/
    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );



    $mstring .= HelpBox("셀러별 방문수(네이버 지식쇼핑)", $help_text);

    return $mstring;
}

if ($mode == "iframe"){
    echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";


    $ca = new Calendar();
    $ca->LinkPage = 'maxviewbyproduct.php';

    echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";
    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.ChangeCalenderView($SelectReport);</Script>";


}else{
    $p = new forbizReportPage();
    $p->TopNaviSelect = "step2";
    $p->forbizLeftMenu = commerce_munu('maxviewbyproduct.php');
    $p->forbizContents = ReportTable($vdate,$SelectReport);//"업그레이드 작업중입니다....";//
    $p->Navigation = "이커머스분석 > 상품분석 > 셀러별 방문수(네이버 지식쇼핑)";
    $p->title = "셀러별 방문수(네이버 지식쇼핑)";
    $p->PrintReportPage();
}


function ViewDetailReport($vdate,$SelectReport=1){
    global $search_sdate, $search_edate;

    $pageview01 = 0;
    $fordb = new forbizDatabase();
    $fordb2 = new forbizDatabase();


    if($SelectReport == ""){
        $SelectReport = 1;
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

    if($SelectReport == 1){

        $sql = "Select a.cid, c.pname ,  c.sellprice as vprice, ccd.com_name, sum(a.nview_cnt) as nview_cnt
			from ".TBL_COMMERCE_VIEWINGVIEW." a, ".TBL_SHOP_PRODUCT." c 
				join common_company_detail ccd on c.admin = ccd.company_id
			where a.pid = c.id and vdate = '$vdate'
			group by a.pid, a.cid, c.pname, c.sellprice 
			order by nview_cnt desc limit 0,50";
        //echo nl2br($sql);
        //exit;
        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
    }else if($SelectReport == 2 || $SelectReport == 4){
        if($SelectReport == "4"){
            $vdate = $search_sdate;
            $vweekenddate = $search_edate;
        }

        $sql = "Select a.cid, c.pname ,  c.sellprice as vprice, ccd.com_name, sum(a.nview_cnt) as nview_cnt
				from ".TBL_COMMERCE_VIEWINGVIEW." a, ".TBL_SHOP_PRODUCT." c  join
					join common_company_detail ccd on c.admin = ccd.company_id
				where a.pid = c.id and vdate between '$vdate' and '$vweekenddate'
				group by a.cid, c.pname , c.sellprice
				order by nview_cnt desc limit 0,50";

        if($SelectReport == 2){
            $dateString = "주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
        }else if($SelectReport == 4){
            $s_week_num = date("w",mktime(0,0,0,substr($search_sdate,4,2),substr($search_sdate,6,2),substr($search_sdate,0,4)));
            $e_week_num = date("w",mktime(0,0,0,substr($search_edate,4,2),substr($search_edate,6,2),substr($search_edate,0,4)));
            $dateString = "기간별 : ".getNameOfWeekday($s_week_num,$search_sdate,"priodname")."~".getNameOfWeekday($e_week_num,$search_edate,"priodname");
        }
    }else if($SelectReport == 3){

        $sql = "Select a.cid, c.pname,  IFNULL(c.sellprice,0) as vprice, ccd.com_name, sum(a.nview_cnt) as nview_cnt
				from ".TBL_COMMERCE_VIEWINGVIEW." a, ".TBL_SHOP_PRODUCT." c
				join common_company_detail ccd on c.admin = ccd.company_id
				where a.pid = c.id and vdate LIKE '".substr($vdate,0,6)."%'
				group by  a.cid, c.pname,  c.sellprice
				order by nview_cnt desc limit 0,50"; // IFNULL(c.sellprice,0), IFNULL(cs.vquantity,0), IFNULL(cs.vprice,0)

        $dateString = getNameOfWeekday(0,$vdate,"monthname");
    }

    //echo nl2br($sql);
    if($sql){
        $fordb->query($sql);
    }



    $mstring = $mstring.TitleBar("셀러별 방문수",$dateString);


    $mstring = $mstring."<table cellpadding=0 cellspacing=0 width=100% style='table-layout:fixed'  class='list_table_box'  >
							<col width='5%'>
							<col width=* >
							<col width=8% nowrap>
							<col width=8% nowrap>
							<col width=8% nowrap>
							<col width=8% nowrap>
							<col width=13% style='color:red'>
				";
    $mstring .= "<tr height=30 align=center style='font-weight:bold'>
								<td class=s_td >순</td>
								<td class=m_td >카테고리/상품명</td>
								<td class=m_td >조회횟수</td>
								<td class=m_td >단가</td>
								<td class=m_td >구매수</td>
								<td class=m_td >구매금액</td>
								<td class=e_td >잠재매출 (/100)</td>
								</tr>\n";

    if($fordb->total == 0){
        $mstring = $mstring."<tr height=150 bgcolor=#ffffff align=center><td colspan=7>결과값이 없습니다.</td></tr>\n";
    }else{

        for($i=0;$i<$fordb->total;$i++){
            $fordb->fetch($i);

            $mstring .= "<tr height=40 bgcolor=#ffffff  id='Report$i'>
			<td class='list_box_td list_bg_gray' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=center nowrap>".($i+1)."</td>
			<td class='list_box_td point' style='text-align:left;padding:10px;line-height:140%;' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" wrap>".getCategoryPathByAdmin($fordb->dt['cid'],getDepth($fordb->dt['cid']))."<br><b>".$fordb->dt['pname']."</b></td>
			<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".$fordb->dt['nview_cnt']."</td>
			<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['vprice'])."</td>
			<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".$fordb->dt['order_cnt']."</td>
			<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb->dt['order_cnt']*$fordb->dt['order_price'])."</td>
			<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='color:red'>".number_format($fordb->dt['nview_cnt']*$fordb->dt['vprice']/100,0)."</td>
			</tr>";

            /*
            $mstring .= "<tr height=30>
            <td style='padding-left:20px' class=s_td>결제완료</td><td align=center class=m_td width=125>".$fordb->dt['step6']."</td><td align=center class=m_td width=125>-</td><td align=center class=e_td width=125>-</td>
            </tr>\n";
    */
            $nview_cnt = $nview_cnt + returnZeroValue($fordb->dt['nview_cnt']);
            $vprice = $vprice + $fordb->dt['vprice'];
            $order_cnt = $order_cnt + returnZeroValue($fordb->dt['order_cnt']);
            $order_price = $order_price + returnZeroValue($fordb->dt['order_cnt']*$fordb->dt['order_price']);
            $vsale = $vsale + ($fordb->dt['vprice'] * $fordb->dt['nview_cnt']/100);
        }
    }
    //$mstring = $mstring."</table>\n";
    /*$mstring = $mstring."<table cellpadding=0 cellspacing=0 width=100% style='table-layout:fixed;margin-top:5px;'  class='list_table_box' >
                                    <col width='5%'>
                            <col width=* >
                            <col width=8% nowrap>
                            <col width=8% nowrap>
                            <col width=8% nowrap>
                            <col width=8% nowrap>
                            <col width=13% style='color:red' nowrap>";
*/

    //$mstring = $mstring."<tr height=2 bgcolor=#ffffff align=center><td colspan=7></td></tr>\n";
    $mstring = $mstring."<tr height=30 align=center>
					<td width=50 class=s_td width=30 colspan=2>합계</td>
					<td class=m_td >&nbsp;".$nview_cnt."</td>
					<td class=m_td >".number_format($vprice)."</td>
					<td class=m_td >&nbsp;".number_format($order_cnt)."</td>
					<td class=m_td >".number_format($order_price)."</td>
					<td class=e_td >-</td>
					</tr>\n";
    $mstring = $mstring."</table><br><br>\n";

    /*
    $help_text = "
    <table>
        <tr>
            <td style='line-height:150%'>
            - 상품조회 수치를 통해본 상품의 인기도입니다 <br>
            - 또한 조회수와 조회한 상품의 가격을 바탕으로 산출한 잠재매출입니다 약간은 허구적인 수치이나 1/100 로 환산하여 사이트의 정략적인 평가에 도움이 될수 있습니다 <br><br>
            </td>
        </tr>
    </table>
    ";*/
    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );



    $mstring .= HelpBox("셀러별 방문수", $help_text);

    return $mstring;
}
?>
