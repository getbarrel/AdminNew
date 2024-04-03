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
        $sql = "Select IFNULL(sum(step1),0) as step1, 
					IFNULL(sum(step2),0) as step2, 
					IFNULL(sum(step3),0) as step3, 
					IFNULL(sum(step4),0) as step4, 
					IFNULL(sum(step5),0) as step5, 
					IFNULL(sum(step6),0) as step6,
					sum(case when agent_type = 'W' then IFNULL(step1,0) else 0 end ) as web_step1, 
					sum(case when agent_type = 'W' then IFNULL(step2,0) else 0 end ) as web_step2, 
					sum(case when agent_type = 'W' then IFNULL(step3,0) else 0 end ) as web_step3, 
					sum(case when agent_type = 'W' then IFNULL(step4,0) else 0 end ) as web_step4, 
					sum(case when agent_type = 'W' then IFNULL(step5,0) else 0 end ) as web_step5, 
					sum(case when agent_type = 'W' then IFNULL(step6,0) else 0 end ) as web_step6,
					sum(case when agent_type = 'M' then IFNULL(step1,0) else 0 end ) as mobile_step1, 
					sum(case when agent_type = 'M' then IFNULL(step2,0) else 0 end ) as mobile_step2, 
					sum(case when agent_type = 'M' then IFNULL(step3,0) else 0 end ) as mobile_step3, 
					sum(case when agent_type = 'M' then IFNULL(step4,0) else 0 end ) as mobile_step4, 
					sum(case when agent_type = 'M' then IFNULL(step5,0) else 0 end ) as mobile_step5, 
					sum(case when agent_type = 'M' then IFNULL(step6,0) else 0 end ) as mobile_step6
		from ".TBL_LOGSTORY_MEMBERREG_STACK." where vdate = '$vdate' group by vdate";
        $sql2 = "Select sum(nview_cnt) as cnt  from ".TBL_COMMERCE_VIEWINGVIEW." where vdate = '$vdate' group by vdate";

        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
    }else if($SelectReport == 2 || $SelectReport == 4){
        if($SelectReport == "4"){
            $vdate = $search_sdate;
            $vweekenddate = $search_edate;
        }

        $sql = "Select IFNULL(sum(step1),0) as step1, 
					IFNULL(sum(step2),0) as step2, 
					IFNULL(sum(step3),0) as step3, 
					IFNULL(sum(step4),0) as step4, 
					IFNULL(sum(step5),0) as step5, 
					IFNULL(sum(step6),0) as step6,
					sum(case when agent_type = 'W' then IFNULL(step1,0) else 0 end ) as web_step1, 
					sum(case when agent_type = 'W' then IFNULL(step2,0) else 0 end ) as web_step2, 
					sum(case when agent_type = 'W' then IFNULL(step3,0) else 0 end ) as web_step3, 
					sum(case when agent_type = 'W' then IFNULL(step4,0) else 0 end ) as web_step4, 
					sum(case when agent_type = 'W' then IFNULL(step5,0) else 0 end ) as web_step5, 
					sum(case when agent_type = 'W' then IFNULL(step6,0) else 0 end ) as web_step6,
					sum(case when agent_type = 'M' then IFNULL(step1,0) else 0 end ) as mobile_step1, 
					sum(case when agent_type = 'M' then IFNULL(step2,0) else 0 end ) as mobile_step2, 
					sum(case when agent_type = 'M' then IFNULL(step3,0) else 0 end ) as mobile_step3, 
					sum(case when agent_type = 'M' then IFNULL(step4,0) else 0 end ) as mobile_step4, 
					sum(case when agent_type = 'M' then IFNULL(step5,0) else 0 end ) as mobile_step5, 
					sum(case when agent_type = 'M' then IFNULL(step6,0) else 0 end ) as mobile_step6
		from ".TBL_LOGSTORY_MEMBERREG_STACK." where vdate between '$vdate' and '$vweekenddate' ";

        $sql2 = "Select sum(nview_cnt) as cnt  from ".TBL_COMMERCE_VIEWINGVIEW." where vdate between '$vdate' and '$vweekenddate' ";

        if($SelectReport == 2){
            $dateString = "주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
        }else if($SelectReport == 4){
            $s_week_num = date("w",mktime(0,0,0,substr($search_sdate,4,2),substr($search_sdate,6,2),substr($search_sdate,0,4)));
            $e_week_num = date("w",mktime(0,0,0,substr($search_edate,4,2),substr($search_edate,6,2),substr($search_edate,0,4)));
            $dateString = "기간별 : ".getNameOfWeekday($s_week_num,$search_sdate,"priodname")."~".getNameOfWeekday($e_week_num,$search_edate,"priodname");
        }
    }else if($SelectReport == 3){
        $sql = "Select IFNULL(sum(step1),0) as step1, 
					IFNULL(sum(step2),0) as step2, 
					IFNULL(sum(step3),0) as step3, 
					IFNULL(sum(step4),0) as step4, 
					IFNULL(sum(step5),0) as step5, 
					IFNULL(sum(step6),0) as step6,
					sum(case when agent_type = 'W' then IFNULL(step1,0) else 0 end ) as web_step1, 
					sum(case when agent_type = 'W' then IFNULL(step2,0) else 0 end ) as web_step2, 
					sum(case when agent_type = 'W' then IFNULL(step3,0) else 0 end ) as web_step3, 
					sum(case when agent_type = 'W' then IFNULL(step4,0) else 0 end ) as web_step4, 
					sum(case when agent_type = 'W' then IFNULL(step5,0) else 0 end ) as web_step5, 
					sum(case when agent_type = 'W' then IFNULL(step6,0) else 0 end ) as web_step6,
					sum(case when agent_type = 'M' then IFNULL(step1,0) else 0 end ) as mobile_step1, 
					sum(case when agent_type = 'M' then IFNULL(step2,0) else 0 end ) as mobile_step2, 
					sum(case when agent_type = 'M' then IFNULL(step3,0) else 0 end ) as mobile_step3, 
					sum(case when agent_type = 'M' then IFNULL(step4,0) else 0 end ) as mobile_step4, 
					sum(case when agent_type = 'M' then IFNULL(step5,0) else 0 end ) as mobile_step5, 
					sum(case when agent_type = 'M' then IFNULL(step6,0) else 0 end ) as mobile_step6
		from ".TBL_LOGSTORY_MEMBERREG_STACK." where vdate LIKE '".substr($vdate,0,6)."%' ";

        $sql2 = "Select sum(nview_cnt) as cnt  from ".TBL_COMMERCE_VIEWINGVIEW." where vdate LIKE '".substr($vdate,0,6)."%'";
        $dateString = getNameOfWeekday(0,$vdate,"monthname");
    }

    //echo $sql;
    $fordb->query($sql);
    $fordb2->query($sql2);


    $mstring = $mstring.TitleBar("회원가입단계분석",$dateString);


    $mstring = $mstring."<table cellpadding=3 cellspacing=0 width=100%  class='list_table_box' >\n";
    $mstring = $mstring."<col width='20%'>
						<col width='10%'>
						<col width='10%'>
						<col width='10%'>
						<col width='10%'>
						<col width='10%'>
						<col width='10%'>";
    $mstring = $mstring."<tr height=30  align=center style='font-weight:bold'>
			<td class=s_td width=20% rowspan=2>회원가입단계</td>
			<td class=m_td colspan=3  nowrap>PC(웹)</td>
			<td class=m_td colspan=3  nowrap>모바일</td> 
			<td class=m_td colspan=3  nowrap>전체</td> 
			</tr>\n";
    $mstring = $mstring."<tr height=30  align=center style='font-weight:bold'>
			
			<td class=m_td nowrap>진입횟수</td>
			<td class=m_td nowrap>이탈횟수</td>
			<td class=e_td nowrap>이탈율(%)</td>

			<td class=m_td nowrap>진입횟수</td>
			<td class=m_td nowrap>이탈횟수</td>
			<td class=e_td nowrap>이탈율(%)</td>

			<td class=m_td nowrap>진입횟수</td>
			<td class=m_td nowrap>이탈횟수</td>
			<td class=e_td nowrap>이탈율(%)</td>
			</tr>\n";

