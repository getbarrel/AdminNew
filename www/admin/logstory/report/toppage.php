<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");
//include("../report/toppage.chart.php");

function ReportTable($vdate,$SelectReport=1){
    global $pageinfos, $first_page, $pageid;
    $mstring = "";

    $pageview01 = 0;
    $pageview02 = 0;
    $pageview03 = 0;
    $chart_data2 = array();
    $fordb = new forbizDatabase();
    $fordb2 = new forbizDatabase();

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
						where (b.vdate = '$vdate' or b.vdate = '$voneweekago' or b.vdate LIKE '".substr($vdate,0,6)."%' ) and p.pageid = b.pageid
						group by b.pageid, p.vurl
						order by pageview0 desc 
						LIMIT 0,50";
        }else{
            $sql = "Select b.pageid, p.vurl, sum(case when b.vdate = '".$vdate."' then ncnt else 0 end) as pageview0 ,
						sum(case when b.vdate = '".$voneweekago."' then ncnt else 0 end) as pageview1,
						avg(case when b.vdate LIKE '".substr($vdate,0,6)."%' then ncnt else 0 end) as pageview2
						from ".TBL_LOGSTORY_PAGEINFO." p, ".TBL_LOGSTORY_BYPAGE." b
						where (b.vdate = '$vdate' or b.vdate = '$voneweekago' or b.vdate LIKE '".substr($vdate,0,6)."%' ) and p.pageid = b.pageid
						group by b.pageid, p.vurl
						order by pageview0 desc LIMIT 0,50";
        }
        $table_title1 = "페이지뷰(해당일)";
        $table_title2 = "페이지뷰(1주일전)";
        $table_title3 = "페이지뷰(한달평균)";


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
						group by b.pageid , p.vurl
						order by pageview0 desc 
						LIMIT 0,50";
        //$sql = "Select sum(ncnt) as ncnt, p.pageid, p.vurl from ".TBL_LOGSTORY_PAGEINFO." p, ".TBL_LOGSTORY_BYPAGE." b where b.vdate between '$vdate' and '$vweekenddate' and p.pageid = b.pageid group by p.pageid order by ncnt desc LIMIT 0,50";
        $table_title1 = "페이지뷰(해당주)";
        $table_title2 = "페이지뷰(1주전)";
        $table_title3 = "페이지뷰(4주전)";
    }else if($SelectReport == 3){
        $sql = "Select b.pageid, p.vurl, 
						sum(case when b.vdate LIKE '".substr($vdate,0,6)."%' then ncnt else 0 end) as pageview0 ,
						sum(case when b.vdate LIKE '".substr($vonemonthago,0,6)."%' then ncnt else 0 end) as pageview1,
						avg(case when b.vdate LIKE '".substr($vtwomonthago,0,6)."%' then ncnt else 0 end) as pageview2
						from ".TBL_LOGSTORY_PAGEINFO." p, ".TBL_LOGSTORY_BYPAGE." b
						where (b.vdate LIKE '".substr($vdate,0,6)."%' or b.vdate LIKE '".substr($vonemonthago,0,6)."%' or b.vdate LIKE '".substr($vtwomonthago,0,6)."%' ) 
						and p.pageid = b.pageid
						group by b.pageid, p.vurl
						order by pageview0 desc 
						LIMIT 0,50";

        $table_title1 = "페이지뷰(해당월)";
        $table_title2 = "페이지뷰(1개월전)";
        $table_title3 = "페이지뷰(2개월전)";
        //$sql = "Select sum(ncnt) as ncnt, p.pageid, p.vurl from ".TBL_LOGSTORY_PAGEINFO." p, ".TBL_LOGSTORY_BYPAGE." b where b.vdate LIKE '".substr($vdate,0,6)."%' and p.pageid = b.pageid group by p.pageid order by ncnt desc LIMIT 0,50";
    }
    //echo nl2br($sql);
    $fordb->query($sql);
    //echo $sql;
    //$fordb2->query($sql2);
    //$datas = $fordb2->fetchall();

    //print_r($datas);

    if($SelectReport == 1){
        $mstring .= TitleBar("자주찾는 페이지","일간 : ". getNameOfWeekday(0,$vdate,"dayname"));
    }else if($SelectReport == 2){
        $mstring .= TitleBar("자주찾는 페이지",getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate));
    }else if($SelectReport == 3){
        $mstring .= TitleBar("자주찾는 페이지",getNameOfWeekday(0,$vdate,"monthname"));
    }


