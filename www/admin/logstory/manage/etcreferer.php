<?php
include("../class/reportpage.class");
include("../report/etcreferer.chart.php");

function ReportTable($vdate,$SelectReport=1){
    $pageview01 = 0;
    $fordb = new forbizDatabase();
    if($SelectReport == ""){
        $SelectReport = 1;
    }else if($SelectReport == "4"){
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
        $sql = "Select visit_cnt, p.vetcreferer_id, p.vetcreferer_url from  ".TBL_LOGSTORY_ETCREFERERINFO." p,  ".TBL_LOGSTORY_BYETCREFERER." b where b.vdate = '$vdate' and p.vetcreferer_id = b.vetcreferer_id order by visit_cnt desc LIMIT 0,50";
    }else if($SelectReport == 2){
        $sql = "Select sum(visit_cnt) as visit_cnt, p.vetcreferer_id, p.vetcreferer_url from  ".TBL_LOGSTORY_ETCREFERERINFO." p,  ".TBL_LOGSTORY_BYETCREFERER." b where b.vdate between '$vdate' and '$vweekenddate' and p.vetcreferer_id = b.vetcreferer_id group by p.vetcreferer_id, vetcreferer_url order by visit_cnt desc LIMIT 0,50";
    }else if($SelectReport == 3){
        $sql = "Select sum(visit_cnt) as visit_cnt, p.vetcreferer_id, p.vetcreferer_url from  ".TBL_LOGSTORY_ETCREFERERINFO." p,  ".TBL_LOGSTORY_BYETCREFERER." b where b.vdate LIKE '".substr($vdate,0,6)."%' and p.vetcreferer_id = b.vetcreferer_id group by p.vetcreferer_id, vetcreferer_url order by visit_cnt desc LIMIT 0,50";
    }

    $fordb->query($sql);

    $mstring = $mstring.TitleBar("기타 URL 관리");
    /*	$mstring = $mstring."<table cellpadding=0 cellspacing=0 width=615 border=0 >\n";
        $mstring = $mstring."<tr  align=center><td colspan=3 style='padding-bottom:10px;'>".etcrefererGraph($vdate,$SelectReport)."</td></tr>\n";
        $mstring = $mstring."<tr  align=center><td colspan=3 ></td></tr>\n";
        $mstring = $mstring."</table>";
    */
    /*
        $mstring = $mstring."<table cellpadding=3 cellspacing=0 width=615  >\n";
        $mstring = $mstring."<tr height=25 align=center><td width=150 class=s_td width=30>항목</td><td class=m_td width=200>페이지 명</td><td class=e_td width=50>페이지뷰</td></tr>\n";
        $mstring = $mstring."<tr height=25 bgcolor=#ffffff>
            <td bgcolor=#efefef align=center id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">최다 요청</td>
            <td align=right id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">오후 2:00:00 ∼ 오후 3:00:00</td>
            <td bgcolor=#efefef  align=right id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">543</td>
            </tr>\n";
        $mstring = $mstring."<tr height=25 bgcolor=#ffffff>
            <td bgcolor=#efefef align=center id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">최소 요청</td>
            <td align=right id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">오후 2:00:00 ∼ 오후 3:00:00</td>
            <td bgcolor=#efefef  align=right id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">33</td>
            </tr>\n";
        $mstring = $mstring."</table><br>\nword-break:keep-all";
    */

    $mstring = $mstring."<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' STYLE='TABLE-LAYOUT:fixed' class='list_table_box'>\n";
    $mstring = $mstring."<tr height=25 align=center><td width=50 class=s_td>순</td><td class=m_td width=490>페이지 명</td><td class=e_td width=75 nowrap>페이지뷰</td></tr>\n";
    for($i=0;$i<$fordb->total;$i++){
        $fordb->fetch($i);
        $mstring = $mstring."<tr height=40 bgcolor=#ffffff  id='Report$i'>
		<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($i+1)."</td>
		<td class='point' align=left onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" width=390 title='".urldecode($fordb->dt[vetcreferer_url])."' style='padding:10px;' wrap>
			<a href='".$fordb->dt[vetcreferer_url]."' target=_blank>".$fordb->dt[vetcreferer_url]."</a>
		</td>
		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".returnZeroValue($fordb->dt[visit_cnt])."&nbsp;</td></tr>\n";

        $pageview01 = $pageview01 + returnZeroValue($fordb->dt[visit_cnt]);


    }
    //$mstring = $mstring."</table>\n";
    //$mstring = $mstring."<table cellpadding=3 cellspacing=0 width=100% class='list_table_box' >\n";
    if ($pageview01 == 0){
        $mstring = $mstring."<tr height=150 bgcolor=#ffffff align=center><td colspan=3>결과값이 없습니다.</td></tr>\n";
    }

    $mstring = $mstring."<tr height=25 align=center><td width=50 class=s_td width=30 colspan=2>합계</td><td class=e_td width=190>".$pageview01."</td></tr>\n";
    $mstring = $mstring."</table>\n";

    /*
    $help_text = "
    <table>
        <tr>
            <td style='line-height:150%'>
            - 기타 URL 관리란? \"유입 사이트별 방문 횟수\" 메뉴의 좌측 카테고리에 등록되어 있지 않은 사이트에서 유입한 방문자의 데이터를 분석하여 각 사이트의 URL과 방문 횟수를 확인하실 수 있는 리포트입니다.<br>
            - 기타사이트의 카테고리 등록은 \"관리자모드\" 메뉴에서 변경/등록하실 수 있습니다.<br>
            </td>
        </tr>
    </table>
    ";*/
    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );


    $mstring .= HelpBox("기타 방문 URL 관리", $help_text);

    return $mstring;
}
/*
if ($mode == "iframe"){
	echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
	echo "<Script>parent.document.getElementById('contentsarea').innerHTML = document.tablefrm.reportvalue.value</Script>";

	if($SelectReport == "3"){
		$ca = new Calendar();
		$ca->LinkPage = 'etcreferer.php';

		echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";
		echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.vdate;parent.ChangeCalenderView($SelectReport);</Script>";

	}
}else{
*/
if ($mode == "iframe"){
    //echo "<meta http-equiv='content-type' content='text/html; charset=euc-kr'>";
    //echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
    //echo "<Script>parent.document.getElementById('contentsarea').innerHTML = document.tablefrm.reportvalue.value</Script>";


    $ca = new Calendar();
    $ca->LinkPage = 'etcreferer.php';

    echo "<html>";
    echo "<meta http-equiv='content-type' content='text/html; charset=euc-kr'>";
    echo "<body>";
    echo "<div id='report_view'>".ReportTable($vdate,$SelectReport)."</div>";
    echo "<div id='calendar_view'>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</div>";
    echo "</body>";
    echo "<Script>parent.document.getElementById('contentsarea').innerHTML = document.getElementById('report_view').innerHTML;</Script>";
    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.getElementById('calendar_view').innerHTML;parent.vdate;parent.ChangeCalenderView($SelectReport);</Script>";
    echo "</html>";

    //echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";
    //echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.ChangeCalenderView($SelectReport);</Script>";

}else{
    $p = new forbizReportPage();

    $p->Navigation = "로그분석 > 관리자모드 > 기타 URL 관리";
    $p->title = "기타 URL 관리";
    $p->forbizLeftMenu = Stat_munu('etcreferer.php');
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->addScript = "<script language='JavaScript' src='../include/manager.js'></script>\n<script language='JavaScript' src='../include/Tree.js'></script>";
    $p->PrintReportPage();
}
?>