//	for($i=0;$i<$fordb->total;$i++){
    $fordb->fetch(0);
    $fordb2->fetch(0);
    $i = 0;


    $i = $i + 1;

    $mstring .= "<tr height=30  id='Report$i'>
		<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >회원선택</td>
		<td class='list_box_td point'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".($fordb->dt['web_step1'] ? $fordb->dt['web_step1']:"0")."</td>
		<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($fordb->dt['web_step1']-$fordb->dt['web_step2'])."</td>
		<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".number_format(CheckDivision(($fordb->dt['web_step1']-$fordb->dt['web_step2']),$fordb->dt['web_step1'])*100,1)."</td>

		<td class='list_box_td point'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".($fordb->dt['mobile_step1'] ? $fordb->dt['mobile_step1']:"0")."</td>
		<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($fordb->dt['mobile_step1']-$fordb->dt['mobile_step2'])."</td>
		<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".number_format(CheckDivision(($fordb->dt['mobile_step1']-$fordb->dt['mobile_step2']),$fordb->dt['mobile_step1'])*100,1)."</td>

		<td class='list_box_td point'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".($fordb->dt['step1'] ? $fordb->dt['step1']:"0")."</td>
		<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($fordb->dt['step1']-$fordb->dt['step2'])."</td>
		<td class='list_box_td'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".number_format(CheckDivision(($fordb->dt['step1']-$fordb->dt['step2']),$fordb->dt['step1'])*100,1)."</td>
		</tr>";

    $i = $i + 1;

    $mstring .= "<tr height=30  id='Report$i'>
		<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">약관동의</td>
		<td class='list_box_td point'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".($fordb->dt['web_step2'] ? $fordb->dt['web_step2']:"0")."</td>
		<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".($fordb->dt['web_step2']-$fordb->dt['web_step3'])."</td>
		<td class='list_box_td'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(CheckDivision(($fordb->dt['web_step2']-$fordb->dt['web_step6']),$fordb->dt['web_step2'])*100,1)."</td>

		<td class='list_box_td point'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".($fordb->dt['mobile_step2'] ? $fordb->dt['mobile_step2']:"0")."</td>
		<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".($fordb->dt['mobile_step2']-$fordb->dt['mobile_step3'])."</td>
		<td class='list_box_td'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(CheckDivision(($fordb->dt['mobile_step2']-$fordb->dt['mobile_step6']),$fordb->dt['mobile_step2'])*100,1)."</td>

		<td class='list_box_td point'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".($fordb->dt['step2'] ? $fordb->dt['step2']:"0")."</td>
		<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".($fordb->dt['step2']-$fordb->dt['step3'])."</td>
		<td class='list_box_td'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(CheckDivision(($fordb->dt['step2']-$fordb->dt['step6']),$fordb->dt['step2'])*100,1)."</td>
		</tr>";

    $i = $i + 1;

    $mstring .= "<tr height=30  id='Report$i'>
		<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">정보입력</td>
		<td class='list_box_td point'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($fordb->dt['web_step3'] ? $fordb->dt['web_step3']:"0")."</td>
		<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($fordb->dt['web_step3']-$fordb->dt['web_step6'])."</td>
		<td class='list_box_td'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(CheckDivision(($fordb->dt['web_step2']-$fordb->dt['web_step6']),$fordb->dt['web_step2'])*100,1)."</td>

		<td class='list_box_td point'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($fordb->dt['mobile_step3'] ? $fordb->dt['mobile_step3']:"0")."</td>
		<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($fordb->dt['mobile_step3']-$fordb->dt['mobile_step6'])."</td>
		<td class='list_box_td'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(CheckDivision(($fordb->dt['mobile_step2']-$fordb->dt['mobile_step6']),$fordb->dt['mobile_step2'])*100,1)."</td>

		<td class='list_box_td point'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($fordb->dt['step3'] ? $fordb->dt['step3']:"0")."</td>
		<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($fordb->dt['step3']-$fordb->dt['step6'])."</td>
		<td class='list_box_td'  onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".number_format(CheckDivision(($fordb->dt['step2']-$fordb->dt['step6']),$fordb->dt['step2'])*100,1)."</td>
		</tr>";

    $i = $i + 1;


    $mstring .= "<tr height=30  >
		<td class='list_box_td list_bg_gray' >가입완료</td>
		<td class='list_box_td point'>".($fordb->dt['web_step6'] ? $fordb->dt['web_step6']:"0")."</td>
		<td class='list_box_td list_bg_gray' >-</td>
		<td class='list_box_td'>-</td>

		<td class='list_box_td point'>".($fordb->dt['mobile_step6'] ? $fordb->dt['mobile_step6']:"0")."</td>
		<td class='list_box_td list_bg_gray' >-</td>
		<td class='list_box_td'>-</td>

		<td class='list_box_td point'>".($fordb->dt['step6'] ? $fordb->dt['step6']:"0")."</td>
		<td class='list_box_td list_bg_gray' >-</td>
		<td class='list_box_td'>-</td>
		</tr>\n";

    //	$exitcnt = $pageview01 + returnZeroValue($fordb->dt['visit_cnt']);