//	$mstring .= TitleBar("자주찾는 페이지");
    $mstring .= "<table cellpadding=0 cellspacing=0 width=100% border=0>\n";
    if($fordb->total){
        /*
        $mstring .= "<tr  align=center>
                        <td colspan=3 style='padding-bottom:10px;' align=left>".TopPageGraph($vdate,$SelectReport)."</td>
                        <td colspan=3 style='padding-bottom:10px;'><img src='toppage.bar_chart.php?vdate=".$vdate."&SelectReport=".$SelectReport."&pageid=".($pageid ? $pageid:$first_page[pageid])."&pagename=".($first_page[page_ko_name])."' ></td>
                        </tr>\n";
        */

//        TopPageGraph($vdate,$SelectReport);

        $mstring .= "<tr  align=center>
					<td colspan=3 style='padding-bottom:10px;' align=left><div id='piechart' style=' height: 300px; padding: 0px; position: relative;'></div></td>
					</tr>\n";

        $mstring .= "<tr  align=center><td colspan=3 style='padding:10px 0px;' align='right'><".($first_page['page_ko_name'] ? $first_page['page_ko_name']." 요일별 추이":'페이지뷰')."></td></tr>\n";
        //$mstring .= "<tr  align=center><td colspan=3 style='padding:10px 0px;'><div id='line-chart' style='height: 300px; position: relative;'></div></td></tr>\n";
    }

    $mstring .= "<tr  align=center><td colspan=3 ></td></tr>\n";
    $mstring .= "</table>";
    /*
        $mstring .= "<table cellpadding=3 cellspacing=0 width=745  >\n";
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

    $mstring .= "<table cellpadding=3 cellspacing=0 width=100%  ID='MaxViewProductTable' class='list_table_box'>\n";
    $mstring .= "<tr height=30 align=center>
								<td width=5% class=s_td width=30>순</td>
								<td class=m_td width=25%>페이지 명</td>
								<td class=m_td width=20%>".$table_title1."</td>
								<td class=m_td width=20%>".$table_title2."</td>
								<td class=e_td width=20%>".$table_title3."</td>
							</tr>\n";

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

    for($i=0;$i<$fordb->total;$i++){

        if($i < 10){
            $chart_data2[] = array(
                'label' => ($pageinfos[$fordb->dt['pageid']]['page_ko_name'] ? $pageinfos[$fordb->dt['pageid']]['page_ko_name']:$pageinfos[$fordb->dt['pageid']]['vurl']),
                'data' => array(0=>array("1",$fordb->dt['pageview0'])) ,
                'color' => $color[$i]
            );
        }

        $fordb->fetch($i);
        //$fordb2->fetch($i);
        $mstring .= "<tr height=32 bgcolor=#ffffff align=right id='Report$i'>
		<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\" >".($i+1)."</td>
		<td class='point' align=left onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding:10px;line-height:140%;'>".($pageinfos[$fordb->dt['pageid']]['page_ko_name'] ? $pageinfos[$fordb->dt['pageid']]['page_ko_name']."<br><span style='color:gray'>".$pageinfos[$fordb->dt['pageid']][vurl]."</span>":$pageinfos[$fordb->dt['pageid']]['vurl'])."</td>
		<td bgcolor=#efefef onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb->dt['pageview0']),0)."&nbsp;</td>
		<td bgcolor=#ffffff onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb->dt['pageview1']),0)."&nbsp;</td>
		<td bgcolor=#efefef onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format(returnZeroValue($fordb->dt['pageview2']),0)."&nbsp;</td>
		</tr>\n";

        $pageview01 = $pageview01 + returnZeroValue($fordb->dt['pageview0']);
        $pageview02 = $pageview02 + returnZeroValue($fordb->dt['pageview1']);
        $pageview03 = $pageview03 + returnZeroValue($fordb->dt['pageview2']);

    }

//	$mstring .= "</table>\n";
//	$mstring .= "<table cellpadding=3 cellspacing=0 width=100%  >\n";
    if ($pageview03 == 0){
        $mstring .= "<tr height=50 bgcolor=#ffffff align=center><td colspan=5>결과값이 없습니다.</td></tr>\n";
    }
    $mstring .= "<tr height=30 align=right>
								<td class=s_td align=center colspan=2>합계</td>
								<td class=m_td width=190 style='padding-right:20px;'>".number_format($pageview01)."</td>
								<td class=m_td width=190 style='padding-right:20px;'>".number_format($pageview02)."</td>
								<td class=e_td width=190 style='padding-right:20px;'>".number_format($pageview03)."</td>
							</tr>\n";
    $mstring .= "</table>\n";
    /*
        $help_text = "
        <table>
            <tr>
                <td style='line-height:150%'>
                - 자주찾는 페이지란? 방문자가 웹페이지를 한번 클릭해서 보여지는 페이지의 횟수를 각각의 페이지별로 분석해 주는 리포트중 상위페이지를 보여주는 리포트입니다 <br>
                - 사이트내의 방문자들의 관심분야를 볼수 있는 중요한 리포트중에 하나입니다<br>
                </td>
            </tr>
        </table>
        ";*/

    $chart_mode="morris";
    $pageid = ($pageid ? $pageid:$first_page['pageid']);
    $pagename = ($first_page['page_ko_name']);
    include_once("./toppage.bar_chart.php");

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
	
	var piedata = ".json_encode($chart_data2).";
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

  new Morris.Line({
        // ID of the element in which to draw the chart.
        element: 'line-chart',
        // Chart data records -- each entry in this array corresponds to a point on
        // the chart.
        data: ".json_encode($chart_data).",
        xkey: 'y',
        ykeys: ".json_encode($ykeys).",
        labels: ".json_encode($labels).",
        lineColors: ['#D9534F', '#428BCA','#1CAF9A','#5BC0DE'],
        lineWidth: '2px',
        hideHover: true,
		parseTime:false
    });

	function labelFormatter(label, series) {
		return \"<div style='font-size:8pt; text-align:center; padding:2px; color:white;'>\" + label + \"<br/>\" + Math.round(series.percent) + \"%</div>\";
	}

</script>";

    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );


    $mstring .= HelpBox("자주찾는 페이지", $help_text);
    return $mstring;
}
if ($mode == "iframe"){
    echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";


    $ca = new Calendar();
    $ca->LinkPage = 'toppage.php';

    echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";
    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.vdate;parent.ChangeCalenderView($SelectReport);</Script>";

}else{
    $p = new forbizReportPage();

    $p->Navigation = "로그분석 > 페이지 분석 > 자주찾는 페이지";
    $p->title = "자주찾는 페이지";
    $p->forbizLeftMenu = Stat_munu('toppage.php');
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->PrintReportPage();
}
?>
