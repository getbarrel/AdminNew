<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");
include("../report/etcreferer.chart.php");


function ReportTable($vdate,$SelectReport=1){
    global $search_sdate, $search_edate;

    $pageview01 = 0;
    $mstring = "";
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
    }else{
        if($SelectReport ==3){
            $vdate = $vdate."01";
        }
        $vweekenddate = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))+60*60*24*6);
        $vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24);
        $voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2),substr($vdate,0,4))-60*60*24*7);
    }

    if($SelectReport == 1){
        $sql = "Select vdate, sum(step1) as step1, sum(step2) as step2, sum(step3) as step3, sum(step4) as step4, sum(step5) as step5, sum(step6) as step6 from ".TBL_COMMERCE_SALESTACK." where vdate = '$vdate' group by vdate";
        $sql2 = "Select sum(nview_cnt) as cnt  from ".TBL_COMMERCE_VIEWINGVIEW." where vdate = '$vdate' group by vdate";
        $sql3 = "SELECT
			sum(case when step1 = 1 then vsale else 0 end) as step1_sale,
			sum(case when step2 = 1 then vsale else 0 end) as step2_sale,
			sum(case when step3 = 1 then vsale else 0 end) as step3_sale,
			sum(case when step4 = 1 then vsale else 0 end) as step4_sale,
			sum(case when step5 = 1 then vsale else 0 end) as step5_sale,
			sum(case when step6 = 1 then vsale else 0 end) as step6_sale
			FROM ".TBL_COMMERCE_SALESTACK."
			where vdate ='$vdate'";

        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
    }else if($SelectReport == 2 || $SelectReport == 4){
        if($SelectReport == "4"){
            $vdate = $search_sdate;
            $vweekenddate = $search_edate;
        }

        $sql = "Select sum(step1) as step1, sum(step2) as step2, sum(step3) as step3, sum(step4) as step4, sum(step5) as step5, sum(step6) as step6 from ".TBL_COMMERCE_SALESTACK." where vdate between '$vdate' and '$vweekenddate' ";

        $sql2 = "Select sum(nview_cnt) as cnt  from ".TBL_COMMERCE_VIEWINGVIEW." where vdate between '$vdate' and '$vweekenddate' ";

        $sql3 = "SELECT
			sum(case when step1 = 1 then vsale else 0 end) as step1_sale,
			sum(case when step2 = 1 then vsale else 0 end) as step2_sale,
			sum(case when step3 = 1 then vsale else 0 end) as step3_sale,
			sum(case when step4 = 1 then vsale else 0 end) as step4_sale,
			sum(case when step5 = 1 then vsale else 0 end) as step5_sale,
			sum(case when step6 = 1 then vsale else 0 end) as step6_sale
			FROM ".TBL_COMMERCE_SALESTACK."
			where vdate between '$vdate' and '$vweekenddate'";

        if($SelectReport == 2){
            $dateString = "주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
        }else if($SelectReport == 4){
            $s_week_num = date("w",mktime(0,0,0,substr($search_sdate,4,2),substr($search_sdate,6,2),substr($search_sdate,0,4)));
            $e_week_num = date("w",mktime(0,0,0,substr($search_edate,4,2),substr($search_edate,6,2),substr($search_edate,0,4)));
            $dateString = "기간별 : ".getNameOfWeekday($s_week_num,$search_sdate,"priodname")."~".getNameOfWeekday($e_week_num,$search_edate,"priodname");
        }
    }else if($SelectReport == 3){
        $sql = "Select sum(step1) as step1, sum(step2) as step2, sum(step3) as step3, sum(step4) as step4, sum(step5) as step5, sum(step6) as step6 from ".TBL_COMMERCE_SALESTACK." where vdate LIKE '".substr($vdate,0,6)."%' ";

        $sql2 = "Select sum(nview_cnt) as cnt  from ".TBL_COMMERCE_VIEWINGVIEW." where vdate LIKE '".substr($vdate,0,6)."%'";
        $dateString = getNameOfWeekday(0,$vdate,"monthname");

        $sql3 = "SELECT
			sum(case when step1 = 1 then vsale else 0 end) as step1_sale,
			sum(case when step2 = 1 then vsale else 0 end) as step2_sale,
			sum(case when step3 = 1 then vsale else 0 end) as step3_sale,
			sum(case when step4 = 1 then vsale else 0 end) as step4_sale,
			sum(case when step5 = 1 then vsale else 0 end) as step5_sale,
			sum(case when step6 = 1 then vsale else 0 end) as step6_sale
			FROM ".TBL_COMMERCE_SALESTACK."
			where vdate LIKE '".substr($vdate,0,6)."%'";
    }

    //echo $sql;
    if($sql){
        $fordb->query($sql);
        $fordb->fetch(0);
    }
    if($sql2){
        $fordb2->query($sql2);
        $fordb2->fetch(0);
    }
    if($sql3){
        $fordb3->query($sql3);
        $fordb3->fetch(0);
    }




    $mstring = $mstring.TitleBar("구매단계별 이탈매출",$dateString);
    /*	$mstring = $mstring."<table cellpadding=0 cellspacing=0 width=100% border=0>\n";
        $mstring = $mstring."<tr  align=center><td colspan=3 style='padding-bottom:10px;'><img src='escapesalebystep.chart.php?vdate=".$vdate."&SelectReport=".$SelectReport."'></td></tr>\n";
        $mstring = $mstring."<tr  align=center><td colspan=3 ></td></tr>\n";
        $mstring = $mstring."</table>";
    */
    $mstring = $mstring."<table cellpadding=3 cellspacing=0 width=100%  class='list_table_box' >\n";
    $mstring = $mstring."<tr height=30  align=center style='font-weight:bold'><td class=s_td width=30%>구매단계</td><td class=m_td width=40% nowrap>예상매출</td><td class=e_td width=40% nowrap>이탈매출</td></tr>\n";