//	}
    $mstring = $mstring."</table>\n";
//	$mstring = $mstring."<table cellpadding=3 cellspacing=0 width=745  >\n";
//	if ($pageview01 == 0){
//		$mstring = $mstring."<tr height=50 bgcolor=#ffffff align=center><td colspan=5>결과값이 없습니다.</td></tr>\n";
//	}
//	$mstring = $mstring."<tr height=2 bgcolor=#ffffff align=center><td colspan=5 width=190></td></tr>\n";
//	$mstring = $mstring."<tr height=30  align=center><td width=50 class=s_td width=30 colspan=2>합계</td><td class=e_td width=190>".$pageview01."</td></tr>\n";
//	$mstring = $mstring."</table>\n";
    /*
        $help_text = "
        <table>
            <tr>
                <td style='line-height:150%'>
                - 방문 고객의 회원가입 단계에서의 이탈한 위치 및 횟수를 확인하실 수 있습니다<br>
    (※ 분석 리포트 데이터 중 마이너스(-)로 표기 된 데이터는 이탈 없이 최종 구매 단계 또는 회원가입 단계를 완료한 데이터입니다.)

    <br>

                </td>
            </tr>
        </table>
        ";*/
    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );


    $mstring .= HelpBox("회원가입단계분석", $help_text);
    return $mstring;
}

if ($mode == "iframe"){
    echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";


    $ca = new Calendar();
    $ca->LinkPage = 'memberregstep.php';

    echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";
    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.ChangeCalenderView($SelectReport);</Script>";

}else{
    $p = new forbizReportPage();
    $p->TopNaviSelect = "step2";
    $p->forbizLeftMenu = commerce_munu('memberregstep.php');
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->Navigation = "이커머스분석 > 구매/회원단계 분석 > 회원가입단계분석";
    $p->title = "회원가입단계분석";
    $p->PrintReportPage();
}
?>
