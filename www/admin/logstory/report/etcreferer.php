<?php
@include($_SERVER["DOCUMENT_ROOT"]."/forbiz.config.php");
include("../class/reportpage.class");
include("../report/etcreferer.chart.php");

function ReportTable($vdate,$SelectReport=1){
    $visit_cnt_sum = 0;
    $mstring = "";

    $pre_visit_cnt_sum = "";
    $pre_visitor_cnt_sum = "";
    $visitor_cnt_sum = "";
    $chart_data = array();

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
        $sql = "Select visit_cnt, visitor_cnt, p.vetcreferer_id, p.vetcreferer_url 
					from ".TBL_LOGSTORY_ETCREFERERINFO." p, ".TBL_LOGSTORY_BYETCREFERER." b 
					where b.vdate = '$vdate' and p.vetcreferer_id = b.vetcreferer_id 
					order by visit_cnt desc 
					LIMIT 0,150";

        $dateString = "일간 : ". getNameOfWeekday(0,$vdate,"dayname");
    }else if($SelectReport == 2){
        /*
        $sql = "Select sum(visit_cnt) as visit_cnt, p.vetcreferer_id, p.vetcreferer_url
                from ".TBL_LOGSTORY_ETCREFERERINFO." p, ".TBL_LOGSTORY_BYETCREFERER." b
                where b.vdate between '$vdate' and '$vweekenddate'
                and p.vetcreferer_id = b.vetcreferer_id
                group by p.vetcreferer_id, p.vetcreferer_url
                order by visit_cnt desc
                LIMIT 0,150";
        */
        $sql = "select p.vetcreferer_url, b.* from ".TBL_LOGSTORY_ETCREFERERINFO." p, 
					(select sum(visit_cnt) as visit_cnt, sum(visitor_cnt) as visitor_cnt, vetcreferer_id from ".TBL_LOGSTORY_BYETCREFERER." b 
					where b.vdate between '$vdate' and '$vweekenddate' 
					group by vetcreferer_id 
					order by visit_cnt desc 					
					) b
					where p.vetcreferer_id = b.vetcreferer_id LIMIT 0,150 ";

        $dateString = "주간 : ". getNameOfWeekday(0,$vdate)."~".getNameOfWeekday(6,$vdate);
    }else if($SelectReport == 3){
        $sql = "Select sum(visit_cnt) as visit_cnt, sum(visitor_cnt) as visitor_cnt, p.vetcreferer_id, p.vetcreferer_url 
				from ".TBL_LOGSTORY_ETCREFERERINFO." p, ".TBL_LOGSTORY_BYETCREFERER." b 
				where b.vdate LIKE '".substr($vdate,0,6)."%' 
				and p.vetcreferer_id = b.vetcreferer_id 
				group by p.vetcreferer_id order by visit_cnt desc 
				LIMIT 0,150";
        $sql = "select p.vetcreferer_url, b.* from ".TBL_LOGSTORY_ETCREFERERINFO." p, 
					(select sum(visit_cnt) as visit_cnt, sum(visitor_cnt) as visitor_cnt, vetcreferer_id from ".TBL_LOGSTORY_BYETCREFERER." b 
					where b.vdate LIKE '".substr($vdate,0,6)."%' 
					group by vetcreferer_id 
					order by visit_cnt desc 					
					) b
					where p.vetcreferer_id = b.vetcreferer_id LIMIT 0,150 ";
        $dateString = getNameOfWeekday(0,$vdate,"monthname");
    }

    $fordb->query($sql);


    $mstring = $mstring.TitleBar("기타 URL",$dateString);
    $mstring = $mstring."<table cellpadding=0 cellspacing=0 width=100% border=0 >\n";
    //$mstring = $mstring."<tr  align=center><td colspan=3 style='padding-bottom:10px;'>".etcrefererGraph($vdate,$SelectReport)."</td></tr>\n";
    $mstring .= "<tr  align=center><td colspan=3 style='padding:30px 0px;text-align:center;'><div id='piechart' style='width: 70%; height: 300px; padding: 0px; position: relative;'></div></td></tr>\n";
    $mstring = $mstring."<tr  align=center><td colspan=3 ></td></tr>\n";
    $mstring = $mstring."</table>";
    /*
        $mstring = $mstring."<table cellpadding=3 cellspacing=0 width=100%  >\n";
        $mstring = $mstring."<tr height=25 align=center><td width=150 class=s_td width=30>항목</td><td class=m_td width=200>페이지 명</td><td class=e_td width=50>방문횟수</td></tr>\n";
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
    $mstring = $mstring."<tr height=25 align=center><td width=5% class=s_td>순</td>
			<td class=m_td width=*>페이지 명</td>
			<td class=e_td width=10% nowrap>방문횟수</td>
			<td class=e_td width=10% nowrap>점유율(방문횟수)</td>
			<td class=e_td width=10% nowrap>방문자수</td>
			<td class=e_td width=10% nowrap>점유율(방문자수)</td>
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
        $fordb->fetch($i);
        $pre_visit_cnt_sum = $pre_visit_cnt_sum + $fordb->dt['visit_cnt'];
        $pre_visitor_cnt_sum = $pre_visitor_cnt_sum + $fordb->dt['visitor_cnt'];

        if($i < 10){
            $chart_data[] = array(
                'label' => urldecode($fordb->dt['vetcreferer_url']),
                'data' => array(0=>array("1",$fordb->dt['visit_cnt'])) ,
                'color' => $color[$i]
            );
        }

    }

    for($i=0;$i<$fordb->total;$i++){
        $fordb->fetch($i);
        $mstring = $mstring."<tr height=40 bgcolor=#ffffff  id='Report$i'>
		<td bgcolor=#efefef align=center onmouseover=\"mouseOnTD('".$i."',true)\" onmouseout=\"mouseOnTD('$i',false)\">".($i+1)."</td>
		<td class='point' align=left onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" width=390 title='".urldecode($fordb->dt['vetcreferer_url'])."' style='padding:10px;word-break:break-all ' wrap>
			<a href='http://".$fordb->dt['vetcreferer_url']."' target=_blank>".$fordb->dt['vetcreferer_url']."</a>
		</td>
		<td bgcolor=#ffffff align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".returnZeroValue($fordb->dt['visit_cnt'])."&nbsp;</td>
		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format($fordb->dt['visit_cnt']/$pre_visit_cnt_sum*100,1)."%&nbsp;</td>
		<td bgcolor=#ffffff align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\">".returnZeroValue($fordb->dt['visitor_cnt'])."&nbsp;</td>
		<td bgcolor=#efefef align=right onmouseover=\"mouseOnTD('$i',true)\" onmouseout=\"mouseOnTD('$i',false)\" style='padding-right:20px;'>".number_format($fordb->dt['visitor_cnt']/$pre_visitor_cnt_sum*100,1)."%&nbsp;</td>
		</tr>\n";

        $visit_cnt_sum = $visit_cnt_sum + returnZeroValue($fordb->dt['visit_cnt']);
        $visitor_cnt_sum = $visitor_cnt_sum + returnZeroValue($fordb->dt['visitor_cnt']);


    }
    //$mstring = $mstring."</table>\n";
    //$mstring = $mstring."<table cellpadding=3 cellspacing=0 width=100%  >\n";
    if ($visit_cnt_sum == 0){
        $mstring = $mstring."<tr height=50 bgcolor=#ffffff align=center><td colspan=6>결과값이 없습니다.</td></tr>\n";
    }

    $mstring = $mstring."<tr height=25 align=center>
		<td class=s_td width=30 colspan=2>합계</td>
		<td class=e_td >".$visit_cnt_sum."</td>
		<td class=e_td >100%</td>
		<td class=e_td >".$visitor_cnt_sum."</td>
		<td class=e_td >100%</td>
		</tr>\n";
    $mstring = $mstring."</table>\n";
    /*
        $help_text = "
        <table>
            <tr>
                <td style='line-height:140%'>
                - 쇼핑몰을 방문하는 고객의 유입경로에대한 분석입니다. <br>
                - 이는 검색엔진 및 기타 알려진 유입경로 이외에 경로에 대한 분석 리포트 입니다. <br>
                - 기타 방문 URL 은 알려지지 않은 곳으로부터의 사이트의 링크정보를 알수 있어 또다른 온라인 프로모션을 진행하실수 있습니다 <br>
                - 좌측에 카테고리를 클릭하시면 해당분류에 대한 상세리포트를 확인하실수 있습니다
                </td>
            </tr>
        </table>
        ";*/

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

    $help_text = getTransDiscription(md5($_SERVER["PHP_SELF"]),'A' );


    $mstring .= HelpBox("기타 방문 URL", $help_text);
    return $mstring;
}


if ($mode == "iframe"){
    echo "<form name=tablefrm><textarea name=reportvalue>".ReportTable($vdate,$SelectReport)."</textarea></form>";
    echo "<Script>parent.document.getElementById('contents_frame').innerHTML = document.tablefrm.reportvalue.value</Script>";


    $ca = new Calendar();
    $ca->LinkPage = 'etcreferer.php';

    echo "<form name=calendarfrm><textarea name=calendarvalue>".$ca->getMonthView(substr($vdate,4,2), substr($vdate,0,4))."</textarea></form>";
    echo "<Script>parent.document.getElementById('calendararea').innerHTML = document.calendarfrm.calendarvalue.value;parent.ChangeCalenderView($SelectReport);</Script>";

}else{
    $p = new forbizReportPage();

    $p->Navigation = "로그분석 > 유입사이트 분석 > 기타 URL";
    $p->title = "기타 URL";
    $p->forbizLeftMenu = Stat_munu('etcreferer.php');
    $p->forbizContents = ReportTable($vdate,$SelectReport);
    $p->PrintReportPage();
}
?>
