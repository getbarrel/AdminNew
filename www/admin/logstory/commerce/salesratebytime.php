<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");
include("../report/etcreferer.chart.php");

function ReportTable($vdate,$SelectReport=1){
    global $search_sdate, $search_edate;

    $fordb = new forbizDatabase();
    $fordb2 = new forbizDatabase();
    $fordb3 = new forbizDatabase();

    $sumvisit = 0;
    $sumucnt = 0;
    $sumcnt = 0;

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
        $selected_date = date("Ymd", time());
    }else{
        if($SelectReport ==3){
            $vdate = $vdate."01";
        }
        $vweekenddate = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6);
        $vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
        $voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
        $vtwoweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*14);
        $vfourweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*28);
        $vonemonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)));
        $vtwomonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-2,substr($vdate,0,4)));
        $selected_date = date("Ymd", mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)));
    }

    if($SelectReport == 1){

        $sql = "SELECT t.vtime as vtime, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt
		FROM ".TBL_LOGSTORY_TIME." t left join ".TBL_COMMERCE_SALESTACK." c on t.vtime = c.vtime where vdate = '$vdate' and step6 = 1
		group by t.vtime ";

        $sql2 = "Select nh00, nh01, nh02, nh03, nh04, nh05, nh06, nh07, nh08, nh09, nh10, nh11, nh12, nh13, nh14, nh15, nh16, nh17, nh18, nh19, nh20, nh21, nh22, nh23 from ".TBL_LOGSTORY_VISITTIME." where vdate = '$vdate'";

        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");

    }else if($SelectReport == 2 || $SelectReport == 4){
        if($SelectReport == "4"){
            $vdate = $search_sdate;
            $vweekenddate = $search_edate;
        }

        $sql = "SELECT t.vtime as vtime, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt FROM ".TBL_LOGSTORY_TIME." t left join ".TBL_COMMERCE_SALESTACK." c on t.vtime = c.vtime where vdate between '$vdate' and '$vweekenddate' and step6 = 1 group by t.vtime ";

        $sql2 = "Select sum(nh00), sum(nh01), sum(nh02), sum(nh03), sum(nh04), sum(nh05), sum(nh06), sum(nh07), sum(nh08), sum(nh09), sum(nh10), sum(nh11), sum(nh12), sum(nh13), sum(nh14), sum(nh15), sum(nh16), sum(nh17), sum(nh18), sum(nh19), sum(nh20), sum(nh21), sum(nh22), sum(nh23) from ".TBL_LOGSTORY_VISITTIME." where vdate between '$vdate' and '$vweekenddate'";

        if($SelectReport == 2){
            $dateString = "주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
        }else if($SelectReport == 4){
            $s_week_num = date("w",mktime(0,0,0,substr($search_sdate,4,2),substr($search_sdate,6,2),substr($search_sdate,0,4)));
            $e_week_num = date("w",mktime(0,0,0,substr($search_edate,4,2),substr($search_edate,6,2),substr($search_edate,0,4)));
            $dateString = "기간별 : ".getNameOfWeekday($s_week_num,$search_sdate,"priodname")."~".getNameOfWeekday($e_week_num,$search_edate,"priodname");
        }

    }else if($SelectReport == 3){
        $sql = "SELECT t.vtime as vtime, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt FROM ".TBL_LOGSTORY_TIME." t left join ".TBL_COMMERCE_SALESTACK." c on t.vtime = c.vtime where vdate between '".substr($selected_date,0,7)."-01 00:00:00' and '".substr($selected_date,0,7)."-31 23:59:59' and step6 = 1 group by t.vtime ";

        $sql2 = "Select sum(nh00), sum(nh01), sum(nh02), sum(nh03), sum(nh04), sum(nh05), sum(nh06), sum(nh07), sum(nh08), sum(nh09), sum(nh10), sum(nh11), sum(nh12), sum(nh13), sum(nh14), sum(nh15), sum(nh16), sum(nh17), sum(nh18), sum(nh19), sum(nh20), sum(nh21), sum(nh22), sum(nh23) from ".TBL_LOGSTORY_VISITTIME." where vdate between '".substr($selected_date,0,6)."01' and '".substr($selected_date,0,6)."31' ";

        $dateString = getNameOfWeekday(0,$vdate,"monthname");

    }

//	echo $sql."\n";

    $fordb->query($sql);
    $fordb2->query($sql2);


    $mstring = $mstring.TitleBar("구매율(시간대)",$dateString);
