<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");

function ReportTable($vdate,$SelectReport=1){
    $mstring = "";
    $pageview01 = 0;
    $pageview02 = 0;
    $pageview03 = 0;

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
        $vweekenddate = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)+6,substr($vdate,0,4)));
        $vyesterday = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-1,substr($vdate,0,4)));
        $voneweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-7,substr($vdate,0,4)));
        $vfourweekago = date("Ymd",mktime(0,0,0,substr($vdate,4,2),substr($vdate,6,2)-28,substr($vdate,0,4)));
        $vonemonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-1,substr($vdate,6,2),substr($vdate,0,4)));
        $vtwomonthago = date("Ymd",mktime(0,0,0,substr($vdate,4,2)-2,substr($vdate,6,2),substr($vdate,0,4)));

    }

    $sql = "Select p.pageid, vurl, page_ko_name from ".TBL_LOGSTORY_PAGEINFO." p ";
    //echo $sql;
    $fordb->query($sql);

    for($i=0;$i<$fordb->total;$i++){
        $fordb->fetch($i);

        $pageinfos[$fordb->dt['pageid']] = array("vurl"=> $fordb->dt['vurl'], "page_ko_name"=> $fordb->dt['page_ko_name']);
    }

    if($SelectReport == 1){
        if($fordb->dbms_type == "oracle"){
            $sql = "Select b.pageid, p.vurl, sum(case when b.vdate = '".$vdate."' then ncnt else 0 end) as pageview0 ,
						sum(case when b.vdate = '".$voneweekago."' then ncnt else 0 end) as pageview1,
						avg(case when b.vdate LIKE '".substr($vdate,0,6)."%' then ncnt else 0 end) as pageview2
						from ".TBL_LOGSTORY_PAGEINFO." p, ".TBL_LOGSTORY_BYPAGE." b
						where (b.vdate = '$vdate' or b.vdate = '$voneweekago' or b.vdate LIKE '".substr($vdate,0,6)."%' ) 
						and p.pageid = b.pageid and ncnt != 0
						group by b.pageid, p.vurl order by pageview0 asc LIMIT 0,50";
        }else{
            $sql = "Select b.pageid, p.vurl, sum(case when b.vdate = '".$vdate."' then ncnt else 0 end) as pageview0 ,
						sum(case when b.vdate = '".$voneweekago."' then ncnt else 0 end) as pageview1,
						avg(case when b.vdate LIKE '".substr($vdate,0,6)."%' then ncnt else 0 end) as pageview2
						from ".TBL_LOGSTORY_PAGEINFO." p, ".TBL_LOGSTORY_BYPAGE." b
						where (b.vdate = '$vdate' or b.vdate = '$voneweekago' or b.vdate LIKE '".substr($vdate,0,6)."%' )and p.pageid = b.pageid and ncnt != 0
						group by b.pageid order by pageview0 asc LIMIT 0,50";
        }
        $table_title1 = "페이지뷰(해당일)";
        $table_title2 = "페이지뷰(1주일전)";
        $table_title3 = "페이지뷰(한달평균)";
        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
        //	$sql = "Select * from ".TBL_LOGSTORY_PAGEINFO." p, ".TBL_LOGSTORY_BYPAGE." b where b.vdate = '$vdate' and p.pageid = b.pageid order by ncnt desc LIMIT 0,50";
        //	$sql2 = "Select * from ".TBL_LOGSTORY_PAGEINFO." p, ".TBL_LOGSTORY_BYPAGE." b where b.vdate = '$voneweekago' and p.pageid = b.pageid order by ncnt desc LIMIT 0,50";
    }else if($SelectReport == 2){
        $sql = "Select b.pageid, p.vurl,
						sum(case when b.vdate between '$vdate' and '$vweekenddate' then ncnt else 0 end) as pageview0 ,
						sum(case when b.vdate between '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2),substr($voneweekago,0,4)))."'
						and '".date("Ymd",mktime(0,0,0,substr($voneweekago,4,2),substr($voneweekago,6,2)+6,substr($voneweekago,0,4)))."' then ncnt else 0 end) as pageview1,
						sum(case when b.vdate between '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2),substr($vfourweekago,0,4)))."'
						and '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2)+6,substr($vfourweekago,0,4)))."' then ncnt else 0 end) as pageview2
						from ".TBL_LOGSTORY_PAGEINFO." p, ".TBL_LOGSTORY_BYPAGE." b
						where (b.vdate between  '".date("Ymd",mktime(0,0,0,substr($vfourweekago,4,2),substr($vfourweekago,6,2)-7,substr($vfourweekago,0,4)))."' and '$vdate' ) and p.pageid = b.pageid
						group by b.pageid, p.vurl 
						order by pageview0 asc 
						LIMIT 0,50";
        //$sql = "Select sum(ncnt) as ncnt, p.pageid, p.vurl from ".TBL_LOGSTORY_PAGEINFO." p, ".TBL_LOGSTORY_BYPAGE." b where b.vdate between '$vdate' and '$vweekenddate' and p.pageid = b.pageid group by p.pageid order by ncnt desc LIMIT 0,50";
        $table_title1 = "페이지뷰(해당주)";
        $table_title2 = "페이지뷰(1주전)";
        $table_title3 = "페이지뷰(4주전)";
        $dateString = "주간 : ".getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
    }else if($SelectReport == 3){
        $sql = "Select b.pageid, p.vurl, sum(case when b.vdate LIKE '".substr($vdate,0,6)."%' then ncnt else 0 end) as pageview0 ,
						sum(case when b.vdate LIKE '".substr($vonemonthago,0,6)."%' then ncnt else 0 end) as pageview1,
						avg(case when b.vdate LIKE '".substr($vtwomonthago,0,6)."%' then ncnt else 0 end) as pageview2
						from ".TBL_LOGSTORY_PAGEINFO." p, ".TBL_LOGSTORY_BYPAGE." b
						where (b.vdate LIKE '".substr($vdate,0,6)."%' or b.vdate LIKE '".substr($vonemonthago,0,6)."%' or b.vdate LIKE '".substr($vtwomonthago,0,6)."%' ) and p.pageid = b.pageid
						group by b.pageid ,p.vurl
						order by pageview0 asc 
						LIMIT 0,50";

        $table_title1 = "페이지뷰(해당월)";
        $table_title2 = "페이지뷰(1개월전)";
        $table_title3 = "페이지뷰(2개월전)";

        $dateString = "월간 : ".getNameOfWeekday(0,$vdate,"monthname");
        //$sql = "Select sum(ncnt) as ncnt, p.pageid, p.vurl from ".TBL_LOGSTORY_PAGEINFO." p, ".TBL_LOGSTORY_BYPAGE." b where b.vdate LIKE '".substr($vdate,0,6)."%' and p.pageid = b.pageid group by p.pageid order by ncnt desc LIMIT 0,50";
    }

    //echo $sql;
    $fordb->query($sql);

    $mstring = $mstring.TitleBar("자주찾지 않는 페이지", $dateString);

    $mstring = $mstring."<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>\n";
    $mstring .= "<tr height=30 align=center>
								<td width=50 class=s_td width=30>순</td>
								<td class=m_td width=390>페이지 명</td>
								<td class=m_td width=190>".$table_title1."</td>
								<td class=m_td width=190>".$table_title2."</td>
								<td class=e_td width=190>".$table_title3."</td>
							</tr>\n";

    for($i=0;$i<$fordb->total;$i++){
        $fordb->fetch($i);
        $mstring = $mstring."<tr height=32 bgcolor=#ffffff  id='Report$i'>
		<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($i+1)."</td>
		<td class='point' align=left onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:10px;line-height:140%;'>
			".($pageinfos[$fordb->dt['pageid']]['page_ko_name'] ? $pageinfos[$fordb->dt['pageid']]['page_ko_name']."<br><span style='color:gray'>".$pageinfos[$fordb->dt['pageid']][vurl]."</span>":$pageinfos[$fordb->dt['pageid']]['vurl'])."
		</td>
		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb->dt['pageview0']),0)."&nbsp;</td>
		<td bgcolor=#ffffff align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb->dt['pageview1']),0)."&nbsp;</td>
		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb->dt['pageview2']),0)."&nbsp;</td>
		</tr>\n";

        $pageview01 = $pageview01 + returnZeroValue($fordb->dt['pageview0']);
        $pageview02 = $pageview02 + returnZeroValue($fordb->dt['pageview1']);
        $pageview03 = $pageview03 + returnZeroValue($fordb->dt['pageview2']);


    }
    //$mstring = $mstring."</table>\n";
    //$mstring = $mstring."<table border=0 cellpadding=3 cellspacing=0 width=100%  >\n";
    if ($pageview03 == 0){
        $mstring = $mstring."<tr height=100 bgcolor=#ffffff align=center><td colspan=5>결과값이 없습니다.</td></tr>\n";
    }
    $mstring = $mstring."<tr height=2 bgcolor=#ffffff align=center><td colspan=5 ></td></tr>\n";
    $mstring = $mstring."<tr height=30 align=right>
							<td class=s_td align=center colspan=2>합계</td>
							<td class=m_td width=190 style='padding-right:20px;'>".number_format($pageview01)."</td>
							<td class=m_td width=190 style='padding-right:20px;'>".number_format($pageview02)."</td>
							<td class=e_td width=190 style='padding-right:20px;'>".number_format($pageview03)."</td>
						</tr>\n";
    $mstring = $mstring."</table>";
    /*
        $help_text = "
        <table>
            <tr>
                <td style='line-height:150%'>
                - 자주찾지 않는 페이지란? 방문자가 각 페이지를 한번 클릭해서 확인한 횟수를 각각의 페이지별로 분석해주는 리포트이며, 요청이 적은 순으로 확인하실 수 있습니다. <br>
                - 사이트내의 방문자들의 관심분야를 볼수 있는 중요한 리포트중에 하나입니다<br>
                </td>
            </tr>
        </table>
        ";*/
    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );


    $mstring .= HelpBox("자주찾지 않는 페이지", $help_text);

    return $mstring;
}

if ($mode == "iframe"){
    echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";


    $ca = new Calendar();
    $ca->LinkPage = 'lowpageview.php';

    echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";
    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.ChangeCalenderView($SelectReport);</Script>";

}else{
    $p = new forbizReportPage();

    $p->Navigation = "로그분석 > 페이지 분석 > 자주찾지 않는 페이지";
    $p->title = "자주찾지 않는 페이지";
    $p->forbizLeftMenu = Stat_munu('lowpageview.php');
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->PrintReportPage();
}
?>
