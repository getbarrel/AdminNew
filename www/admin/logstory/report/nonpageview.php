<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");

function ReportTable($vdate,$SelectReport=1){
    global $page;

    $pageview01 = 0;
    $fordb = new forbizDatabase();
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
    if($SelectReport ==1){
        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
    }else if($SelectReport ==2){
        $dateString = "주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
    }else if($SelectReport ==3){
        $dateString = "월간 : ".getNameOfWeekday(0,$vdate,"monthname");
    }


    $sql = "Select p.pageid, vurl, page_ko_name from ".TBL_LOGSTORY_PAGEINFO." p ";
    //echo $sql;
    $fordb->query($sql);

    for($i=0;$i<$fordb->total;$i++){
        $fordb->fetch($i);

        $pageinfos[$fordb->dt[pageid]] = array("vurl"=> $fordb->dt[vurl], "page_ko_name"=> $fordb->dt[page_ko_name]);
    }

    if($SelectReport == 1){
        //$tmp_sql = "create temporary table ".TBL_LOGSTORY_BYPAGE."_tmp ENGINE = MEMORY select vdate, pageid, ncnt, nduration from ".TBL_LOGSTORY_BYPAGE." where vdate = '$vdate' ";
        $sql = "Select vurl, ncnt from ".TBL_LOGSTORY_PAGEINFO." p LEFT JOIN (select vdate, pageid, ncnt, nduration from ".TBL_LOGSTORY_BYPAGE." where vdate = '$vdate') b on p.pageid = b.pageid and b.vdate = '$vdate' where ncnt is NULL";
    }else if($SelectReport == 2){
        //$tmp_sql = "create temporary table ".TBL_LOGSTORY_BYPAGE."_tmp ENGINE = MEMORY select vdate, pageid, ncnt, nduration from ".TBL_LOGSTORY_BYPAGE." where vdate between '$vdate' and '$vweekenddate'";
        $sql = "Select distinct vurl, ncnt from ".TBL_LOGSTORY_PAGEINFO." p LEFT JOIN (select vdate, pageid, ncnt, nduration from ".TBL_LOGSTORY_BYPAGE." where vdate between '$vdate' and '$vweekenddate') b on p.pageid = b.pageid and  b.vdate between '$vdate' and '$vweekenddate' where ncnt is NULL ";
    }else if($SelectReport == 3){
        //$tmp_sql = "create temporary table ".TBL_LOGSTORY_BYPAGE."_tmp ENGINE = MEMORY select vdate, pageid, ncnt, nduration from ".TBL_LOGSTORY_BYPAGE." where vdate LIKE '".substr($vdate,0,6)."%'";
        $sql = "Select distinct vurl, ncnt 
					from ".TBL_LOGSTORY_PAGEINFO." p 
					LEFT JOIN 
						(
						select vdate, pageid, ncnt, nduration 
						from ".TBL_LOGSTORY_BYPAGE." 
						where vdate LIKE '".substr($vdate,0,6)."%'
						) b on p.pageid = b.pageid and  b.vdate LIKE '".substr($vdate,0,6)."%' 
					where ncnt is NULL ";
    }
    //echo nl2br($sql);

    //$fordb->query($tmp_sql);
    $fordb->query($sql);
    $total = $fordb->total;
    $mstring = $mstring.TitleBar("비요청 페이지",$dateString);

    /*
        $mstring = $mstring."<table cellpadding=3 cellspacing=0 width=745  >\n";
        $mstring = $mstring."<tr height=30 align=center><td width=150 class=s_td width=30>항목</td><td class=m_td width=200>페이지 명</td><td class=e_td width=200>페이지뷰</td></tr>\n";
        $mstring = $mstring."<tr height=30 bgcolor=#ffffff>
            <td bgcolor=#efefef align=center id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">최다 요청</td>
            <td align=right id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">오후 2:00:00 ∼ 오후 3:00:00</td>
            <td bgcolor=#efefef  align=right id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">543</td>
            </tr>\n";
        $mstring = $mstring."<tr height=30 bgcolor=#ffffff>
            <td bgcolor=#efefef align=center id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">최소 요청</td>
            <td align=right id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">오후 2:00:00 ∼ 오후 3:00:00</td>
            <td bgcolor=#efefef  align=right id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">33</td>
            </tr>\n";
        $mstring = $mstring."</table><br>\n";
    */

    $max = 30;


    if ($page == ''){
        $start = 0;
        $page  = 1;
    }else{
        $start = ($page - 1) * $max;
    }

    if($SelectReport == 1){
        $sql = "Select p.pageid, vurl, ncnt from ".TBL_LOGSTORY_PAGEINFO." p LEFT JOIN (select vdate, pageid, ncnt, nduration from ".TBL_LOGSTORY_BYPAGE." where vdate = '$vdate' ) b on p.pageid = b.pageid and b.vdate = '$vdate' where ncnt is NULL  LIMIT $start, $max ";
    }else if($SelectReport == 2){
        $sql = "Select distinct p.pageid, vurl, ncnt from ".TBL_LOGSTORY_PAGEINFO." p LEFT JOIN (select vdate, pageid, ncnt, nduration from ".TBL_LOGSTORY_BYPAGE." where vdate between '$vdate' and '$vweekenddate') b on p.pageid = b.pageid and  b.vdate between '$vdate' and '$vweekenddate' where ncnt is NULL   LIMIT $start, $max  ";
    }else if($SelectReport == 3){
        $sql = "Select distinct p.pageid, vurl, ncnt from ".TBL_LOGSTORY_PAGEINFO." p LEFT JOIN (
						select vdate, pageid, ncnt, nduration 
						from ".TBL_LOGSTORY_BYPAGE." 
						where vdate LIKE '".substr($vdate,0,6)."%'
						) b on p.pageid = b.pageid and  b.vdate LIKE '".substr($vdate,0,6)."%' 
					where ncnt is NULL  LIMIT $start, $max  ";
    }


    $fordb->query($sql);

    $mstring = $mstring."<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>\n
									<col width=50>
									<col width=*>
									<col width=190>";
    $mstring = $mstring."<tr height=30 align=center><td  class=s_td >순</td><td class=m_td >페이지 명</td><td class=e_td>페이지뷰</td></tr>\n";
    for($i=0;$i<$fordb->total;$i++){
        $no = $total - ($page - 1) * $max - $i;

        $fordb->fetch($i);
        $mstring = $mstring."<tr height=32 bgcolor=#ffffff  id='Report$i'>
		<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($no)."</td>
		<td class='point' align=left onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:10px;line-height:140%;'>
			".($pageinfos[$fordb->dt['pageid']]['page_ko_name'] ? $pageinfos[$fordb->dt['pageid']]['page_ko_name']."<br><span style='color:gray'>".$pageinfos[$fordb->dt['pageid']][vurl]."</span>":$pageinfos[$fordb->dt['pageid']][vurl])."
		</td>
		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".returnZeroValue($fordb->dt[ncnt])."&nbsp;</td></tr>\n";

        $pageview01 = $pageview01 + returnZeroValue($fordb->dt[ncnt]);


    }

    ;

    $mstring = $mstring."</table>\n";

    $mstring = $mstring."<table cellpadding=0 cellspacing=0 width=100%  class='list_table_box' style='margin-top:5px;'>\n
									<col width=50>
									<col width=*>
									<col width=190>";
    if ($fordb->total == 0){
        $mstring = $mstring."<tr height=50 bgcolor=#ffffff align=center><td colspan=5>결과값이 없습니다.</td></tr>\n";
    }
    $mstring = $mstring."<tr height=30 align=center><td width=50 class=s_td width=30 colspan=2>합계</td><td class=e_td width=190>".$pageview01."</td></tr>\n";
    $mstring = $mstring."</table>\n";
    $mstring = $mstring."<table cellpadding=3 cellspacing=0 width=100%  >\n";
    $mstring = $mstring."<tr height=50 bgcolor=#ffffff align=center><td colspan=5>".page_bar($total, $page, $max,$HTTP_URL."&SubID=".$_GET["SubID"], "&SubID=".$_GET["SubID"])."</td></tr>\n";
    $mstring = $mstring."</table>\n";
    /*
        $help_text = "
        <table>
            <tr>
                <td style='line-height:150%'>
                - 비요청 페이지란? 해당 일에 한번도 호출 되지 않았던 페이지를 확인하실 수 있습니다. 주 단위, 월 단위로 분석하였을 경우 지속적으로 호출이 되지 않는 페이지가 있다면 경로가 없거나 시스템오류로 인해 고객들이 접속할 수 없는 페이지가 있을 수 있으니 반드시 확인하시기 바랍니다. <br>

                </td>
            </tr>
        </table>
        ";*/
    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );


    $mstring .= HelpBox("비요청 페이지", $help_text);

    return $mstring;
}
if ($mode == "iframe"){
    echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";


    $ca = new Calendar();
    $ca->LinkPage = 'nonpageview.php';

    echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";
    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.ChangeCalenderView($SelectReport);</Script>";

}else{
    $p = new forbizReportPage();

    $p->Navigation = "로그분석 > 페이지 분석 > 비요청페이지";
    $p->title = "비요청페이지";
    $p->forbizLeftMenu = Stat_munu('nonpageview.php');
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->PrintReportPage();
}
?>
