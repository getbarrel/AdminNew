<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");
include("../report/toppage.chart.php");

function ReportTable($vdate,$SelectReport=1){
    $visit_cnt = 0;
    $fordb = new forbizDatabase();

    $mstring = "";

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
        $sql = "Select * from  ".TBL_LOGSTORY_VISITORINFO." b 
				where b.vdate = '$vdate'  
				order by visit_cnt desc 
				LIMIT 0,50";

        $selected_date = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
    }else if($SelectReport == 2){
        $sql = "Select sum(visit_cnt) as visit_cnt, ip_addr, user_agent, regdate 
					from  ".TBL_LOGSTORY_VISITORINFO." b 
					where b.vdate between '$vdate' and '$vweekenddate'  
					group by ip_addr, user_agent, regdate  
					order by visit_cnt desc 
					LIMIT 0,50";
        $selected_date = "주간 : ". getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
    }else if($SelectReport == 3){
        $sql = "Select sum(visit_cnt) as visit_cnt, ip_addr, user_agent, regdate 
				from ".TBL_LOGSTORY_VISITORINFO." b 
				where b.vdate LIKE '".substr($vdate,0,6)."%'  
				group by ip_addr, user_agent, regdate  
				order by visit_cnt desc 
				LIMIT 0,50";
        $selected_date = "월간 : ". getNameOfWeekday(0,$vdate,"monthname");
    }

    $fordb->query($sql);

    $mstring = $mstring.TitleBar("방문자 IP 리스트",$selected_date);



    $mstring = $mstring."<table border=0 cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>\n";
    $mstring = $mstring."<tr height=30 align=center><td width=50 class=s_td width=30>순</td><td class=m_td width=120>방문일시</td><td class=m_td width=130>IP ADDRESS</td><td class=m_td width=500>USER AGENT</td><td class=e_td width=90>방문횟수</td></tr>\n";
    for($i=0;$i<$fordb->total;$i++){
        $fordb->fetch($i);
        $mstring = $mstring."<tr height=30 bgcolor=#ffffff  id='Report$i'>
		<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($i+1)."</td>
		<td align=center onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" nowrap>".$fordb->dt['regdate']."</td>
		<td class='point' align=center onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".$fordb->dt['ip_addr']."</td>
		<td onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" align=left style='padding:10px;'> ".$fordb->dt['user_agent']."</td>
		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".returnZeroValue($fordb->dt['visit_cnt'])."&nbsp;</td>
		</tr>\n";

        $visit_cnt = $visit_cnt + returnZeroValue($fordb->dt['visit_cnt']);


    }

    if ($visit_cnt == 0){
        $mstring = $mstring."<tr height=50 bgcolor=#ffffff align=center><td colspan=5>결과값이 없습니다.</td></tr>\n";
    }

    $mstring = $mstring."<tr height=30 align=center><td class=s_td colspan=4>합계</td><td class=e_td align=right style='padding-right:20px;'>".$visit_cnt."</td></tr>\n";
    $mstring = $mstring."</table>\n";

    /*
    $help_text = "
    <table>
        <tr>
            <td style='line-height:150%'>
            - 방문자 IP 리스트란? 방문자의 IP를 확인 할 수 있는 리포트로 방문시간과 IP 주소 및 방문 횟수를 종합적으로 집계하여 확인하실 수 있습니다.<br>
            - 해당 리스트는 로봇틱한 패턴이나 비정상적인 방문에 대한 IP를 리포트 합니다.
            </td>
        </tr>
    </table>
    ";*/
    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );


    $mstring .= HelpBox("방문자 IP 리스트", $help_text);

    return $mstring;
}

if ($mode == "iframe"){
    echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";


    $ca = new Calendar();
    $ca->LinkPage = 'visitor_list.php';

    echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";
    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.vdate;parent.ChangeCalenderView($SelectReport);</Script>";

}else{
    $p = new forbizReportPage();

    $p->Navigation = "로그분석 > 방문자분석 > 방문자 IP 리스트";
    $p->title = "방문자 IP 리스트";
    $p->forbizLeftMenu = Stat_munu('visitor_list.php');
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->PrintReportPage();
}
?>
