<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");

function ReportTable($vdate,$SelectReport=1){
    $pageview01 = 0;
    $mstring = "";
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

    $sql = "Select p.pageid, vurl, page_ko_name from ".TBL_LOGSTORY_PAGEINFO." p ";
    //echo $sql;
    $fordb->query($sql);

    for($i=0;$i<$fordb->total;$i++){
        $fordb->fetch($i);

        $pageinfos[$fordb->dt['pageid']] = array("vurl"=> $fordb->dt['vurl'], "page_ko_name"=> $fordb->dt['page_ko_name']);
    }

    if($SelectReport == 1){
        $sql = "Select * from ".TBL_LOGSTORY_PAGEINFO." p, ".TBL_LOGSTORY_BYPAGE." b where b.vdate = '$vdate' and p.pageid = b.pageid order by ncnt desc";
    }else if($SelectReport == 2){
        $sql = "Select p.pageid, p.vurl , sum(ncnt) as ncnt, sum(nduration) as nduration
					from ".TBL_LOGSTORY_PAGEINFO." p, ".TBL_LOGSTORY_BYPAGE." b 
					where b.vdate between '$vdate' and '$vweekenddate' 
					and p.pageid = b.pageid 
					group by p.pageid, p.vurl
					order by ncnt desc 
					LIMIT 0,100";

    }else if($SelectReport == 3){
        $sql = "Select  p.pageid, p.vurl , sum(ncnt) as ncnt, sum(nduration) as nduration
					from ".TBL_LOGSTORY_PAGEINFO." p, ".TBL_LOGSTORY_BYPAGE." b 
					where b.vdate LIKE '".substr($vdate,0,6)."%' 
					and p.pageid = b.pageid 
					group by  p.pageid, p.vurl
					order by ncnt desc 
					LIMIT 0,100";
    }

    $fordb->query($sql);

    if($SelectReport == 1){
        $mstring = $mstring.TitleBar("페이지별 페이지뷰","일간 : ". getNameOfWeekday(0,$vdate,"dayname"));
    }else if($SelectReport == 2){
        $mstring = $mstring.TitleBar("페이지별 페이지뷰",getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate));
    }else if($SelectReport == 3){
        $mstring = $mstring.TitleBar("페이지별 페이지뷰",getNameOfWeekday(0,$vdate,"monthname"));
    }
    /*
        $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  >\n";
        $mstring .= "<tr height=30 align=center><td width=150 class=s_td width=30>항목</td><td class=m_td width=200>페이지 명</td><td class=e_td width=200>페이지뷰</td></tr>\n";
        $mstring .= "<tr height=30 bgcolor=#ffffff>
            <td bgcolor=#efefef align=center id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">최다 요청</td>
            <td align=right id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">오후 2:00:00 ∼ 오후 3:00:00</td>
            <td bgcolor=#efefef  align=right id='Report10000' onmouseover=\"mouseOnTD('10000',true)\" onmouseout=\"mouseOnTD('10000',false)\">543</td>
            </tr>\n";
        $mstring .= "<tr height=30 bgcolor=#ffffff>
            <td bgcolor=#efefef align=center id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">최소 요청</td>
            <td align=right id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">오후 2:00:00 ∼ 오후 3:00:00</td>
            <td bgcolor=#efefef  align=right id='Report10001' onmouseover=\"mouseOnTD('10001',true)\" onmouseout=\"mouseOnTD('10001',false)\">33</td>
            </tr>\n";
        $mstring .= "</table><br>\n";
    */
    $mstring .= "<table cellpadding=0 cellspacing=0 width=100% border=0 >\n";
    //$mstring .= "<tr  align=center><td colspan=3 style='padding:10px 0px;'><div id='stacked-chart' style='width: 100%; height: 300px; padding: 0px; position: relative;' /></td></tr>\n";
    $mstring .= "<tr  align=center><td colspan=3 style='padding:30px 0px;text-align:center;'><div id='piechart' style='width: 70%; height: 300px; padding: 0px; position: relative;'></div></td></tr>\n";
    $mstring .= "</table><br>";

    $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box' style='table-layout:fixed; word-break:break-all;'>
				<col width='50'>
				<col width='*'>
				<col width='190'>
				<col width='190'>";
    $mstring .= "<tr height=30 align=center><td  class=s_td >순</td><td class=m_td >페이지 명</td><td class=m_td >페이지뷰</td><td class=e_td >페이지뷰당 평균 체류시간</td></tr>\n";

    $color[] = "#D9534F";
    $color[] = "#1CAF9A";
    $color[] = "#F0AD4E";
    $color[] = "#428BCA";
    $color[] = "#5BC0DE";
    $color[] = "#FF9933";
    $color[] = "#FFCC33";
    $color[] = "#FFFF33";
    $color[] = "#33CCFF";
    $color[] = "#33FFFF";
    $duration_sum = "";
    for($i=0;$i<$fordb->total;$i++){
        $fordb->fetch($i);
        if($i < 10){
            $chart_data[] = array(
                'label' => ($pageinfos[$fordb->dt['pageid']]['page_ko_name'] ? $pageinfos[$fordb->dt['pageid']]['page_ko_name']:$pageinfos[$fordb->dt['pageid']]['vurl']),
                'data' => array(0=>array("1",$fordb->dt['ncnt'])) ,
                'color' => $color[$i]
            );
        }
        $mstring .= "<tr height=30 bgcolor=#ffffff  id='Report$i'>
		<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($i+1)."</td>
		<td class='point' align=left onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:5px;line-height:140%;'>
			".($pageinfos[$fordb->dt['pageid']]['page_ko_name'] ? $pageinfos[$fordb->dt['pageid']]['page_ko_name']."<br><span style='color:gray'>".$pageinfos[$fordb->dt['pageid']]['vurl']."</span>":$pageinfos[$fordb->dt['pageid']]['vurl'])."
		</td>
		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb->dt['ncnt']))."</td>
		<td bgcolor=#ffffff align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".($fordb->dt['ncnt'] > 0 ? displayTimeFormat(returnZeroValue($fordb->dt['nduration']/$fordb->dt['ncnt'])):0)."</td>
		</tr>\n";

        $pageview01 = $pageview01 + returnZeroValue($fordb->dt['ncnt']);
        $duration_sum = $duration_sum + returnZeroValue($fordb->dt['nduration']);


    }
