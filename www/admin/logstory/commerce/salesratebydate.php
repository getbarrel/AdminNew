<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");
include("../report/etcreferer.chart.php");

function ReportTable($vdate,$SelectReport=1){
    global $search_sdate, $search_edate;

    $pageview01 = 0;
    $sumvisit = 0;
    $sumucnt =0;
    $fordb = new forbizDatabase();
    $fordb2 = new forbizDatabase();
    $fordb3 = new forbizDatabase();
    $fordb4 = new forbizDatabase();


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
            $vdate = substr($vdate,0,6)."01";
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
        $nLoop = 24;
    }else if($SelectReport ==2){
        $nLoop = 7;
    }else if($SelectReport ==3){
        $nLoop = date("t", mktime(0, 0, 0, substr($vdate,4,2), substr($vdate,6,2), substr($vdate,0,4)));
    }else if($SelectReport ==4){
        $timestamp_search_sdate = mktime(0, 0, 0, substr($search_sdate,4,2), substr($search_sdate,6,2), substr($search_sdate,0,4));
        $timestamp_search_edate = mktime(0, 0, 0, substr($search_edate,4,2), substr($search_edate,6,2), substr($search_edate,0,4));
        $nLoop = ($timestamp_search_edate-$timestamp_search_sdate)/86400;
        //echo $nLoop;
    }

    if($SelectReport == 1){
        $sql = "Select vdate, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt from ".TBL_COMMERCE_SALESTACK." where   vdate = '$vdate' and step6 = 1 group by vdate order by vdate";
        $sql2 = "Select vdate, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt from ".TBL_COMMERCE_SALESTACK." where   vdate = '$vyesterday' and step6 = 1 group by vdate order by vdate";
        $sql3 = "Select vdate, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt from ".TBL_COMMERCE_SALESTACK." where   vdate = '$voneweekago' and step6 = 1 group by vdate order by vdate";


        $sql4 = "Select
				sum(case when vdate = '$vdate' then ncnt else 0 end) as today,
				sum(case when vdate = '$vyesterday' then ncnt else 0 end) as yesterday,
				sum(case when vdate = '$voneweekago' then ncnt else 0 end) as oneweeksago
				from ".TBL_LOGSTORY_VISITTIME."";

        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
        $title1 = "해당일";
        $title2 = "1일전";
        $title3 = "일주전";
    }else if($SelectReport == 2 || $SelectReport == 4){
        if($SelectReport == "4"){
            $vdate = $search_sdate;
            $vweekenddate = $search_edate;
        }

        $sql = "SELECT vdate, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt FROM ".TBL_COMMERCE_SALESTACK." c where vdate between '$vdate' and '$vweekenddate' and step6 = 1 group by vdate ";

        $sql2 = "Select
			sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4)))."' then ncnt else 0 end)";
        for($i=2;$i < $nLoop;$i++){
            $sql2 .= ", sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*$i)."' then ncnt else 0 end)";
        }
        /*
            sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*2)."' then ncnt else 0 end),
            sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*3)."' then ncnt else 0 end),
            sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*4)."' then ncnt else 0 end),
            sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*5)."' then ncnt else 0 end),
            sum(case when vdate = '".date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6)."' then ncnt else 0 end)
        */
        $sql2 .= " from ".TBL_LOGSTORY_VISITTIME." ";

        if($SelectReport == 2){
            $dateString = "주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
        }else if($SelectReport == 4){
            $s_week_num = date("w",mktime(0,0,0,substr($search_sdate,4,2),substr($search_sdate,6,2),substr($search_sdate,0,4)));
            $e_week_num = date("w",mktime(0,0,0,substr($search_edate,4,2),substr($search_edate,6,2),substr($search_edate,0,4)));
            $dateString = "기간별 : ".getNameOfWeekday($s_week_num,$search_sdate,"priodname")."~".getNameOfWeekday($e_week_num,$search_edate,"priodname");
        }

    }else if($SelectReport == 3){
        $sql = "SELECT c.vdate as vdate, sum(vsale) sales, sum(step6) as cnt, count(distinct ucode)as ucnt FROM ".TBL_COMMERCE_SALESTACK." c where vdate between '".substr($selected_date,0,6)."01' and '".substr($selected_date,0,6)."31' and step6 = 1 group by c.vdate ";

        $sql2 = "Select vdate, ncnt from ".TBL_LOGSTORY_VISITTIME." where vdate between '".substr($selected_date,0,6)."01' and '".substr($selected_date,0,6)."31' ";
        $dateString = "월간 : ".getNameOfWeekday(0,$vdate,"monthname");

    }






    $mstring = $mstring.TitleBar("구매율(일자별)",$dateString);
    if($SelectReport == 1){
        $fordb->query($sql);
        $fordb2->query($sql2);
        $fordb3->query($sql3);
        $fordb4->query($sql4);

        $mstring .= "<table cellpadding=3 cellspacing=0 width=100% class='list_table_box'  >\n";
        $mstring .= "<tr height=30  align=center style='font-weight:bold'><td class=s_td width=19%>날짜</td><td class=m_td width=27%>방문고객수</td><td class=m_td width=27% nowrap>구매자수</td><td class=e_td width=27% nowrap>구매율(%)</td></tr>\n";

//	if($fordb->total == 0){
//		$mstring .= "<tr height=50 bgcolor=#ffffff align=center><td colspan=5>결과값이 없습니다.</td></tr>\n";
//	}else{


        $fordb->fetch(0);
        $fordb2->fetch(0);
        $fordb3->fetch(0);
        $fordb4->fetch(0);


        if(returnZeroValue($fordb4->dt['today']) > 0){
            $salerate = number_format((returnZeroValue($fordb->dt['ucnt'])/returnZeroValue($fordb4->dt['today'])*100),2);
        }else{
            $salerate = 0;
        }
        //$salerate2 = number_format((returnZeroValue($fordb2->dt['ucnt'])/returnZeroValue($fordb4->dt['yesterday'])*100),2);
        if(returnZeroValue($fordb4->dt['oneweeksago']) > 0){
            $salerate3 = number_format((returnZeroValue($fordb3->dt['ucnt'])/returnZeroValue($fordb4->dt['oneweeksago'])*100),2);
        }else{
            $salerate3 = 0;
        }
        $i=0;
        $mstring .= "
			<tr height=30 id='Report$i'>
			<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=center nowrap>$title1 </td>
			<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".number_format(returnZeroValue($fordb4->dt['today']),0)."</td>
			<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".returnZeroValue($fordb->dt['ucnt'])."</td>
			<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".$salerate."</td>
			</tr>";
        $i = $i + 1;
        $mstring .= "
			<tr height=30 id='Report$i'>
			<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=center nowrap>$title2 </td>
			<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".BarchartView($fordb4->dt['today'],$fordb4->dt['yesterday'])."</td>
			<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".BarchartView(returnZeroValue($fordb->dt['ucnt']),returnZeroValue($fordb2->dt['ucnt']))."</td>
			<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".BarchartView(returnZeroValue($salerate),returnZeroValue($salerate2))."</td>
			</tr>";
        $i = $i + 1;
        $mstring .= "
			<tr height=30 id='Report$i'>
			<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=center nowrap>$title3 </td>
			<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".BarchartView(returnZeroValue($fordb4->dt['today']),returnZeroValue($fordb4->dt['oneweeksago']))."</td>
			<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".BarchartView(returnZeroValue($fordb->dt['ucnt']),returnZeroValue($fordb3->dt['ucnt']))."</td>
			<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".BarchartView(returnZeroValue($salerate),returnZeroValue($salerate3))."</td>
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

    }else if ($SelectReport == 2 || $SelectReport == 4){
        $fordb->query($sql);
        $fordb2->query($sql2);
        //echo $sql;

        $mstring .= "<table cellpadding=3 cellspacing=0 width=100% class='list_table_box'   >\n";
        $mstring .= "<tr height=30  align=center style='font-weight:bold'><td class=s_td width=19%>시간</td><td class=m_td width=27%>방문고객수</td><td class=m_td width=27% nowrap>구매자수</td><td class=e_td width=27% nowrap>구매율(%)</td></tr>\n";

        if($fordb->total == 0){
            $mstring .= "<tr class='list_box_td'  align=center><td colspan=4 height=50 >결과값이 없습니다.</td></tr>\n";
        }else{
            $j = 0;
            $fordb->fetch($j);
            $fordb2->fetch(0);
            for($i=0;$i < $nLoop;$i++){

                if($fordb->dt['vdate'] == date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*$i)){

                    $mstring .= "
				<tr height=30 id='Report$i'>
				<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=center nowrap>".getNameOfWeekday($i,$vdate,"datename")."</td>
				<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px'>".number_format(returnZeroValue($fordb2->dt[$i]),0)."</td>
				<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px'>".returnZeroValue($fordb->dt['ucnt'])."</td>
				<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px'>".($fordb2->dt[$i] > 0 ? number_format((returnZeroValue($fordb->dt['ucnt'])/returnZeroValue($fordb2->dt[$i])*100),2):0)."</td>
				</tr>";

                    $j = $j + 1;
                    $fordb->fetch($j);
                    $sumvisit = $sumvisit + $fordb2->dt[$i];
                    $sumucnt = $sumucnt + $fordb->dt['ucnt'];

                }else{
                    $mstring .= "
				<tr height=30 id='Report$i'>
				<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=center nowrap>".getNameOfWeekday($i,$vdate,"datename")." </td>
				<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px'>".number_format(returnZeroValue($fordb2->dt[$i]),0)."</td>
				<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px'>0</td>
				<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px'>0</td>
				</tr>";

                    $sumvisit = $sumvisit + $fordb2->dt[$i];
                }

            }

        }
        if($sumvisit == 0){
            $order_rate = 0;
        }else{
            $order_rate = number_format((returnZeroValue($sumucnt)/returnZeroValue($sumvisit)*100),2);
        }



        $mstring .= "<tr height=30  align=right style='font-weight:bold'>
	<td class=s_td align=center>합계</td>
	<td class=m_td style='padding-right:20px'>".number_format($sumvisit,0)."</td>
	<td class=m_td style='padding-right:20px'>".returnZeroValue($sumucnt)."</td>
	<td class=e_td style='padding-right:20px'>".$order_rate."</td>
	</tr>\n";
        $mstring .= "</table><br>\n";
    }else if ($SelectReport == 3){
        $fordb->query($sql);
        $fordb2->query($sql2);

        $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  class='list_table_box'  >\n";
        $mstring .= "<tr height=30  align=center style='font-weight:bold'><td class=s_td width=19%>시간</td><td class=m_td width=27%>방문고객수</td><td class=m_td width=27% nowrap>구매자수</td><td class=e_td width=27% nowrap>구매율(%)</td></tr>\n";

        if($fordb->total == 0){
            $mstring .= "<tr class='list_box_td'  align=center><td colspan=4 height=50 >결과값이 없습니다.</td></tr>\n";
        }else{
            $j = 0;
            $fordb->fetch($j);

            for($i=0;$i < $nLoop;$i++){
                $fordb2->fetch($i);
                if($fordb->dt['vdate'] == date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*$i)){
                    $mstring .= "
				<tr height=30  id='Report$i'>
				<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=center nowrap>".getNameOfWeekday($i,$vdate,"dayname")."</td>
				<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px'>".number_format(returnZeroValue($fordb2->dt['ncnt']),0)."</td>
				<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px'>".returnZeroValue($fordb->dt['ucnt'])."</td>
				<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px'>".number_format(returnZeroValue($fordb->dt['ucnt'])/returnZeroValue($fordb2->dt['ncnt'])*100,2)."</td>
				</tr>";

                    $j = $j + 1;
                    $fordb->fetch($j);
                    $sumvisit = $sumvisit + $fordb2->dt['ncnt'];
                    $sumucnt = $sumucnt + $fordb->dt['ucnt'];
                    $sumcnt = $sumcnt + $fordb->dt['cnt'];
                }else{
                    if($beforedate == $fordb2->dt['vdate']){
                        $visit_cnt = 0;
                    }else{
                        $visit_cnt = $fordb2->dt['ncnt'];
                    }
                    $sumvisit = $sumvisit + $fordb2->dt['ncnt'];//합계 수가 맞지 않아 분석한 후 추가 kbk 14/04/10
                    $mstring .= "
				<tr height=30 id='Report$i'>
				<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=center nowrap>".getNameOfWeekday($i,$vdate,"dayname")."</td>
				<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px'>".number_format(returnZeroValue($visit_cnt),0)."</td>
				<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px'>0</td>
				<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px'>0</td>
				</tr>";
                }

                $beforedate = $fordb2->dt['vdate'];
            }

        }

        if($sumvisit == 0){
            $order_rate = 0;
        }else{
            $order_rate = number_format((returnZeroValue($sumucnt)/returnZeroValue($sumvisit)*100),2);
        }
        $mstring .= "<tr height=30  align=right style='font-weight:bold'>
	<td class=s_td align=center>합계</td>
	<td class=m_td style='padding-right:20px'>".number_format($sumvisit,0)."</td>
	<td class=m_td style='padding-right:20px'>".number_format(returnZeroValue($sumucnt))."</td>
	<td class=e_td style='padding-right:20px'>".$order_rate."</td>
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
                - 쇼핑몰을 방문한 고객의 횟수와 구매율을 일자별로 보여주며 일 단위로 방문자수 대비 구매율 관련 내용을 요약 하여 이전 구매율과 비교해 간략하게 보여주는 리포트입니다.<br>
                - 방문고객 수 : 쇼핑몰의 총 방문자수를 이전 방문자수와 비교하여 해당 일의 총 방문자수 비율을 확인하실 수 있습니다.<br>
                - 구매자수 : 쇼핑몰의 총 구매자수와 이전 구매자수 대비 해당 일의 구매자 비율을 확인하실 수 있습니다.<br>
                - 구매율 : 쇼핑몰의 총 구매율과 이전 구매율 대비 해당 일의 구매율을 비교하여 확인하실 수 있습니다.
                <br>

                </td>
            </tr>
        </table>
        ";*/
    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );


    $mstring .= HelpBox("구매율(일자별)", $help_text);
    return $mstring;
}

if ($mode == "iframe"){
    echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";


    $ca = new Calendar();
    $ca->SelectReport = $SelectReport;
    $ca->LinkPage = 'salesratebydate.php';


    echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";

    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.ChangeCalenderView($SelectReport);</Script>";


}else{
    $p = new forbizReportPage();
    $p->TopNaviSelect = "step2";
    $p->forbizLeftMenu = commerce_munu('salesratebydate.php');//.text_button("#", "test","190").colorCirCleBoxStart("#efefef",190)."test<br>test<br>test<br>test<br>test<br>".colorCirCleBoxEnd("#efefef");
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->Navigation = "이커머스분석 > 매출종합분석 > 구매율(일자별)";
    $p->title = "구매율(일자별)";
    $p->PrintReportPage();
}
?>
