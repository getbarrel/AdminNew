<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");

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
        $sql = "Select distinct vurl, ncnt from ".TBL_LOGSTORY_PAGEINFO." p LEFT JOIN ".TBL_LOGSTORY_BYPAGE." b on p.pageid = b.pageid where p.vdate = '$vdate' order by ncnt desc";
        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
    }else if($SelectReport == 2){
        $sql = "Select distinct vurl, ncnt from ".TBL_LOGSTORY_PAGEINFO." p LEFT JOIN ".TBL_LOGSTORY_BYPAGE." b on p.pageid = b.pageid where  p.vdate between '$vdate' and '$vweekenddate' order by ncnt desc ";
        $dateString = "주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
    }else if($SelectReport == 3){
        $sql = "Select distinct vurl, ncnt from ".TBL_LOGSTORY_PAGEINFO." p LEFT JOIN ".TBL_LOGSTORY_BYPAGE." b on p.pageid = b.pageid where  p.vdate LIKE '".substr($vdate,0,6)."%' order by ncnt desc ";
        $dateString = "월간 : ".getNameOfWeekday(0,$vdate,"monthname");
    }

    $fordb->query($sql);

    $mstring = $mstring.TitleBar("신규등록 페이지",$dateString);

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

    $mstring = $mstring."<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>\n
									<col width=50>
									<col width=*>
									<col width=190>";
    $mstring = $mstring."<tr height=30 align=center><td class=s_td>순</td><td class=m_td>페이지 명</td><td class=e_td>페이지뷰</td></tr>\n";
    for($i=0;$i<$fordb->total;$i++){
        $fordb->fetch($i);
        $mstring = $mstring."<tr height=30 bgcolor=#ffffff  id='Report$i'>
		<td class='list_box_td list_bg_gray'  onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($i+1)."</td>
		<td class='list_box_td point' align=left onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:10px;'>".$fordb->dt[vurl]."</td>
		<td class='list_box_td list_bg_gray number' onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".returnZeroValue($fordb->dt[ncnt])."&nbsp;</td></tr>\n";

        $pageview01 = $pageview01 + returnZeroValue($fordb->dt[ncnt]);


    }
    if ($fordb->total == 0){
        $mstring = $mstring."<tr height=100 bgcolor=#ffffff align=center><td class='list_box_td'  colspan=3 height=100>결과값이 없습니다.</td></tr>\n";
    }

    //$mstring = $mstring."</table>\n";
    /*$mstring = $mstring."<table cellpadding=3 cellspacing=0 width=100%  >\n
                                    <col width=50>
                                    <col width=*>
                                    <col width=190>";*/


    $mstring = $mstring."<tr height=30 align=center><td width=50 class=s_td width=30 colspan=2>합계</td><td class=e_td width=190>".$pageview01."</td></tr>\n";
    $mstring = $mstring."</table>\n";
    /*
        $help_text = "
        <table>
            <tr>
                <td style='line-height:150%'>
                - 신규등록 페이지란? 쇼핑몰에 신규로 추가된 페이지들의 페이지뷰 횟수를 확인하는 리포트 입니다. 이벤트나 신규페이지를 생성하여 링크를 걸었을 경우 해당 페이지 접속율을 알 수 있습니다.
                </td>
            </tr>
        </table>
        ";*/
    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );


    $mstring .= HelpBox("신규등록 페이지", $help_text);

    return $mstring;
}
if ($mode == "iframe"){
    echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";


    $ca = new Calendar();
    $ca->LinkPage = 'newpage.php';

    echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";
    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.ChangeCalenderView($SelectReport);</Script>";

}else{
    $p = new forbizReportPage();

    $p->Navigation = "로그분석 > 페이지분석 > 신규등록 페이지";
    $p->title = "신규등록 페이지";
    $p->forbizLeftMenu = Stat_munu('newpage.php');
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->PrintReportPage();
}
?>