//	$mstring .= "</table>\n";
//	$mstring .= "<table cellpadding=3 cellspacing=0 width=100%  >\n";
    if ($pageview01 == 0){
        $mstring .= "<tr bgcolor=#ffffff align=center><td colspan=4 height=100>결과값이 없습니다.</td></tr>\n";
    }
    //$mstring .= "<tr height=2 bgcolor=#ffffff align=center><td colspan=4 width=190></td></tr>\n";
    $mstring .= "<tr height=30 align=right>
	<td class=s_td align=center colspan=2>합계</td>
	<td class=m_td style='padding-right:20px;'>".number_format($pageview01)."</td>
	<td class=e_td style='padding-right:20px;'>".($pageview01 > 0 ? displayTimeFormat($duration_sum/$pageview01):"0")."</td>
	</tr>\n";
    $mstring .= "</table>\n";
    /*
        $help_text = "
        <table>
            <tr>
                <td style='line-height:150%'>
                - 페이지별 페이지뷰란? 방문자가 웹페이지를 한번 클릭해서 보여지는 페이지의 횟수를 각각의 페이지별로 분석해 주는 리포트입니다.  <br>
                - 사이트내의 방문자들의 관심도를 볼수 있는 중요한 리포트중에 하나입니다<br>
                </td>
            </tr>
        </table>
        ";*/
    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );

    $mstring .= HelpBox("페이지별 페이지뷰", $help_text);


    $mstring .= "<link href='../css/morris.css' rel='stylesheet'>
<!--script src='../js/jquery-1.10.2.min.js'></script-->
<script src='../js/jquery-migrate-1.2.1.min.js'></script>
<script src='../js/bootstrap.min.js'></script>

<script src='../js/modernizr.min.js'></script> 
<script src='../js/jquery.sparkline.min.js'></script>
<script src='../js/toggles.min.js'></script>

<script src='../js/retina.min.js'></script> 
<script src='../js/jquery.cookies.js'></script>

<script src='../js/flot/flot.min.js'></script>
<script src='../js/flot/flot.resize.min.js'></script>
<script src='../js/flot/flot.symbol.min.js'></script>
<script src='../js/flot/flot.crosshair.min.js'></script>
<script src='../js/flot/flot.categories.min.js'></script>
<script src='../js/flot/flot.pie.min.js'></script>
<script src='../js/morris.min.js'></script>
<script src='../js/raphael-2.1.0.min.js'></script>

<script src='../js/custom.js'></script>
<!--script src='../js/charts.js'></script-->
<script script='javascript'>
var piedata = ".json_encode($chart_data).";
    
    jQuery.plot('#piechart', piedata, {
        series: {
            pie: {
                show: true,
                radius: 1,
                label: {
                    show: true,
                    radius: 2/3,
                    formatter: labelFormatter,
                    threshold: 0.1
                }
            }
        },
        grid: {
            hoverable: true,
            clickable: true
        }
    });

	function labelFormatter(label, series) {
		return \"<div style='font-size:8pt; text-align:center; padding:2px; color:white;'>\" + label + \"<br/>\" + Math.round(series.percent) + \"%</div>\";
	}

</script>";


    return $mstring;
}

if ($mode == "iframe"){
    echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";


    $ca = new Calendar();
    $ca->LinkPage = 'pageviewbypage.php';

    echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";
    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.vdate;parent.ChangeCalenderView($SelectReport);</Script>";

}else{
    $p = new forbizReportPage();

    $p->Navigation = "로그분석 > 페이지 분석 > 페이지별 페이지뷰";
    $p->title = "페이지별 페이지뷰";
    $p->forbizLeftMenu = Stat_munu('pageviewbypage.php');
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->PrintReportPage();
}