//	$mstring .= "<table cellpadding=0 cellspacing=0 width=745 border=0 >\n";
//	$mstring .= "<tr  align=center><td colspan=3 style='padding-bottom:10px;'>".etcrefererGraph($vdate,$SelectReport)."</td></tr>\n";
//	$mstring .= "<tr  align=center><td colspan=3 ></td></tr>\n";
//	$mstring .= "</table>";
    /*
        $mstring .= "<table cellpadding=3 cellspacing=0 width=745  >\n";
        $mstring .= "<tr height=30  align=center><td width=150 class=s_td width=30>항목</td><td class=m_td width=200>페이지 명</td><td class=e_td width=50>페이지뷰</td></tr>\n";
        $mstring .= "<tr height=30  class='list_box_td' >
            <td class='list_box_td list_bg_gray'  align=center id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">최다 요청</td>
            <td align=right id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">오후 2:00:00 ∼ 오후 3:00:00</td>
            <td class='list_box_td list_bg_gray'   align=right id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">543</td>
            </tr>\n";
        $mstring .= "<tr height=30  class='list_box_td' >
            <td class='list_box_td list_bg_gray'  align=center id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">최소 요청</td>
            <td align=right id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">오후 2:00:00 ∼ 오후 3:00:00</td>
            <td class='list_box_td list_bg_gray'   align=right id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">33</td>
            </tr>\n";
        $mstring .= "</table><br>";
    */

    $mstring .= "<table cellpadding=3 cellspacing=0 width=100% class='list_table_box'   >\n";
    $mstring .= "<tr height=30  align=center style='font-weight:bold'><td class=s_td width=10%>시간</td><td class=m_td width=30%>방문고객수</td><td class=m_td width=30% nowrap>구매자수</td><td class=e_td width=30% nowrap>구매율(%)</td></tr>\n";

//	if($fordb->total == 0){
//		$mstring .= "<tr height=50 class='list_box_td'  align=center><td colspan=5>결과값이 없습니다.</td></tr>\n";
//	}else{



//	$fordb2->fetch(0);
//	$fordb3->fetch(0);
    $j = 0;
    $fordb->fetch($j);
    $fordb2->fetch();

    for($i=0;$i <= 23;$i++){

        if((!empty($fordb->dt['vtime']) && $fordb->dt['vtime']) == $i){
            $mstring .= "
				<tr height=30 id='Report$i'>
				<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=center nowrap>$i </td>
				<td class='list_box_td point'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px'>".returnZeroValue($fordb2->dt[$i])."</td>
				<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px'>".returnZeroValue($fordb->dt['ucnt'])."</td>
				<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px'>".number_format(returnZeroValue(($fordb->dt['ucnt'] == "" ? 0:$fordb->dt['ucnt']/$fordb2->dt[$i])*100),2)." </td>
				</tr>";
            $sumvisit = $sumvisit + $fordb2->dt[$i];
            $sumucnt = $sumucnt + $fordb->dt['ucnt'];
            $sumcnt = $sumcnt + $fordb->dt['cnt'];
            $j = $j + 1;
            $fordb->fetch($j);
        }else{
            $mstring .= "
				<tr height=30 id='Report$i'>
				<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=center nowrap> $i </td>
				<td class='list_box_td point'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px' >".returnZeroValue($fordb2->dt[$i])." </td>
				<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px't>0</td>
				<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px'>0.00 </td>
				</tr>";
            $sumvisit = $sumvisit + $fordb2->dt[$i];
        }
    }

//	}
    $mstring .= "<tr height=30  align=center>
	<td class=s_td align=center>합계</td>
	<td class=m_td style='padding-right:20px'>".number_format($sumvisit,0)."</td>
	<td class=m_td style='padding-right:20px'>".number_format($sumucnt)."</td>
	<td class=e_td style='padding-right:20px'>".number_format((($sumcnt == "" ? "0":$sumcnt/$sumvisit)*100),2)."</td>
	</tr>\n";
    $mstring .= "</table>\n";
    $mstring .= "<table cellpadding=3 cellspacing=0 width=745  >\n";
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
                - 시간대별 방문자수 대비 구매자 비율을 나타내는 리포트입니다.<br>

                </td>
            </tr>
        </table>
        ";*/
    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );


    $mstring .= HelpBox("구매율(시간대)", $help_text);
    return $mstring;
}

if ($mode == "iframe"){
    echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";


    $ca = new Calendar();
    $ca->SelectReport = $SelectReport;
    $ca->LinkPage = 'salesratebytime.php';


    echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";

    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.ChangeCalenderView($SelectReport);</Script>";


}else{
    $p = new forbizReportPage();
    $p->TopNaviSelect = "step2";
    $p->forbizLeftMenu = commerce_munu('salesratebytime.php');//.text_button("#", "test","190").colorCirCleBoxStart("#efefef",190)."test<br>test<br>test<br>test<br>test<br>".colorCirCleBoxEnd("#efefef");
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->Navigation = "이커머스분석 > 매출종합분석 > 구매율(시간대)";
    $p->title = "구매율(시간대)";
    $p->PrintReportPage();
}
?>