//	for($i=0;$i<$fordb->total;$i++){

    $i = 0;
    /*
            $mstring .= "<tr height=30 bgcolor=#ffffff >
            <td bgcolor=#efefef id='Report$i' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:20px'>상품조회</td>
            <td bgcolor=#ffffff align=center id='Report$i' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".$fordb2->dt['cnt']."</td>
            <td bgcolor=#efefef align=center id='Report$i' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($fordb2->dt['cnt']-$fordb->dt['step1'])."</td>
            <td bgcolor=#ffffff align=center id='Report$i' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(($fordb2->dt['cnt']-$fordb->dt['step1'])/$fordb2->dt['cnt']*100,1)."</td>
            </tr>";

            $i = $i + 1;
    */
    $mstring .= "<tr height=30 id='Report$i'>
		<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\"  >쇼핑카트</td>
		<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".number_format($fordb3->dt['step1_sale'],0)." 원</td>
		<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb3->dt['step1_sale']-$fordb3->dt['step2_sale'],0)." 원</td>
		</tr>";

    $i = $i + 1;

    $mstring .= "<tr height=30 id='Report$i'>
		<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">결제정보입력</td>
		<td class='list_box_td'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format($fordb3->dt['step2_sale'],0)." 원</td>
		<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".number_format($fordb3->dt['step2_sale']-$fordb3->dt['step3_sale'],0)." 원</td>
		</tr>";
    $i = $i + 1;
    $mstring .= "<tr height=30 id='Report$i'>
		<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >결제정보확인</td>
		<td class='list_box_td'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".number_format($fordb3->dt['step3_sale'],0)." 원</td>
		<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".number_format($fordb3->dt['step3_sale']-$fordb3->dt['step6_sale'],0)." 원</td>
		</tr>\n";
    $i = $i + 1;
    /*	$mstring .= "<tr height=30 class='list_box_td'  >
        <td class='list_box_td list_bg_gray'  id='Report$i' onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-left:20px'>결제완료</td>
        <td class='list_box_td'  align=center id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".$fordb->dt['step6']."&nbsp;</td>
        <td class='list_box_td list_bg_gray'  align=center id='Report$i' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" width=390  wrap>-</td>
        </tr>\n";
    */

    $mstring .= "<tr height=30  >
		<td class='list_box_td list_bg_gray'>결제완료</td>
		<td class='list_box_td' >".number_format($fordb3->dt['step6_sale'],0)." 원</td>
		<td class='list_box_td list_bg_gray' >-</td>
		</tr>\n";

    //	$exitcnt = $pageview01 + returnZeroValue($fordb->dt['visit_cnt']);


//	}
    $mstring = $mstring."</table>\n";
//	$mstring = $mstring."<table cellpadding=3 cellspacing=0 width=745  >\n";
//	if ($pageview01 == 0){
//		$mstring = $mstring."<tr height=50 class='list_box_td'  align=center><td colspan=5>결과값이 없습니다.</td></tr>\n";
//	}
//	$mstring = $mstring."<tr height=2 class='list_box_td'  align=center><td colspan=5 width=190></td></tr>\n";
//	$mstring = $mstring."<tr height=30  align=center><td width=50 class=s_td width=30 colspan=2>합계</td><td class=e_td width=190>".$pageview01."</td></tr>\n";
//	$mstring = $mstring."</table>\n";
    /*
        $help_text = "
        <table>
            <tr>
                <td style='line-height:150%'>
                - 방문 고객이 쇼핑몰 상품 구매절차 중 이탈했을 때 발생하는 이탈 매출액과 예상 매출액을 확인하실 수 있습니다.<br>
    (※ 분석 리포트 데이터 중 마이너스(-)로 표기 된 데이터는 이탈 없이 최종 구매 단계 또는 회원가입 단계를 완료한 데이터입니다.)

    <br>

                </td>
            </tr>
        </table>
        ";*/
    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );


    $mstring .= HelpBox("구매단계별 이탈매출", $help_text);
    return $mstring;
}

if ($mode == "iframe"){
    echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";


    $ca = new Calendar();
    $ca->LinkPage = 'escapesalebystep.php';

    echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";
    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.ChangeCalenderView($SelectReport);</Script>";

}else{
    $p = new forbizReportPage();
    $p->TopNaviSelect = "step2";
    $p->forbizLeftMenu = commerce_munu('escapesalebystep.php');
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->Navigation = "이커머스분석 > 구매/회원단계 분석 > 구매단계별 이탈매출";
    $p->title = "구매단계별 이탈매출";
    $p->PrintReportPage();
}
?>
